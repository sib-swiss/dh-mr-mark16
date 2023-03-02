<?php

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
Route::get('/manuscript/{manuscript}', [ManuscriptController::class, 'show'])->name('manuscript.show');
