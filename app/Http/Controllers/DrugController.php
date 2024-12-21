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
    $drugNames = DB::table('scripts')->distinct()->pluck('Drug_Name');
    $insurances = DB::table('scripts')->distinct()->pluck('Ins');
    $ndcs = DB::table('scripts')->distinct()->pluck('NDC');

    return view('search', compact('drugNames', 'insurances', 'ndcs'));
}

public function filterData(Request $request)
{
    $drugName = $request->input('drug_name');

    if (!$drugName) {
        return response()->json([
            'insurances' => [],
            'ndcs' => [],
        ]);
    }

    $filteredData = DB::table('scripts')
        ->where('Drug_Name', $drugName)
        ->get();
  //dd($filteredData);
    // Debug: Check what data is fetched
    return response()->json([
        'filteredData' => $filteredData,
        'insurances' => $filteredData->pluck('Ins')->unique(),
        'ndcs' => $filteredData->pluck('NDC')->unique(),
    ]);
}


}
