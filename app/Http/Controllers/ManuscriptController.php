<?php

namespace App\Http\Controllers;

use App\Models\Manuscript;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ManuscriptController extends Controller
{
    public function index(): View
    {
        $manuscripts = Manuscript::where('published', 1)
            ->orderBy('temporal')
            ->get();
        // dd($manuscripts);
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
        $manuscriptContentHtml = $manuscript->folios[$pageNumber - 1]->contentHtml;
        // dd($manuscriptContentHtml);
        return view('manuscript-page', ['manuscriptContentHtml' => $manuscriptContentHtml]);
    }

    public function search(): View
    {
        return view('search');
    }

    public function results(Request $request): View
    {
        $manuscripts = Manuscript::where('published', 1)
                ->orderBy('temporal')
                ->get()
                ->filter(function (Manuscript $manuscript) use ($request) {
                    if (
                        ($request->keywords && stripos($manuscript->getMeta('abstract'), $request->keywords) !== false)
                        ||
                        ($request->title && stripos($manuscript->name, $request->title) !== false)
                        ||
                        ($request->shelfmark && stripos($manuscript->getMeta('isFormatOf'), $request->shelfmark) !== false)
                        ||
                        ($request->docId && stripos($manuscript->getMeta('temporal'), $request->docId) !== false)
                        ||
                        ($request->language && stripos($manuscript->getLangExtended(), $request->language) !== false)

                    ) {
                        return true;
                    }
                    return false;
                })->all();

        return view('results', ['manuscripts' => $manuscripts]);
    }
}
