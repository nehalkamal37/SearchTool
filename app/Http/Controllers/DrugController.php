<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use App\Models\Script;
use Illuminate\Http\Request;
use App\Models\ReclassifiedDrug;
use Illuminate\Support\Facades\DB;

class DrugController extends Controller
{
    public function index(Request $request)
    {
        // Load datasets
        $drugs = Drug::query()->distinct()->get();
        $reclassifiedDrugs = Script::query()->distinct()->get();

        // Ensure NDC and Drug Name are strings for comparison
        $drugs->each(function ($drug) {
            $drug->NDC = trim((string) $drug->NDC);
            $drug->Drug_Name = trim((string) $drug->Drug_Name);
            $drug->class = trim((string) $drug->class);
        });

        $reclassifiedDrugs->each(function ($reclassified) {
            $reclassified->ndc = trim((string) $reclassified->ndc);
            $reclassified->drug_name = trim((string) $reclassified->drug_name);
        });

        // Parse Date and calculate Net Profit dynamically
        $drugs->each(function ($drug) {
            $drug->Date = !empty($drug->Date) ? date('Y-m-d', strtotime($drug->Date)) : null;
            $drug->Net_Profit = round(($drug->Pat_Pay + $drug->Ins_Pay - $drug->ACQ), 2);
        });

        // Insurance mapping
        $insuranceMapping = [
            'AL' => 'Aetna (AL)',
            'BW' => 'aetna (BW)',
            'AD' => 'Aetna Medicare (AD)',
            'AF' => 'Anthem BCBS (AF)',
            'DS' => 'Blue Cross Blue Shield (DS)',
            // Add other mappings here...
        ];

        $drugs->each(function ($drug) use ($insuranceMapping) {
            $drug->Ins_Full_Name = $insuranceMapping[$drug->Ins] ?? $drug->Ins;
        });

        // Filters from the request
        $drugNameInput = $request->get('drug_name');
        $insuranceInput = $request->get('insurance');
        $ndcInput = $request->get('ndc');

        // Filter drugs based on inputs
        $filteredDrugs = $drugs;

        if ($drugNameInput) {
            $filteredDrugs = $filteredDrugs->where('Drug_Name', $drugNameInput);
        }
        if ($ndcInput) {
            $filteredDrugs = $filteredDrugs->where('NDC', $ndcInput);
        }
        if ($insuranceInput) {
            $filteredDrugs = $filteredDrugs->where('Ins_Full_Name', $insuranceInput);
        }

        // Sort by latest date
        $filteredDrugs = $filteredDrugs->sortByDesc('Date');

        // Check if no results found
        if ($drugNameInput && $ndcInput && $filteredDrugs->isEmpty()) {
            // Search in reclassified database
            $formattedNDC = substr($ndcInput, 0, 5) . '-' . substr($ndcInput, 5, 4) . '-' . substr($ndcInput, 9);
            $reclassifiedDetails = $reclassifiedDrugs->where('ndc', $formattedNDC);

            if ($reclassifiedDetails->isNotEmpty()) {
                $firstReclassifiedResult = $reclassifiedDetails->first();
                $drugClass = $firstReclassifiedResult->drug_class;

                // Fetch alternatives by drug class
                $alternatives = $reclassifiedDrugs->where('drug_class', $drugClass)->unique('drug_name');

                return response()->json([
                    'message' => 'No insurance data available.',
                    'reclassifiedDetails' => $firstReclassifiedResult,
                    'alternatives' => $alternatives
                ]);
            } else {
                return response()->json(['message' => 'No additional data found in the reclassified database.']);
            }
        }

        // Prepare response data
        $response = [
            'filteredDrugs' => $filteredDrugs,
        ];

        if ($drugNameInput && $insuranceInput && !$filteredDrugs->isEmpty()) {
            $firstValidResult = $filteredDrugs->first();
            $drugClass = $firstValidResult->class;

            // Fetch alternatives by class
            if (strtolower($drugClass) !== 'other') {
                $alternatives = $drugs
                    ->where('class', $drugClass)
                    ->where('Drug_Name', '!=', $firstValidResult->Drug_Name)
                    ->sortByDesc('Date')
                    ->unique('Drug_Name');

                $response['alternatives'] = $alternatives;
            }
        }

        return response()->json($response);
    }


    //   mor funcs


