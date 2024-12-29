<?php

namespace App\Http\Controllers\dashboard;

use App\Models\Drug;
use App\Models\Script;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class scriptsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
             
            $insuranceMapping = [
                'AL' => 'Aetna (AL)',
                'BW' => 'Aetna (BW)',
                'AD' => 'Aetna Medicare (AD)',
                'AF' => 'Anthem BCBS (AF)',
                'DS' => 'Blue Cross Blue Shield (DS)',
                'CA' => 'Blue Shield Medicare (CA)',
                'FQ' => 'Capital Rx (FQ)',
                'BF' => 'Caremark (BF)',
                'ED' => 'CatalystRx (ED)',
                'AM' => 'Cigna (AM)',
                'BO' => 'Default Claim Format (BO)',
                'AP' => 'Envision Rx Options (AP)',
                'CG' => 'Express Scripts (CG)',
                'BI' => 'Horizon (BI)',
                'AJ' => 'Humana Medicare (AJ)',
                'BP' => 'informedRx (BP)',
                'AO' => 'MEDCO HEALTH (AO)',
                'AC' => 'MEDCO MEDICARE PART D (AC)',
                'AQ' => 'MEDGR (AQ)',
                'CC' => 'MY HEALTH LA (CC)',
                'AG' => 'Navitus Health Solutions (AG)',
                'AH' => 'OptumRx (AH)',
                'AS' => 'PACIFICARE LIFE AND H (AS)',
                'FJ' => 'Paramount Rx (FJ)',
                'X ' => 'PF - DEFAULT (X )',
                'EA' => 'Pharmacy Data Management (EA)',
                'DW' => 'PHCS (DW)',
                'AX' => 'PINNACLE (AX)',
                'BN' => 'Prescription Solutions (BN)',
                'AA' => 'Tri-Care Express Scripts (AA)',
                'AI' => 'United Healthcare (AI)',
            ];
            $query = Script::query();

          // If a search term is provided, filter the results
          if ($request->has('search') && $request->search != '') {
            $query->where('Drug_Name', 'like', '%' . $request->search . '%');

          
      //   return view('dashboard.scriptsTable',compact('scripts'));

        }  
  
        $scripts = $query->orderBy('Date', 'desc')->paginate(7);
        // Example: Assuming each script has an `insurance_code` field
        $scripts->getCollection()->transform(function ($script) use ($insuranceMapping) {
            $script->insurance_name = $insuranceMapping[$script->Ins] ?? $script->Ins;
            return $script;
        });        
     
        //if ($scripts->isEmpty()) {
          //  return view('dashboard.drugstable', compact('drugs','scripts','insuranceMapping'))->with('message', 'No scripts found.');
        //}
    /*    $scripts->transform(function ($script) {
            $script->formatted_date = \Carbon\Carbon::parse($script->Date)->format('m/d/Y');
            return $script;

        });  */
        $scripts->getCollection()->transform(function ($script) {
            $script->formatted_date = \Carbon\Carbon::parse($script->Date)->format('m/d/Y');
            return $script;
        });
        
      //  $scripts=Script::paginate(7);
     
    
        return view('dashboard.scriptsTable',compact('scripts'));
    
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
            $insuranceMapping = [
                'AL' => 'Aetna (AL)',
                'BW' => 'Aetna (BW)',
                'AD' => 'Aetna Medicare (AD)',
                'AF' => 'Anthem BCBS (AF)',
                'DS' => 'Blue Cross Blue Shield (DS)',
                'CA' => 'Blue Shield Medicare (CA)',
                'FQ' => 'Capital Rx (FQ)',
                'BF' => 'Caremark (BF)',
                'ED' => 'CatalystRx (ED)',
                'AM' => 'Cigna (AM)',
                'BO' => 'Default Claim Format (BO)',
                'AP' => 'Envision Rx Options (AP)',
                'CG' => 'Express Scripts (CG)',
                'BI' => 'Horizon (BI)',
                'AJ' => 'Humana Medicare (AJ)',
                'BP' => 'informedRx (BP)',
                'AO' => 'MEDCO HEALTH (AO)',
                'AC' => 'MEDCO MEDICARE PART D (AC)',
                'AQ' => 'MEDGR (AQ)',
                'CC' => 'MY HEALTH LA (CC)',
                'AG' => 'Navitus Health Solutions (AG)',
                'AH' => 'OptumRx (AH)',
                'AS' => 'PACIFICARE LIFE AND H (AS)',
                'FJ' => 'Paramount Rx (FJ)',
                'X ' => 'PF - DEFAULT (X )',
                'EA' => 'Pharmacy Data Management (EA)',
                'DW' => 'PHCS (DW)',
                'AX' => 'PINNACLE (AX)',
                'BN' => 'Prescription Solutions (BN)',
                'AA' => 'Tri-Care Express Scripts (AA)',
                'AI' => 'United Healthcare (AI)',
            ];
        
            // Fetch unique insurance codes from the database
            $insuranceCodes = DB::table('scripts')->distinct()->pluck('Ins');
        
            // Map insurance codes to their full names
            $insurances = $insuranceCodes->map(function ($code) use ($insuranceMapping) {
                return [
                    'code' => $code,
                    'name' => $insuranceMapping[$code] ?? $code, // Fallback to the code if not found in mapping
                ];
            });
        
            return view('dashboard.addscript', compact('insurances'));
        }
        
   

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'class' => ['required', 'string', 'max:255'],
            'ndc' => ['required','numeric'],
            'script' => ['required'], // Validate the role input
            'date' => ['required', 'date_format:m/d/Y'], // Validate the date format
            'insurance' => ['required', 'string'], // Add validation for insurance

            // 'regex:/^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/\d{4}$/'], // MM/DD/YYYY format
            
            // Parse the date
            
        ]);
        $date = Carbon::createFromFormat('m/d/Y', $request->date);

        $user = Script::create([
            'Drug_Name' => $request->name,
            'Class' => $request->class,
            'NDC' => ($request->ndc),
            'Script' => $request->script, // Store the role
            'Date' => $date,
            'Ins'=>$request->insurance // Store the role

        ]);

        return redirect()->route('dashboard.scripts.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    $insuranceMapping = [
        'AL' => 'Aetna (AL)',
        'BW' => 'Aetna (BW)',
        'AD' => 'Aetna Medicare (AD)',
        'AF' => 'Anthem BCBS (AF)',
        'DS' => 'Blue Cross Blue Shield (DS)',
        'CA' => 'Blue Shield Medicare (CA)',
        'FQ' => 'Capital Rx (FQ)',
        'BF' => 'Caremark (BF)',
        'ED' => 'CatalystRx (ED)',
        'AM' => 'Cigna (AM)',
        'BO' => 'Default Claim Format (BO)',
        'AP' => 'Envision Rx Options (AP)',
        'CG' => 'Express Scripts (CG)',
        'BI' => 'Horizon (BI)',
        'AJ' => 'Humana Medicare (AJ)',
        'BP' => 'informedRx (BP)',
        'AO' => 'MEDCO HEALTH (AO)',
        'AC' => 'MEDCO MEDICARE PART D (AC)',
        'AQ' => 'MEDGR (AQ)',
        'CC' => 'MY HEALTH LA (CC)',
        'AG' => 'Navitus Health Solutions (AG)',
        'AH' => 'OptumRx (AH)',
        'AS' => 'PACIFICARE LIFE AND H (AS)',
        'FJ' => 'Paramount Rx (FJ)',
        'X ' => 'PF - DEFAULT (X )',
        'EA' => 'Pharmacy Data Management (EA)',
        'DW' => 'PHCS (DW)',
        'AX' => 'PINNACLE (AX)',
        'BN' => 'Prescription Solutions (BN)',
        'AA' => 'Tri-Care Express Scripts (AA)',
        'AI' => 'United Healthcare (AI)',
    ];

    // Fetch the record to edit
    $data = Script::findOrFail($id);

    // Fetch insurance codes and map to full names
    $insuranceCodes = DB::table('scripts')->distinct()->pluck('Ins');
    $insurances = $insuranceCodes->map(function ($code) use ($insuranceMapping) {
        return [
            'code' => $code,
            'name' => $insuranceMapping[$code] ?? $code,
        ];
    });

    return view('dashboard.editscript', compact('data', 'insurances'));
}
 
     /**
      * Update the specified resource in storage.
      */
     public function update(Request $request, string $id)
     {
    
     // Validate the incoming data
    
     $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'class' => ['required', 'string', 'max:255'],
        'ndc' => ['required'],
        'script' => ['required'], // Validate the role input
        'date' => ['required','date'],
        'insurance'=>['required','string']
        // 'regex:/^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/\d{4}$/'], // MM/DD/YYYY format
    
        
        // Parse the date
        
    ]);
    $script = Script::findOrFail($id);
    $script->Date = \Carbon\Carbon::parse($request->date)->format('Y-m-d H:i:s');
    $script->save();
     // Find the existing drug record by ID
 
     // Update the record with new data
     $s = $script->update([
        'Drug_Name' => $request->name,
        'Class' => $request->class,
        'NDC' => ($request->ndc),
        'Script' => $request->script, // Store the role
       'Ins' => $request->insurance, // Store the role

    ]);
 
     // Redirect to the index page with a success message
     return redirect()->route('dashboard.scripts.index')->with('success', 'record updated successfully.');
 }
    

    /**
     * Update the specified resource in storage.
     */
   
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
    // Find the drug record by ID
    $s = Script::findOrFail($id);

    // Delete the record
    $s->delete();

    // Redirect to the index page with a success message
    return redirect()->route('dashboard.scripts.index')->with('success', 'Drug deleted successfully.');
    }
}
