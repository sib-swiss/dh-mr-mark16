<?php

namespace App\Http\Controllers;

use App\Models\Manuscript;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
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

    public function showOld(Request $request): RedirectResponse
    {
        $manuscriptName = base64_decode($request->id);

        return redirect(route('manuscript.show', $manuscriptName), 301);
    }

    public function show(string $manuscriptName): View
    {
        $manuscript = Manuscript::firstWhere('name', $manuscriptName);

        return view('manuscript', ['manuscript' => $manuscript]);
    }

    public function showPage(string $manuscriptName, int $pageNumber): View
    {
        $manuscript = Manuscript::firstWhere('name', $manuscriptName);
        $manuscriptFolio = $manuscript->folios[$pageNumber - 1];

        $lang = request()->lang;
        if (in_array($lang, ['ENG', 'FRA', 'GER'])) {
            $manuscriptFolioTranslation = $manuscriptFolio->contentsTranslations()
                ->where('name', 'LIKE', '%_'.$lang.'.%')
                ->first();
            if ($manuscriptFolioTranslation) {
                return view('manuscript-page', ['manuscriptContentHtml' => $manuscriptFolioTranslation]);
            }
        }

        return view('manuscript-page', ['manuscriptContentHtml' => $manuscriptFolio->contentHtml]);
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
