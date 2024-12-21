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
    

    public function search(Request $request){

      // getting data for our choosen drug from select options with latest date
$data= Script::where('Drug_Name', $request->drug_name)
->where('Ins', $request->insurance)
->where('NDC', $request->ndc)
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
              ->where('NDC', $request->ndc)
              ->distinct()
              ->pluck('Class')
              ->first();

      
      if(empty($class)){
        $class=Drug::where('drug_name',$request->drug_name)
        ->where('ndc', $request->ndc)
        ->distinct()
        ->pluck('drug_class')
        ->first();

      }
    
      // getting alternatives depending on class with latest date
      $script=Script::where('Class',$class)
      ->where('Ins','!=',$request->insurance)
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
}
