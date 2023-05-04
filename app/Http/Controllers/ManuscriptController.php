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
            ->paginate(10);

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
        $data = [
            'languages' => collect(config('manuscript.languages'))->sortBy('name'),
        ];

        return view('search', $data);
    }

    public function results(Request $request): View
    {
        $manuscripts = Manuscript::where('published', 1)
            ->when($request->subject, function ($query) use ($request) {
                return $query->where('name', 'like', '%'.str_replace(' ', '', $request->subject).'%');
            })
            ->orderBy('temporal')
            ->get()
            ->filter(function (Manuscript $manuscript) use ($request) {

                if ($request->subject) {
                    return true;
                } elseif (

                    ($request->keywords && stripos($manuscript->getMeta('abstract'), $request->keywords) !== false)
                    ||
                    ($request->title && stripos(str_replace(' ', '', $manuscript->name), str_replace(' ', '', $request->title)) !== false)
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
