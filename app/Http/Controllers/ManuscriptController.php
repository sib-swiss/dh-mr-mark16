<?php

namespace App\Http\Controllers;

use App\Models\Manuscript;
use Illuminate\Contracts\View\View;

class ManuscriptController extends Controller
{
    public function index(): View
    {
        $manuscripts = Manuscript::where('published', 1)->get();

        return view('home', ['manuscripts' => $manuscripts]);
    }

    public function show(Manuscript $manuscript): View
    {
        return view('manuscript', ['manuscript' => $manuscript]);
    }
}