    public function showSearchPage()
{
    // إحضار جميع الأسماء المميزة من الجدول
    $drugNames = DB::table('drugs')->distinct()->pluck('drug_name');

    //$insurances = DB::table('scripts')->distinct()->pluck('Ins');

    // Get distinct drug names
        $drugNames = DB::table('drugs')->distinct()->pluck('drug_name');
    
        // Get distinct insurance short names
        $insuranceShortNames = DB::table('scripts')->distinct()->pluck('Ins');
    
        // Map short names to full names
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
            'AI' => 'United Healthcare (AI)'
        ];
    
        // Replace short names with full names
        $insurances = $insuranceShortNames->map(function ($shortName) use ($insuranceMapping) {
            return $insuranceMapping[$shortName] ?? $shortName; // Fallback to short name if no mapping found
        });
    
        // Get distinct NDCs
        $ndcs = DB::table('drugs')->pluck('ndc');
    
        // Pass data to the view
        return view('search', compact('drugNames', 'insurances', 'ndcs'));
    }
    
   



public function filterData(Request $request)
{
    $drugName = $request->input('drug_name');
    $insurance = $request->input('insurance'); // Retrieve insurance from the request

    // Log inputs for debugging
    \Log::info('Drug Name:', [$drugName]);
    \Log::info('Insurance:', [$insurance]);

    // Normalize spaces: trim and replace multiple spaces with a single space
    \Log::info('Normalized Drug Name:', [$drugName]); // Log the normalized name

    // Retrieve filtered data from drugs table
    $filteredDrugData = DB::table('drugs')
        ->where(DB::raw('TRIM(drug_name)'), trim($drugName))
        ->get();

    // Retrieve filtered data from scripts table
    $filteredScriptData = DB::table('scripts')
        ->where(DB::raw('TRIM(Drug_Name)'), trim($drugName))
        ->get();

    \Log::info('Filtered Drug Data:', $filteredDrugData->toArray());
    \Log::info('Filtered Script Data:', $filteredScriptData->toArray());

    // Get unique insurances from filtered script data
    $insurances = $filteredScriptData->pluck('Ins')->unique();

    // Get unique NDCs that match the selected drug name and insurance
    $ndcs = Script::where('Drug_Name', $drugName)
        ->where(function ($query) use ($insurance) {
            $query->where('Ins', $insurance) // Direct match
                  ->orWhere('Ins', 'LIKE', "%($insurance)"); // Match short name
        })
        ->pluck('NDC');

        $ndcs = Script::where('Drug_Name', $drugName)
            ->where('Ins', $insurance) // Direct match
                  ->orWhere('Ins', 'LIKE', "%($insurance)") // Match short name
            ->pluck('NDC');

    // Fallback to NDCs from filteredDrugData if no NDCs were found
    if ($ndcs->isEmpty()) {
        $ndcs = Script::where('Drug_Name', $drugName)
        ->where('Ins', $insurance) // Direct match
              ->orWhere('Ins', 'LIKE', "%($insurance)") // Match short name
        ->pluck('NDC');
       $ndcs = $filteredScriptData->pluck('NDC')->unique()->values();
    }

    // Return the response as JSON
    return response()->json([
        'filteredData' => $filteredScriptData,
        'insurances' => $insurances->values(),
        'ndcs' => $ndcs,
    ]);
}


public function processNdc(Request $request)
{
    $ndc = $request->query('ndc'); // Get the NDC from the query string

    if (!$ndc) {
        return back()->with('error', 'No NDC provided.');
    }

    // Normalize the input NDC (remove any dashes)
    $normalizedInputNdc = str_replace('-', '', $ndc);

    // Step 1: Find the drug name related to the provided NDC
    $data = Drug::whereRaw("REPLACE(ndc, '-', '') = ?", [$normalizedInputNdc])->first();
    
    // Step 1: Find the drug name related to the provided NDC
   // $data = Drug::where('ndc', $ndc)->first(); // Get the first matching record
    if (!$data) {
        return back()->with('error', 'No drug found for the provided NDC.');
    }

    $drugName = $data->drug_name; // Extract the drug name

    // Step 2: Find all rows related to the drug name
    $relatedRows = Drug::where('drug_name', $drugName) ->distinct()->get();; // Get all records for the same drug name

    // Step 3: Return the data to the view
    return view('ndcResult', [
        'selectedNdc' => $ndc, // The initially selected NDC
        'drugName' => $drugName, // The drug name related to the selected NDC
        'relatedRows' => $relatedRows, // All related rows (full columns)
        'message' => 'NDC processed successfully.',
    ]);
}

}