<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $logs = AuditLog::query()
            ->when($q, fn($qq) => $qq->where('action','like',"%$q%")
                ->orWhere('description','like',"%$q%"))
            ->orderByDesc('id')
            ->paginate(20)->withQueryString();

        return view('admin.audit.index', compact('logs','q'));
        // View code in step 5
    }
}
