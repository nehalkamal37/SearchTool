<?php

namespace App\Http\Controllers\dashboard;

use App\Models\Drug;
use App\Models\Script;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class drugsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    

    public function index(Request $request)
    {
        
       // $drugs=Drug::paginate(7);
        $query = Drug::query();

        // If a search term is provided, filter the results
        if ($request->has('search') && $request->search != '') {
            $scripts=$query->where('drug_name', 'like', '%' . $request->search . '%');

            $drugCounts = DB::table('scripts')
            ->select(DB::raw('LOWER(Drug_Name) as Drug_Name'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('LOWER(Drug_Name)'))
            ->get()
            ->mapWithKeys(function ($item) {
                return [strtolower($item->Drug_Name) => $item->count];
            });
           // return view('dashboard.drugstable',compact('drugs','scripts','drugCounts'));

        }
        $drugs = $query->paginate(7);
      

              //  return view('dashboard.scriptstable', compact('scripts', 'insuranceMapping'));
    
    


        /*
      //  $scripts=Script::paginate(7);
        $drugCounts = DB::table('scripts')
        ->select('Drug_Name', 'NDC', DB::raw('COUNT(*) as count'))
        ->groupBy('Drug_Name', 'NDC')
        ->get()
        ->mapWithKeys(function ($item) {
            return [$item->Drug_Name . '_' . $item->NDC => $item->count];
        });    
        $drugCounts = DB::table('scripts')
        ->select(DB::raw('LOWER(Drug_Name) as Drug_Name'), DB::raw('COUNT(*) as count'))
        ->groupBy(DB::raw('LOWER(Drug_Name)'))
        ->get()
        ->mapWithKeys(function ($item) {
            return [strtolower($item->Drug_Name) => $item->count];
        });
      */
    
        return view('dashboard.drugstable',compact('drugs'));
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.adddrug');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'class' => ['required', 'string', 'max:255'],
            'ndc' => ['required', 'string'],
            'mfg' => ['required', 'string'], // Validate the role input
            'form' => ['required', 'string'], // Validate the role input
'strength' => ['required', 'string'],
        ]);
    
        $user = Drug::create([
            'drug_name' => $request->name,
            'drug_class' => $request->class,
            'ndc' => ($request->ndc),
            'mfg' => $request->mfg, // Store the role
            'form' => $request->form,
'strength'=>$request->strength,
        ]);

        return redirect()->route('dashboard.drugs.index');
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
    public function edit(string $id,Request $req)
    {
        $data=Drug::find($id);
       // dd($data);
        return view('dashboard.editdrug',compact('data'));

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
        'ndc' => ['required', 'string'],
        'mfg' => ['required', 'string'], 
        'form' => ['required', 'string'], 
    ]);

    // Find the existing drug record by ID
    $drug = Drug::findOrFail($id);

    // Update the record with new data
    $drug->update([
        'drug_name' => $request->name,
        'drug_class' => $request->class,
        'ndc' => $request->ndc,
        'mfg' => $request->mfg,
        'form' => $request->form,
    ]);

    // Redirect to the index page with a success message
    return redirect()->route('dashboard.drugs.index')->with('success', 'Drug updated successfully.');
}

    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
      
    // Find the drug record by ID
    $drug = Drug::findOrFail($id);

    // Delete the record
    $drug->delete();

    // Redirect to the index page with a success message
    return redirect()->route('dashboard.drugs.index')->with('success', 'Drug deleted successfully.');
    
}

    
}
