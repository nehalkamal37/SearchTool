<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class streamController extends Controller
{
    public function index(Request $request)
    {
        // Load the datasets
        $mainFilePath = storage_path('public/drugs.csv');
        $reclassifiedFilePath = storage_path('public/scripts.csv');
        
        $df = collect(array_map('str_getcsv', file($mainFilePath)));
        $reclassifiedDf = collect(array_map('str_getcsv', file($reclassifiedFilePath)));

        // Clean and prepare the data
        $df = $df->map(function ($row) {
            return [
                'NDC' => trim($row['NDC']),
                'Drug Name' => trim($row['Drug Name']),
                'class' => trim($row['class']),
                'Ins' => trim($row['Ins']),
                'Pat Pay' => (float) $row['Pat Pay'],
                'Ins Pay' => (float) $row['Ins Pay'],
                'ACQ' => (float) $row['ACQ'],
                'Qty' => $row['Qty'],
                'Script' => $row['Script'],
                'Date' => Carbon::parse($row['Date']),
            ];
        });

        $df = $df->map(function ($row) {
            $row['Net Profit'] = round(($row['Pat Pay'] + $row['Ins Pay']) - $row['ACQ'], 2);
            return $row;
        });

        $insuranceMapping = [
            'AL' => 'Aetna (AL)',
            // Add all mappings here...
        ];

        // Map insurance codes to full names
        $df = $df->map(function ($row) use ($insuranceMapping) {
            $row['Ins Full Name'] = $insuranceMapping[$row['Ins']] ?? $row['Ins'];
            return $row;
        });

        // Filter logic
        $drugName = $request->input('drug_name', null);
        $ndc = $request->input('ndc', null);
        $insuranceFullName = $request->input('insurance', null);

        $filteredDf = $df;
        if ($drugName) {
            $filteredDf = $filteredDf->where('Drug Name', $drugName);
        }
        if ($ndc) {
            $filteredDf = $filteredDf->where('NDC', $ndc);
        }
        if ($insuranceFullName) {
            $filteredDf = $filteredDf->where('Ins Full Name', $insuranceFullName);
        }

        return view('drugs.index', [
            'df' => $filteredDf,
            'reclassifiedDf' => $reclassifiedDf,
            'drugName' => $drugName,
            'ndc' => $ndc,
            'insuranceFullName' => $insuranceFullName,
        ]);
    }
}
