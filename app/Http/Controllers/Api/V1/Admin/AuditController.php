<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuditController extends Controller
{
    public function __construct()
    {
        // Sanctum auth + Admin gate (AdminOnly blocks access while impersonating)
        $this->middleware(['auth:sanctum', 'admin']);
    }

    /**
     * GET /api/v1/admin/audit
     *
     * Query params:
     * - page, per_page (1..100)
     * - sort: created_at | -created_at (default -created_at)
     * - actor_id, target_type, target_id, action, ip
     * - from, to (ISO date/time)
     * - q (free text over action, description, metadata, ip, user_agent)
     */
    public function index(Request $request)
    {
        [$query, $perPage] = $this->buildFilteredQuery($request);

        $results = $query->paginate(
            perPage: $perPage,
            page: max(1, (int) $request->integer('page', 1))
        );

        return response()->json([
            'data' => $results->items(),
            'meta' => [
                'current_page' => $results->currentPage(),
                'per_page'     => $results->perPage(),
                'total'        => $results->total(),
                'last_page'    => $results->lastPage(),
                'sort'         => $request->input('sort', '-created_at'),
                'filters'      => $request->only([
                    'actor_id','target_type','target_id','action','ip','from','to','q'
                ]),
            ],
        ]);
    }

    /**
     * GET /api/v1/admin/audit/{auditLog}
     */
    public function show(AuditLog $auditLog)
    {
        return response()->json([
            'data' => $auditLog,
        ]);
    }

    /**
     * GET /api/v1/admin/audit/export.csv
     *
     * Same filters as index(); streams CSV. Optional query param:
     * - limit (defaults 50_000, hard-capped)
     */
    public function export(Request $request)
    {
        [$query] = $this->buildFilteredQuery($request);

        $limit = (int) $request->integer('limit', 50000);
        $limit = max(1, min($limit, 100000));

        $filename = 'audit-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($query, $limit) {
            $out = fopen('php://output', 'w');

            // Header row
            fputcsv($out, [
                'id',
                'actor_id',
                'target_type',
                'target_id',
                'action',
                'description',
                'ip_address',
                'user_agent',
                'metadata',
                'created_at',
            ]);

            $query->limit($limit)->chunk(1000, function ($rows) use ($out) {
                foreach ($rows as $row) {
                    fputcsv($out, [
                        $row->id,
                        $row->actor_id,
                        $row->target_type,
                        $row->target_id,
                        $row->action,
                        // Keep descriptions as-is; guard against newlines to keep CSV sane
                        is_string($row->description) ? preg_replace('/\s+/', ' ', $row->description) : '',
                        $row->ip_address,
                        $row->user_agent,
                        // Ensure metadata is serialized JSON (or blank)
                        is_array($row->metadata) || is_object($row->metadata)
                            ? json_encode($row->metadata, JSON_UNESCAPED_UNICODE)
                            : (is_string($row->metadata) ? $row->metadata : ''),
                        optional($row->created_at)->toDateTimeString(),
                    ]);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /* ---------------------------------------------------------------------
     | Internals
     | ------------------------------------------------------------------ */

    /**
     * Apply filters/sort/pagination defaults and return the query + perPage.
     *
     * @return array{0: Builder, 1: int}
     */
    private function buildFilteredQuery(Request $request): array
    {
        $perPage = (int) $request->integer('per_page', 25);
        $perPage = max(1, min($perPage, 100));

        $sort = (string) $request->input('sort', '-created_at');
        $sortColumn = ltrim($sort, '-');
        $sortDir = Str::startsWith($sort, '-') ? 'desc' : 'asc';
        if (!in_array($sortColumn, ['created_at', 'id'], true)) {
            $sortColumn = 'created_at';
        }

        $q = AuditLog::query();

        // Filters
        if ($request->filled('actor_id')) {
            $q->where('actor_id', (int) $request->input('actor_id'));
        }

        if ($request->filled('target_type')) {
            $q->where('target_type', $request->input('target_type'));
        }

        if ($request->filled('target_id')) {
            $q->where('target_id', (int) $request->input('target_id'));
        }

        if ($request->filled('action')) {
            $q->where('action', $request->input('action'));
        }

        if ($request->filled('ip')) {
            $q->where('ip_address', $request->input('ip'));
        }

        // Date range
        $from = $this->safeDate($request->input('from'));
        $to   = $this->safeDate($request->input('to'));
        if ($from) {
            $q->where('created_at', '>=', $from);
        }
        if ($to) {
            $q->where('created_at', '<=', $to);
        }

        // Free-text search
        if ($request->filled('q')) {
            $term = '%' . str_replace(['%','_'], ['\%','\_'], $request->input('q')) . '%';
            $q->where(function (Builder $w) use ($term) {
                $w->where('action', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhere('ip_address', 'like', $term)
                    ->orWhere('user_agent', 'like', $term)
                    ->orWhere('metadata', 'like', $term);
            });
        }

        $q->orderBy($sortColumn, $sortDir);

        // Columns (keep it consistent & small)
        $q->select([
            'id',
            'actor_id',
            'target_type',
            'target_id',
            'action',
            'description',
            'ip_address',
            'user_agent',
            'metadata',
            'created_at',
        ]);

        return [$q, $perPage];
    }

    private function safeDate(?string $value): ?CarbonImmutable
    {
        if (!$value) return null;
        try {
            return CarbonImmutable::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
