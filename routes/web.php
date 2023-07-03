<?php

use App\Http\Controllers\IIIFImageController;
use App\Http\Controllers\IIIFPresentationController;
use App\Http\Controllers\ManuscriptController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ManuscriptController::class, 'index'])->name('home');
Route::get('/about', function () {
    return view('about');
})->name('about');
Route::get('/search', [ManuscriptController::class, 'search'])->name('search');
Route::get('/results', [ManuscriptController::class, 'results'])->name('results');
Route::get('/manuscript/{manuscriptName}', [ManuscriptController::class, 'show'])->name('manuscript.show');
Route::get('/manuscript/{manuscriptName}/page/{number}', [ManuscriptController::class, 'showPage'])->name('manuscript.show.page');

Route::get('iiif/{identifier}/{region}/{size}/{rotation}/{quality}.{format}', [IIIFImageController::class, 'requests'])->name('iiif.image.requests');
Route::get('iiif/{identifier}/info.json', [IIIFImageController::class, 'info'])->name('iiif.image.info');
Route::get('/iiif/collection', [IIIFPresentationController::class, 'collection'])->name('iiif.presentation.collection');
Route::get('/iiif/{manuscriptName}/manifest.json', [IIIFPresentationController::class, 'manifest'])->name('iiif.presentation.manifest');

Route::get('/show', [ManuscriptController::class, 'showOld'])->name('manuscript.showold');
