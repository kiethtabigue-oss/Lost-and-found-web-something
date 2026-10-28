<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    /**
     * Display a listing of system audit logs.
     */
    public function index(Request $request): View
    {
        $query = AuditLog::with('user')->latest();

        // Action filter
        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        // Admin User filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Target type filter
        if ($request->filled('auditable_type')) {
            $type = $request->input('auditable_type');
            $query->where('auditable_type', 'like', "%{$type}%");
        }

        // Search in action or IP
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $logs = $query->paginate(20)->withQueryString();

        $actions = AuditLog::select('action')->distinct()->orderBy('action')->pluck('action');
        $admins = User::whereHas('role', fn ($q) => $q->whereIn('slug', ['super_admin', 'barangay_admin']))
            ->orderBy('name')
            ->get();

        return view('admin.community.audit_logs.index', compact('logs', 'actions', 'admins'));
    }
}
