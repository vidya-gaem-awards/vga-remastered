<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Services\AuditService;
use Illuminate\Contracts\View\View;

class AuditLogController extends Controller
{
    public function index(AuditService $auditService): View
    {
        $actions = Action::query()
            ->with('user')
            ->with('tableHistory')
            ->whereNotIn('action', array_keys(AuditService::PUBLIC_ACTIONS))
            ->orderByDesc('created_at')
            ->get();

        return view('audit-log', [
            'actions' => $actions,
            'actionTypes' => AuditService::ACTIONS,
            'auditService' => $auditService,
        ]);
    }
}
