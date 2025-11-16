<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\News;
use App\Models\TableHistory;
use App\Services\AuditService;
use App\Settings\AppSettings;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Gate;

class NewsController extends Controller
{
    public function __construct(
        private AppSettings $settings,
        private AuditService $auditService,
    )
    {
    }

    public function index(): View
    {
        $news = News::query()
            ->with('user')
            ->orderBy('show_at', 'desc');

        if (Gate::denies('news_manage')) {
            $news->where('show_at', '<', now());
        }

        $news = $news->get();

        return view('news.index', [
            'news' => $news,
            'now' => now()->setTimezone('America/New_York'),
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        if ($this->settings->read_only) {
            $this->addFlash('error', 'The site is currently in read-only mode. No changes can be made.');
            return redirect()->back();
        }

        if (!$request->post('news_text')) {
            $this->addFlash('error', 'Cannot add a news item without any text.');
            return redirect()->back();
        } else {
            try {
                $date = Date::parse($request->post('date'))->shiftTimezone('America/New_York');
            } catch (Exception) {
                $this->addFlash('error', 'Invalid date provided.');
                return redirect()->back();
            }
        }

        $news = News::make([
            'text' => $request->post('news_text'),
            'show_at' => $date->setTimezone('UTC'),
        ]);
        $news->user()->associate($request->user());
        $news->save();

        $this->auditService->add(
            Action::makeWith('news-added', $news->id),
            TableHistory::makeWith(News::class, $news->id, $request->post()),
        );

        $this->addFlash('success', 'News item successfully added.');
        return redirect()->back();
    }

    public function delete(News $news): RedirectResponse
    {
        $news->delete();

        $this->auditService->add(
            Action::makeWith('news-deleted', $news->id),
        );

        $this->addFlash('success', 'News item successfully deleted.');
        return redirect()->back();
    }
}
