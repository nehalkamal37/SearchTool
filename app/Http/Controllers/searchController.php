<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use App\Models\Script;
use Illuminate\Http\Request;

class searchController extends Controller
{
    public function index(Request $r){

        $drugname=Drug::get();
        
        foreach($drugname as $d){

         $name=($d->drug_name);
        
          $ins=Script::where('Drug_Name', $name)->get();
          $ndc=Script::select('NDC')->where('Drug_Name',$name)->get();
      //  dd($ndc);
         //    foreach($ins as $i){

         return view('search',compact('drugname','ins','ndc'));

        }
          


        




    }
 

    
    public function getDependentData(Request $request)
    {
        $drugName = $request->input('drug_name');
    
        // Fetch related data for the second and third dropdowns
        $ins = Script::where('Drug_Name', $drugName)->pluck('Ins', 'id'); // Adjust columns as needed
        $ndc = Script::where('Drug_Name', $drugName)->pluck('NDC', 'id'); // Adjust columns as needed
    
        return response()->json([
            'ins' => $ins,
            'ndc' => $ndc,
        ]);
    }
    

    public function searchStabeOne(Request $request)
    {

        $normalizedNDC = str_replace('-', '', $request->ndc);

      // getting data for our choosen drug from select options with latest date
$data= Script::where('Drug_Name', $request->drug_name)
->where('Ins', $request->insurance)
->where('NDC', $normalizedNDC)
->distinct()
->get()
->groupBy(function ($item) {
      return $item['Drug_Name'] . '-' . $item['Ins'] . '-' . $item['NDC'];
  })
  ->map(function ($group) {
      return $group->sortBy('Date')->first();
  }); 

      //  getting class from script DB if not there then get it from drugs DB
      $class = Script::where('Drug_Name', $request->drug_name)
              ->where('Ins', $request->insurance)
           //  ->where('NDC', str_replace('-', '', $request->ndc))
              // ->where('NDC', $request->ndc)
              ->distinct()
              ->pluck('Class')
              ->first();
$classs=Drug::where('drug_name', $request->drug_name)
->where('ndc', $request->ndc)
->distinct()
->pluck('drug_class')
->first();
$class = trim($classs); // Remove any leading/trailing whitespace, including \r and \n

    //  dd($class);
     
    
      // getting alternatives depending on class with latest date
      $script=Script::where('Class',$class)
      ->where('Ins',$request->insurance)
      ->where('NDC','!=',$normalizedNDC)
      ->distinct()
      ->get()
      ->groupBy(function ($item) {
            return $item['Drug_Name'] . '-' . $item['Ins'] . '-' . $item['NDC'];
        })
        ->map(function ($group) {
            return $group->sortBy('Date')->first();
        });
    
           // if drug was not found in script DB then search for it in drugs DB  //orrrrr
          //if drug is not dound in script DB then search in DRugs data
      if(($script->isEmpty() ) || $data->isEmpty() ){
        $drug_data= Drug::where('drug_name', $request->drug_name)
        ->where('ndc', $request->ndc)
        ->distinct()
        ->get(); 

        $drugs=Drug::where('drug_name',$request->drug_name)->get();
      //  dd($drug_data);

        return view('drugResult', compact('data','drug_data','request','script','class','drugs'));

      }
     
      return view('drugResult', compact('data','drug_data','request','script','class','drugs'));

    }
//oldddddddd
   
 
    

public function searchold(Request $request)
{
    // Normalize the NDC input
    $normalizedNDC = str_replace('-', '', $request->ndc);

    // Step 1: Get data for the chosen drug from Scripts table with latest date
    $data = Script::where('Drug_Name', $request->drug_name)
        ->where('Ins', $request->insurance)
        ->where('NDC', $normalizedNDC) // Handle NDC normalization
        ->distinct()
        ->get()
        ->groupBy(function ($item) {
            return $item['Drug_Name'] . '-' . $item['Ins']; 
            //. '-' . $item['NDC'];
        })
        ->map(function ($group) {
            return $group->sortByDesc('Date')->first(); // Fetch the latest record
        });
  // dd($data);
    // Step 2: Determine the class from Scripts table or fallback to Drugs table
    $class = Script::where('Drug_Name', $request->drug_name)
        ->where('Ins', $request->insurance)
     //   ->whereRaw("REPLACE(NDC, '-', '') = ?", [$normalizedNDC]) // Handle NDC normalization
        ->distinct()
        ->pluck('Class')
        ->first();

    if (empty($class)) {
        // If class is not found in Scripts, check in Drugs table
        $class = Drug::where('drug_name', $request->drug_name)
            ->where('ndc', $request->ndc) // Use the hyphenated format
            ->distinct()
            ->pluck('drug_class')
            ->first();
    }

    // Step 3: Get alternatives (drugs sharing the same class) with latest date
    $script = Script::where('Class', $class)
        ->where('Drug_Name', $request->drug_name)
        ->where('Ins', '!=', $request->insurance)
        ->where('NDC','!=',$normalizedNDC)
        ->distinct()
        ->get()
        ->groupBy(function ($item) {
            return $item['Drug_Name'] . '-' . $item['Ins'] . '-' . $item['NDC'];
        })
        ->map(function ($group) {
            return $group->sortByDesc('Date')->first(); // Fetch the latest record
        });

    // Step 4: If no data found in Scripts, fallback to Drugs table
    if ($script->isEmpty() || $data->isEmpty()) {
        $drug_data = Drug::where('drug_name', $request->drug_name)
            ->where('ndc', $request->ndc) // Use the hyphenated format
            ->distinct()
            ->get();
            $script = Script::where('Class', $class)
            ->where('Ins', '!=', $request->insurance)
            ->distinct()
            ->get()
            ->groupBy(function ($item) {
                return $item['Drug_Name'] . '-' . $item['Ins'] . '-' . $item['NDC'];
            })
            ->map(function ($group) {
                return $group->sortByDesc('Date')->first(); // Fetch the latest record
            });
        $drugs = Drug::where('drug_name', $request->drug_name)->get();
        return view('drugResult', compact('data', 'drug_data', 'request', 'script', 'class', 'drugs'));
    }

    // Step 5: Return the results to the view
    return view('drugResult', compact('data', 'request', 'script', 'class'));
}


// filter displayed data awp+netprofit
public function searchDrug(Request $request)
{
    // Normalize inputs
    $normalizedDrugName = trim($request->input('drug_name'));
    $normalizedNDC = str_replace('-', '', trim($request->input('ndc')));
    $normalizedInsurance = trim($request->input('insurance'));

    // Validate inputs
  //  if (!$normalizedDrugName || !$normalizedNDC || !$normalizedInsurance) {
    //    return back()->withErrors('Missing required inputs for search.');
    //}
    $class = Script::where('Drug_Name', $request->drug_name)
              ->where('Ins', $request->insurance)
           //  ->where('NDC', str_replace('-', '', $request->ndc))
              // ->where('NDC', $request->ndc)
              ->distinct()
              ->pluck('Class')
              ->first();

$dclass=Drug::where('drug_name', $request->drug_name)
//->where('ndc', $request->ndc)
->distinct()
->pluck('drug_class')
->first();
//dd($dclass);
//$class = trim($classs);

    // Base query for Scripts
    $scriptQuery = Script::where('Class', $class)
      //  ->where('NDC',$normalizedNDC)
        ->where('Ins', $normalizedInsurance)
        ->distinct()
        ->get()
->groupBy(function ($item) {
      return $item['Drug_Name'] . '-' . $item['Ins'] . '-' . $item['NDC'];
  })
  ->map(function ($group) {
      return $group->sortByDesc('Date')->first();
  }); 


       // ->where('Ins', $normalizedInsurance);

    // Apply sorting
    if ($request->has('sort_by')) {
        $sortBy = $request->input('sort_by');
        if ($sortBy === 'net_profit_desc') {
            $scriptQuery = $scriptQuery->sortByDesc(function ($item) {
                return $item->Net_Profit;
            });
       // if ($sortBy === 'net_profit_desc') {
         //   $scriptQuery->orderBy('Net_Profit', 'desc');
        } elseif ($sortBy === 'awp_asc') {

            $scriptQuery = Drug::where('drug_class', $dclass)
            ->orWhere('ndc', $request->ndc);
            $scriptQuery= $scriptQuery->orderBy('awp', 'asc')->distinct()->get();
             //dd($scriptQuery);
        
        }
    }
    // Execute the query
    //$scriptData = $scriptQuery->get();

    // Return the results to the view
    return view('filteredData', [
        'scriptData'=>$scriptQuery,
        //'scriptData' => $scriptData,
        'normalizedDrugName' => $normalizedDrugName,
        'normalizedNDC' => $normalizedNDC,
        'normalizedInsurance' => $normalizedInsurance,
        'sortBy' => $request->input('sort_by'),
    ]);
}

public function searchbef(Request $request)
{

    $normalizedNDC = str_replace('-', '', $request->ndc);

  // getting data for our choosen drug from select options with latest date
$data= Script::where('Drug_Name', $request->drug_name)
->where('Ins', $request->insurance)
->where('NDC', $normalizedNDC)
->distinct()
->get()
->groupBy(function ($item) {
  return $item['Drug_Name'] . '-' . $item['Ins'] . '-' . $item['NDC'];
})
->map(function ($group) {
  return $group->sortBy('Date')->first();
}); 

  //  getting class from script DB if not there then get it from drugs DB
  $class = Script::where('Drug_Name', $request->drug_name)
          ->where('Ins', $request->insurance)
       //  ->where('NDC', str_replace('-', '', $request->ndc))
          // ->where('NDC', $request->ndc)
          ->distinct()
          ->pluck('Class')
          ->first();
$classs=Drug::where('drug_name', $request->drug_name)
->where('ndc', $request->ndc)
->distinct()
->pluck('drug_class')
->first();
$class = trim($classs); // Remove any leading/trailing whitespace, including \r and \n

//  dd($class);
 

  // getting alternatives depending on class with latest date
  $script=Script::where('Class',$class)
  ->where('Ins',$request->insurance)
  ->where('NDC','!=',$normalizedNDC)
  ->distinct()
  ->get()
  ->groupBy(function ($item) {
        return $item['Drug_Name'] . '-' . $item['Ins'] . '-' . $item['NDC'];
    })
    ->map(function ($group) {
        return $group->sortBy('Date')->first();
    });

       // if drug was not found in script DB then search for it in drugs DB  //orrrrr
      //if drug is not dound in script DB then search in DRugs data
  if(($script->isEmpty() ) || $data->isEmpty() ){
    $drug_data= Drug::where('drug_name', $request->drug_name)
    ->where('ndc', $request->ndc)
    ->distinct()
    ->get(); 

    $drugs=Drug::where('drug_name',$request->drug_name)->get();
  //  dd($drug_data);

    return view('drugResult', compact('data','drug_data','request','script','class','drugs'));

  }
 
  return view('drugResult', compact('data','drug_data','request','script','class','drugs'));

}
//oldddddddd

/*
public function search(Request $request)
{
    $normalizedNDC = str_replace('-', '', $request->ndc);

    // Step 1: Fetch the selected drug data from the `scripts` table (latest date)
    $selectedDrugData = Script::where('Drug_Name', $request->drug_name)
        ->where('Ins', $request->insurance)
        ->where('NDC', $normalizedNDC)
        ->distinct()
        ->get()
        ->groupBy(function ($item) {
            return $item['Drug_Name'] . '-' . $item['Ins'] . '-' . $item['NDC'];
        })
        ->map(function ($group) {
            return $group->sortByDesc('Date')->first();
        });

    // Step 2: Retrieve the Class for the drug (from `scripts` or fallback to `drugs`)
    $class = Script::where('Drug_Name', $request->drug_name)
        ->where('Ins', $request->insurance)
        ->pluck('Class')
        ->first();

    if (!$class) {
        $class = Drug::where('drug_name', $request->drug_name)
            ->where('ndc', $normalizedNDC)
            ->pluck('drug_class')
            ->first();
    }

    $class = trim($class); // Clean up whitespace

    // Step 3: Fetch alternatives from the `scripts` table
    $alternativesFromScripts = Script::where('Class', $class)
        ->where('Ins', $request->insurance)
        ->where('NDC', '!=', $normalizedNDC)
        ->distinct()
        ->get()
        ->groupBy(function ($item) {
            return $item['Drug_Name'] . '-' . $item['Ins'] . '-' . $item['NDC'];
        })
        ->map(function ($group) {
            return $group->sortByDesc('Date')->first();
        });

    // Step 4: Fallback to fetching alternatives from the `drugs` table if no scripts found
    $alternativesFromDrugs = collect();
  //  if ($alternativesFromScripts->isEmpty()) {
        $alternativesFromDrugs = Drug::where('drug_class', $class)
            ->distinct()
            ->get();
    //}

    // Step 5: Fetch data for the requested drug from `drugs` table
    $drugData = Drug::where('drug_name', $request->drug_name)
        ->where('ndc', $normalizedNDC)
        ->distinct()
        ->get();

    // Step 6: Return all results to the view
    return view('drugResult', [
        'data' => $selectedDrugData,
        'drug_data' => $drugData,
        'request' => $request,
        'script' => $alternativesFromScripts,
        'class' => $class,
        'drugs' => $alternativesFromDrugs,
    ]);
}
*/


/* very good
public function search(Request $request)
{
    $normalizedNDC = $request->ndc ? str_replace('-', '', $request->ndc) : null;
    //dd($normalizedNDC);
    // إذا لم يتم إدخال NDC
    if (!$normalizedNDC)
     {
        // Step 1: Fetch all data from `scripts` based on `Drug_Name` and `Insurance`
        $dataFromScripts = Script::where('Drug_Name', $request->drug_name)
            ->where('Ins', $request->insurance)
            ->distinct()
            ->get()
            ->groupBy(function ($item) {
                return $item['Drug_Name'] . '-' . $item['Ins'] . '-' . $item['NDC'];
            })
            ->map(function ($group) {
                return $group->sortByDesc(function ($item) {
                    return strtotime($item['Date']); // Convert the Date to a timestamp for reliable comparison
                })->first();
            });
        // Step 2: Fetch all data from `drugs` based on `Drug_Name`
        $dataFromDrugs = Drug::where('drug_name', $request->drug_name)
            ->distinct()
            ->get();

            //alts
            $class = Script::where('Drug_Name', $request->drug_name)
            ->where('Ins', $request->insurance)
            ->pluck('Class')
            ->first();
    
        $class = trim($class);
    
        $class2 = Drug::where('drug_name', $request->drug_name)
           // ->where('Ins', $request->insurance)
            ->pluck('drug_class')
            ->first();
    
        $alternativesFromScripts = Script::where('Class', $class)
            ->where('Ins', $request->insurance)
          //  ->where('NDC', '!=', $normalizedNDC)
            ->distinct()
            ->get()
            ->groupBy(function ($item) {
                return $item['Drug_Name'] . '-' . $item['Ins'];
            })
            ->map(function ($group) {
                return $group->sortByDesc('Date')->first();
            });
    
        $alternativesFromDrugs = collect();
       // if ($alternativesFromScripts->isEmpty()) {
            $alternativesFromDrugs = Drug::where('drug_class', $class2)
                ->distinct()
                ->get();
               // dd($cl);
    
        //}
    
        $drugData = Drug::where('drug_name', $request->drug_name)
           // ->where('ndc', $normalizedNDC)
            ->distinct()
            ->get();
        // Step 3: Return the data to the view
          return view('drugResult', [
            'data' => $dataFromScripts,
           // 'drug_data' => $dataFromDrugs,
            'request' => $request,
            'script' => $alternativesFromScripts,
            'class' => $class,
            'drugs' => $alternativesFromDrugs,
        ]);
    }
    // إذا تم إدخال NDC
    $selectedDrugData = Script::where('Drug_Name', $request->drug_name)
         ->where('NDC', $normalizedNDC)   
        ->where('Ins', $request->insurance)
        ->distinct()
        ->get()
        ->groupBy(function ($item) {
            return $item['Drug_Name'] . '-' . $item['Ins'] . '-' . $item['NDC'];
        })
        ->map(function ($group) {
            // Sort by Date in descending order (newest first) and get the first record
            return $group->sortByDesc(function ($item) {
                return strtotime($item['Date']); // Convert the Date to a timestamp for reliable comparison
            })->first();
        });

        $data=Script::where('Drug_Name', $request->drug_name)
        ->where('Ins', $request->insurance)
        ->where('NDC', 'LIKE', '%' . $normalizedNDC . '%')
        // ->distinct()
         ->get();
 // dd($data);


    $class = Script::where('Drug_Name', $request->drug_name)
        ->where('Ins', $request->insurance)
        ->pluck('Class')
        ->first();

    if (!$class) {
        $class = Drug::where('drug_name', $request->drug_name)
            ->where('ndc', $normalizedNDC)
            ->pluck('drug_class')
            ->first();
    }

    $class = trim($class);

    $class2 = Drug::where('drug_name', $request->drug_name)
       // ->where('Ins', $request->insurance)
        ->pluck('drug_class')
        ->first();

    $alternativesFromScripts = Script::where('Class', $class)
        ->where('Ins', $request->insurance)
        ->where('NDC', '!=', $normalizedNDC)
        ->distinct()
        ->get()
        ->groupBy(function ($item) {
            return $item['Drug_Name'] . '-' . $item['Ins'] . '-' . $item['NDC'];
        })
        ->map(function ($group) {
            return $group->sortByDesc('Date')->first();
        });

    $alternativesFromDrugs = collect();
   // if ($alternativesFromScripts->isEmpty()) {
        $alternativesFromDrugs = Drug::where('drug_class', $class2)
            ->distinct()
            ->get();
           // dd($cl);

    //}

    $drugData = Drug::where('drug_name', $request->drug_name)
        ->orwhere('ndc', $normalizedNDC)
        ->distinct()
        ->get();

    return view('drugResult', [
        'data' => $selectedDrugData,
        'drug_data' => $drugData,
        'request' => $request,
        'script' => $alternativesFromScripts,
        'class' => $class,
        'drugs' => $alternativesFromDrugs,
       // 'ndc'=>$normalizedNDC ?? 'ndc'=>''
    ]);
}
*/

public function search(Request $request)
{
    $normalizedNDC = $request->ndc ? str_replace('-', '', $request->ndc) : null;

    // If no NDC and no insurance is provided
    if (!$normalizedNDC && !$request->insurance) {
        // Fetch all data related to the drug name from `drugs` table
        $drugData = Drug::where('drug_name', $request->drug_name)
            ->distinct()
            ->get();
            $dataFromDrugs = Drug::where('drug_name', $request->drug_name)
            ->distinct()
            ->get();

        $class2 = Drug::where('drug_name', $request->drug_name)
            ->pluck('drug_class')
            ->first();


        $alternativesFromDrugs = Drug::where('drug_class', $class2)
            ->distinct()
            ->get();


        return view('drugResult', [
            'data' => collect(),
            'drug_data' => $drugData,
            'request' => $request,
            'script' => collect(),
            'class' => $class2,
            'drugs' => (trim($class2) == 'Other' || trim($class2) == 'Other NDC') ? collect() : $alternativesFromDrugs,

           // 'drugs' => $alternativesFromDrugs,
        ]);
    }

    // If NDC is not provided
    if (!$normalizedNDC) {
        // Step 1: Fetch all data from `scripts` based on `Drug_Name` and `Insurance`
        $dataFromScripts = Script::where('Drug_Name', $request->drug_name)
            ->where('Ins', $request->insurance)
            ->distinct()
            ->get()
            ->groupBy(function ($item) {
                return $item['Drug_Name'] . '-' . $item['Ins'] . '-' . $item['NDC'];
            })
            ->map(function ($group) {
                return $group->sortByDesc(function ($item) {
                    return strtotime($item['Date']);
                })->first();
            });

        $dataFromDrugs = Drug::where('drug_name', $request->drug_name)
            ->distinct()
            ->get();

        $class = Script::where('Drug_Name', $request->drug_name)
            ->where('Ins', $request->insurance)
            ->pluck('Class')
            ->first();

        $class = trim($class);

        $class2 = Drug::where('drug_name', $request->drug_name)
            ->pluck('drug_class')
            ->first();

        $alternativesFromScripts = Script::where('Class', $class)
            ->where('Ins', $request->insurance)
            ->distinct()
            ->get()
            ->groupBy(function ($item) {
                return $item['Drug_Name'] . '-' . $item['Ins'];
            })
            ->map(function ($group) {
                return $group->sortByDesc('Date')->first();
            });

        $alternativesFromDrugs = Drug::where('drug_class', $class2)
            ->distinct()
            ->get();
           
            return view('drugResult', [
            'data' => $dataFromScripts,
            'request' => $request,
            'script' => (trim($class) == 'Other' || trim($class) == 'Other NDC') ? collect() : $alternativesFromScripts,
            'class' => $class,
            'drugs' => (trim($class2) == 'Other' || trim($class2) == 'Other NDC' || trim($class) == 'Other') ? collect() : $alternativesFromDrugs,
        ]);
    }

    // If NDC is provided
    $selectedDrugData = Script::where('Drug_Name', $request->drug_name)
        ->where('NDC', $normalizedNDC)
        ->where('Ins', $request->insurance)
        ->distinct()
        ->get()
        ->groupBy(function ($item) {
            return $item['Drug_Name'] . '-' . $item['Ins'] . '-' . $item['NDC'];
        })
        ->map(function ($group) {
            return $group->sortByDesc(function ($item) {
                return strtotime($item['Date']);
            })->first();
        });

    $class = Script::where('Drug_Name', $request->drug_name)
        ->where('Ins', $request->insurance)
        ->pluck('Class')
        ->first();

    if (!$class) {
        $class = Drug::where('drug_name', $request->drug_name)
            ->where('ndc', $normalizedNDC)
            ->pluck('drug_class')
            ->first();
    }

    $class = trim($class);

    $class2 = Drug::where('drug_name', $request->drug_name)
        ->pluck('drug_class')
        ->first();

    $alternativesFromScripts = Script::where('Class', $class)
        ->where('Ins', $request->insurance)
        ->where('NDC', '!=', $normalizedNDC)
        ->distinct()
        ->get()
        ->groupBy(function ($item) {
            return $item['Drug_Name'] . '-' . $item['Ins'] . '-' . $item['NDC'];
        })
        ->map(function ($group) {
            return $group->sortByDesc('Date')->first();
        });

    $alternativesFromDrugs = Drug::where('drug_class', $class2)
        ->distinct()
        ->get();

    $drugData = Drug::where('drug_name', $request->drug_name)
        ->orWhere('ndc', $normalizedNDC)
        ->distinct()
        ->get();

    return view('drugResult', [
        'data' => $selectedDrugData,
        'drug_data' => $drugData,
        'request' => $request,
        'script' => (trim($class) == 'Other' || trim($class) == 'Other NDC') ? collect() : $alternativesFromScripts,
        'class' => $class,
        'drugs' => (trim($class2) == 'Other' || trim($class2) == 'Other NDC' || trim($class) == 'Other' ) ? collect() : $alternativesFromDrugs,
    ]);
}




}
