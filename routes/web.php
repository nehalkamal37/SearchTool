<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DrugController;
use App\Http\Controllers\searchController;
use App\Http\Controllers\streamController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\pharmacistController;
use App\Http\Controllers\dashboard\userController;
use App\Http\Controllers\dashboard\drugsController;
use App\Http\Controllers\dashboard\scriptsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('register');
    //view('welcome');
});
    Route::get('search',[DrugController::class,'index'])->name('searchPage');   //used to show data in json format
    Route::match(['get', 'post'],'searchDrug',[searchController::class,'search'])->name('search');  //this is the used method to handle search
    Route::Post('searchDrugs',[searchController::class,'searchDrug'])->name('searchDrug');  //this is the used method to handle search

   Route::post('/get-dependent-data', [searchController::class, 'getDependentData'])->name('getDependentData');
   
//

   Route::get('/search', [DrugController::class, 'showSearchPage'])->name('searchPage'); // view search page
    Route::post('/filter-data', [DrugController::class, 'filterData'])->name('filter');   
    Route::get('/searchndc', [DrugController::class, 'filterData2'])->name('fndc');
    Route::get('/process-ndc', [DrugController::class, 'processNdc'])->name('process.ndc');



//profile route
//Route::get('/profile', [userController::class, 'index'])->name('profile');


Route::middleware(['auth', 'role:pharmacist,administrator,technician,customer'])->group(function () {
    Route::get('/pharmacist/dashboard', [pharmacistController::class, 'index'])->name('pharmacist.dashboard');
});

//Administrator routes
Route::middleware(['auth', 'role:administrator'])->group(function () {
    Route::get('/pharmacist/dashboard', [pharmacistController::class, 'index'])->name('pharmacist.dashboard');
});
/*
// Technician routes
Route::middleware(['auth', 'role:technician'])->group(function () {
    Route::get('/pharmacist/dashboard', [TechnicianController::class, 'index'])->name('technician.dashboard');
});
*/

//dashboard tables

Route::resource('/drugs', drugsController::class)->names([
    'index' => 'dashboard.drugs.index',
    'create' => 'dashboard.drugs.create',
    'store' => 'dashboard.drugs.store',
    'show' => 'dashboard.drugs.show',
    'edit' => 'dashboard.drugs.edit',
    'update' => 'dashboard.drugs.update',
    'destroy' => 'dashboard.drugs.destroy',
]);

Route::resource('/scripts', scriptsController::class)->names([
    'index' => 'dashboard.scripts.index',
    'create' => 'dashboard.scripts.create',
    'store' => 'dashboard.scripts.store',
    'show' => 'dashboard.scripts.show',
    'edit' => 'dashboard.scripts.edit',
    'update' => 'dashboard.scripts.update',
    'destroy' => 'dashboard.scripts.destroy',
]);

//
Route::get('/dashboard', function () {
    return view('dashboard.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('/home','dashboard.dashboard')->middleware('auth')->name('dash');

Route::get('/stream', [streamController::class, 'index'])->name('drugs.index');

require __DIR__.'/auth.php';
