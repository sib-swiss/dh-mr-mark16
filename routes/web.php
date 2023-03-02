<?php

use App\Http\Controllers\ManuscriptController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
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
Route::get('/manuscript/{manuscriptName}', [ManuscriptController::class, 'show'])->name('manuscript.show');

Route::get('iiif/{identifier}/{region}/{size}/{rotation}/{quality}.{format}',
    function (Request $request) {
        $parameters = $request->route()->parameters();

        // $file = storage_path('public/'.str_replace("__","/",$parameters['identifier']);
        $file = storage_path('app/public/'.str_replace('__', '/', $parameters['identifier']));
        // dd($file);
        $factory = new \Conlect\ImageIIIF\ImageFactory;

        $file = $factory()->load($file)
            ->withParameters($parameters)
            ->stream();

        $response = Response::make($file);

        $response->header('Content-Type', config("iiif.mime.{$parameters['format']}"));

        return $response;
    }
);

Route::get('iiif/{identifier}/info.json',
    function (Request $request) {
        $file = storage_path('app/public/'.str_replace('__', '/', $request->identifier));

        $factory = new \Conlect\ImageIIIF\ImageFactory;

        $info = $factory()->load($file)
            ->info('iiif', $request->identifier);

        return $info;
    }
);
