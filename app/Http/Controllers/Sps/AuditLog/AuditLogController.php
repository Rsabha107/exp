<?php

namespace App\Http\Controllers\Sps\AuditLog;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use OwenIt\Auditing\Models\Audit;

class AuditLogController extends Controller
{
    public function index()
    {
        // dd('here');
        $audits = Audit::all();
        return view('sec.auditlog.list', compact('audits'));
    }

    public function get($id)
    {
        $audit = Audit::findOrFail($id);
        return response()->json(['audit' => $audit]);
    }

    public function list(Request $request)
    {
        $search = $request->search;
        $sort = $request->sort ?? 'id';
        $order = $request->order ?? 'desc';
        $limit = $request->limit ?? 10;
        $offset = $request->offset ?? 0;

        $mds_schedule_event_filter = $request->mds_schedule_event_filter ?? "";
        $mds_schedule_venue_filter = $request->mds_schedule_venue_filter ?? "";
        $mds_schedule_rsp_filter = $request->mds_schedule_rsp_filter ?? "";
        $mds_date_range_filter = $request->mds_date_range_filter ?? "";

        $query = Audit::with('user', 'event', 'venue');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('user_type', 'like', "%{$search}%")
                    ->orWhere('user_id', 'like', "%{$search}%")
                    ->orWhere('auditable_type', 'like', "%{$search}%")
                    ->orWhere('auditable_id', 'like', "%{$search}%")
                    ->orWhere('url', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('user_agent', 'like', "%{$search}%");
            });
        }

        if ($mds_schedule_event_filter) {
            $query->where('event_id', $mds_schedule_event_filter);
        }

        if ($mds_schedule_venue_filter) {
            $query->where('venue_id', $mds_schedule_venue_filter);
        }

        if ($mds_schedule_rsp_filter) {
            $query->where('rsp_id', $mds_schedule_rsp_filter);
        }

        if (!empty($mds_date_range_filter)) {
           $dates = array_map('trim', explode('to', $mds_date_range_filter));


            $startDate = trim($dates[0]);
            $endDate = count($dates) > 1 ? trim($dates[1]) : null;

            if (!empty($startDate)) {
                $startDate = Carbon::createFromFormat('d-m-Y', $startDate)->startOfDay();
            }

            if (!empty($endDate)) {
                $endDate = Carbon::createFromFormat('d-m-Y', $endDate)->endOfDay();
            }

            if (!empty($startDate) && !empty($endDate)) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } elseif (!empty($startDate)) {
                $query->where('created_at', '>=', $startDate);
            } elseif (!empty($endDate)) {
                $query->where('created_at', '<=', $endDate);
            }
        }


        $total = $query->count();

        $audits = $query->orderBy($sort, $order)
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(function ($audit) {
                return [
                    'id' => $audit->id,
                    'user_type' => $audit->user_type,
                    'user_id' => $audit->user_id,
                    'user' => $audit->user->name ?? 'System',
                    'event' => $audit->event,
                    'auditable_type' => $audit->auditable_type,
                    'auditable_id' => $audit->auditable_id,
                    'old_values' => json_encode($audit->old_values),
                    'new_values' => json_encode($audit->new_values),
                    'url' => $audit->url,
                    'ip_address' => $audit->ip_address,
                    'user_agent' => $audit->user_agent,
                    'tags' => $audit->tags,
                    'event_id' => $audit->event_id,
                    'event_name' => $audit->event->name ?? '-',
                    'venue_id' => $audit->venue_id,
                    'venue_name' => $audit->venue->title ?? '-',
                    'created_at' => $audit->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $audit->updated_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'total' => $total,
            'rows' => $audits,
        ]);
    }
}
