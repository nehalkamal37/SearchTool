<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\searchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DrugController;

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
    return view('welcome');
});
   // Route::get('search',[searchController::class,'index'])->name('find');   //used one 
    Route::Post('searchDrug',[searchController::class,'search'])->name('search');  //this is the used method to handle search

   Route::post('/get-dependent-data', [searchController::class, 'getDependentData'])->name('getDependentData');
   
//

   Route::get('/search', [DrugController::class, 'showSearchPage'])->name('searchPage'); // view search page
   Route::post('/filter-data', [DrugController::class, 'filterData'])->name('filter');   


//
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
