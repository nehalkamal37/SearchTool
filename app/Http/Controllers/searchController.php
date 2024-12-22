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
        // dd($ndc);
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
    

    public function search(Request $request)
    {

        $normalizedNDC = str_replace('-', '', $request->ndc);

      // getting data for our choosen drug from select options with latest date
$data= Script::where('Drug_Name', $request->drug_name)
->where('Ins', $request->insurance)
->where('NDC', $normalizedNDC)

//->where('NDC', $request->ndc)
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
      ->where('Ins','!=',$request->insurance)
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

      if(empty($script ) || (empty($data))){
        $drug_data= Drug::where('drug_name', $request->drug_name)
        ->where('ndc', $request->ndc)
        ->distinct()
        ->get(); 

        $drugs=Drug::where('drug_name',$request->drug_name)->get();
        dd($drugs);
        return view('drugResult', compact('data','drug_data','request','script','class','drugs'));

      }
  
      return view('drugResult', compact('data','request','script','class'));

    }

    public function search1(Request $request)
    {
        // Normalize the input NDC by removing hyphens for comparison with scripts table
        $normalizedNDC = str_replace('-', '', $request->ndc);
        //dd($normalizedNDC);
        // Step 1: Get data for the chosen drug from scripts table
        $data = Script::where('Drug_Name', $request->drug_name)
            ->where('Ins', $request->insurance)
            ->whereRaw("REPLACE(NDC, '-', '') = ?", [$normalizedNDC]) // Normalize NDC in query
            ->distinct()
            ->get()
            ->groupBy(function ($item) {
                return $item['Drug_Name'] . '-' . $item['Ins'] . '-' . $item['NDC'];
            })
            ->map(function ($group) {
                return $group->sortByDesc('Date')->first(); // Get the latest date
            });

              $data=Drug::where('drug_name', $request->drug_name)
             ->where('ndc', $request->ndc)->get();

    // Step 2: Get the class from scripts table or fallback to drugs table
        $class = Script::where('Drug_Name', $request->drug_name)
            ->where('Ins', $request->insurance)
            ->whereRaw("REPLACE(NDC, '-', '') = ?", [$normalizedNDC]) // Normalize NDC in query
            ->distinct()
            ->pluck('Class')
            ->first();

             $class=Drug::where('drug_name', $request->drug_name)
            ->where('ndc', $request->ndc) // Use exact match for hyphenated format
            ->distinct()
            ->pluck('drug_class')
            ->first(); 
        // Step 3: Get alternatives depending on class with the latest date
        $script = Script::where('Class', $class)
            ->where('Ins', '!=', $request->insurance)
            ->where('NDC', '!=', $normalizedNDC)
            ->distinct()
            ->get()
            ->groupBy(function ($item) {
                return $item['Drug_Name'] . '-' . $item['Ins'] . '-' . $item['NDC'];
            })
            ->map(function ($group) {
                return $group->sortByDesc('Date')->first(); // Get the latest date
            });

            dd($script);

        // Step 4: If no data is found in scripts, fallback to drugs table
      /*  if ($script->isEmpty() || $data->isEmpty())
         {
            $drug_data = Drug::where('drug_name', $request->drug_name)
                ->where('ndc', $request->ndc) // Use exact match for hyphenated format
                ->distinct()
                ->get();
    
            $drugs = Drug::where('drug_name', $request->drug_name)->get();
    
            return view('drugResult', compact('data', 'drug_data', 'request', 'script', 'class', 'drugs'));
        }
    */
        // Step 5: Return the results
        return view('drugResult', compact('data', 'request', 'script', 'class'));
    }
 
    

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
public function search22(Request $request)
{
    // Normalize the NDC input
    $normalizedNDC = str_replace('-', '', $request->ndc);

    // Step 1: Get data for the chosen drug from Scripts table with latest date
   // dd($normalizedNDC);
    $data = Script::where('Drug_Name', $request->drug_name)
        ->where('Ins', $request->insurance)
        ->where('NDC', $normalizedNDC) // Handle NDC normalization
        ->distinct()
        ->get()
        ->groupBy(function ($item) {
            return $item['Drug_Name'] . '-' . $item['Ins']. '-' . $item['NDC']; 
            //. '-' . $item['NDC'];
        })
        ->map(function ($group) {
            return $group->sortByDesc('Date')->first(); // Fetch the latest record
        });
   dd($data);
    // Step 2: Determine the class from Scripts table or fallback to Drugs table
    $class = Drug::where('drug_name', $request->drug_name)
        ->where('ndc', $request->ndc)
     //   ->whereRaw("REPLACE(NDC, '-', '') = ?", [$normalizedNDC]) // Handle NDC normalization
        ->distinct()
        ->pluck('drug_class')
        ->first();

    if (empty($class)) {
        // If class is not found in Scripts, check in Drugs table
        $class = Script::where('Drug_Name', $request->drug_name)
            ->where('NDC', $normalizedNDC) // Use the hyphenated format
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
  /*  if ($script->isEmpty() || $data->isEmpty()) {
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
        */

    // Step 5: Return the results to the view
    return view('drugResult', compact('data', 'request', 'script', 'class'));
}



public function search3(Request $request)
{
    // Normalize the NDC input
    $normalizedNDC = str_replace('-', '', $request->ndc);

    // Step 1: Get data for the chosen drug from the Scripts table with the latest date
    $data = Script::where('Drug_Name', $request->drug_name)
        ->where('Ins', $request->insurance)
        ->where('NDC', $normalizedNDC) // Handle NDC normalization
        ->distinct()
        ->get()
        ->groupBy(function ($item) {
            return $item['Drug_Name'] . '-' . $item['Ins'] . '-' . $item['NDC'];
        })
        ->map(function ($group) {
            return $group->sortByDesc('Date')->first(); // Fetch the latest record
        });

    // Step 2: Determine the class from Scripts table or fallback to Drugs table
    $class = Script::where('Drug_Name', $request->drug_name)
        ->where('NDC', $normalizedNDC)
        ->distinct()
        ->pluck('Class')
        ->first();

    if (empty($class)) {
        // If class is not found in Scripts, check in Drugs table
        $class = Drug::where('drug_name', $request->drug_name)
            ->whereRaw("REPLACE(ndc, '-', '') = ?", [$normalizedNDC]) // Normalize NDC
            ->distinct()
            ->pluck('drug_class')
            ->first();
    }

    // Step 3: Get alternatives (drugs sharing the same class) with the latest date
    $script = Script::where('Class', $class)
        //->where('Drug_Name', $request->drug_name)
       // ->where('Ins', '!=', $request->insurance)
        ->where('NDC', '!=', $normalizedNDC)
        ->distinct()
        ->get()
        ->groupBy(function ($item) {
            return $item['Drug_Name'] . '-' . $item['Ins'] . '-' . $item['NDC'];
        })
        ->map(function ($group) {
            return $group->sortByDesc('Date')->first(); // Fetch the latest record
        });
      //  dd($script);

    // Step 4: Fallback to Drugs table if no data is found in Scripts
    if ($script->isEmpty() || $data->isEmpty()) {
        $drug_data = Drug::where('drug_name', $request->drug_name)
            ->where('ndc', $request->ndc) // Normalize NDC
            ->distinct()
            ->get();

        $drugs = Drug::where('drug_name', $request->drug_name)
        ->where('ndc', $request->ndc)
        ->get();

        return view('drugResult', compact('data', 'drug_data', 'request', 'script', 'class', 'drugs'));
    }

    // Step 5: Return the results to the view
    return view('drugResult', compact('data', 'request', 'script', 'class'));
}
public function searchjson(Request $request)
{
    // Normalize inputs
    $drugName = trim($request->drug_name); // اسم الدواء
    $insurance = trim($request->insurance); // التأمين
    $normalizedNDC = str_replace('-', '', trim($request->ndc)); // NDC بعد إزالة العلامات

    // البحث في جدول Scripts
    $scriptsData = Script::where('Drug_Name', $drugName)
        ->where('Ins', $insurance)
        ->where('NDC', $normalizedNDC)
        ->orderByDesc('Date') // ترتيب حسب التاريخ الأحدث
        ->get();

    if ($scriptsData->isNotEmpty()) {
        // إذا وجد تطابق، قم بتجميع السجلات حسب اسم الدواء والتأمين وأخذ السجل الأحدث
        $latestScriptsData = $scriptsData->groupBy(function ($item) {
            return $item['Drug_Name'] . '-' . $item['Ins'];
        })->map(function ($group) {
            return $group->sortByDesc('Date')->first();
        });

        return response()->json([
            'status' => 'found',
            'source' => 'scripts',
            'data' => $latestScriptsData->values()
        ]);
    }

    // البحث في جدول Drugs إذا لم يوجد تطابق في جدول Scripts
    $drugsData = Drug::where('drug_name', $drugName)
        ->where('ndc', $normalizedNDC)
        ->first();

    if ($drugsData) {
        return response()->json([
            'status' => 'found',
            'source' => 'drugs',
            'data' => $drugsData
        ]);
    }

    // إذا لم يوجد أي تطابق في كلا الجدولين
    return response()->json([
        'status' => 'not_found',
        'message' => 'No matching records found in Scripts or Drugs.'
    ]);
}

}
