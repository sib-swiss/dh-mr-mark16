<?php

namespace App\Http\Controllers;

use App\Models\Manuscript;

class ManuscriptController extends Controller
{
    public function index()
    {
        $manuscripts = Manuscript::where('published', 1)->get();

        return view('home', ['manuscripts' => $manuscripts]);
    }

    public function show(Manuscript $manuscript)
    {
        return view('manuscript', ['manuscript' => $manuscript]);
    }
}
