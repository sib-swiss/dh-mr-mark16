<?php

namespace App\Http\Controllers;

use App\Models\Manuscript;
use Illuminate\Contracts\View\View;

class ManuscriptController extends Controller
{
    public function index(): View
    {
        $manuscripts = Manuscript::where('published', 1)
            ->get()
            ->sortBy(function (Manuscript $manuscript, int $key) {
                return $manuscript->getMeta('temporal');
            })
            ->values();

        return view('home', ['manuscripts' => $manuscripts]);
    }

    public function show(string $manuscriptName): View
    {
        $manuscript = Manuscript::firstWhere('name', $manuscriptName);

        return view('manuscript', ['manuscript' => $manuscript]);
    }

    public function showPage(string $manuscriptName, int $pageNumber): View
    {
        $manuscript = Manuscript::firstWhere('name', $manuscriptName);

        return view('manuscript-page', ['manuscriptFolio' => $manuscript->folios[$pageNumber - 1]->contentHtml]);
    }
}
