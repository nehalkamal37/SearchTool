<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use Illuminate\Http\Request;

class pharmacistController extends Controller
{
    //
    public function index(){

        $drugs=Drug::all();
       // dd($drugs);
        return view('dashboard.dashboard',compact('drugs'));
    }
}
