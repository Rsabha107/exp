<?php

namespace App\Http\Controllers\Sps\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendAdminCancelBookingEmailJob;
use App\Jobs\SendNewBookingEmailJob;
use App\Models\Mds\BookingSlot;
use App\Models\Mds\FunctionalArea;
use App\Models\Mds\DeliveryBooking;
use App\Models\Mds\DeliveryCargoType;
use App\Models\Mds\DeliveryRsp;

use App\Models\Mds\DeliveryType;
use App\Models\Mds\DeliveryVehicle;
use App\Models\Mds\DeliveryVehicleType;
use App\Models\Mds\DeliveryVenue;
use App\Models\Mds\DeliveryZone;
use App\Models\Mds\MdsDriver;
use App\Models\Mds\MdsEvent;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function dashboardxx()
    {
        return view('sps.admin.dashboard.index');
    }

    public function dashboard()
    {
        // dd('inside trackiDashboard');


        // if (session()->has('workspace_id')){
        //     dd('session for workspace: '.session()->get('workspace_id'));
        // }

        $booking_scheduled_today = DeliveryBooking::where('booking_date', date('Y-m-d'))
            ->count();

        $booking_scheduled_tomorrow = DeliveryBooking::where('booking_date', date('Y-m-d', strtotime('+1 day')))
            ->count();
        $total_bookings = DeliveryBooking::all()
            ->count();
        
        $total_checked_in = DeliveryBooking::with('status')
                                            ->whereHas('status', function($query){
                                                $query->where('title', '=', 'Checked In');
                                            })
            ->count();
        $total_users = User::where('status', 1)->count();
        // $proj_count = Event::leftJoin('tasks', 'tasks.event_id', '=', 'events.id')
        //     ->whereNull('archived')
        //     ->when($user_department, function ($query, $user_department) {
        //         return $query->where('tasks.department_assignment_id', $user_department);
        //     })->distinct('events.id')->count();

        // $unbudgeted_proj_count = Event::leftJoin('tasks', 'tasks.event_id', '=', 'events.id')
        //     ->leftJoin('funds_category', 'funds_category.id', '=', 'events.fund_category_id')
        //     ->whereNull('archived')
        //     ->whereNot(function ($query) {
        //         $query->where('funds_category.name', '=', 'Budgeted');
        //     })
        //     ->when($user_department, function ($query, $user_department) {
        //         return $query->where('tasks.department_assignment_id', $user_department);
        //     })->distinct('events.id')->count();

        // $task_count = Task::join('events', 'events.id', '=', 'tasks.event_id')
        //     ->whereNull('events.archived')
        //     ->when($user_department, function ($query, $user_department) {
        //         return $query->where('tasks.department_assignment_id', $user_department);
        //     })
        //     ->when(auth()->user()->functional_area_id, function ($query, $user_fa) {
        //         return $query->where('events.functional_area_id', $user_fa);
        //     })
        //     ->count();

        // $late_tasks_count = Task::join('events', 'events.id', '=', 'tasks.event_id')
        //     ->whereNull('events.archived')
        //     ->whereRaw('datediff(tasks.due_date, CURRENT_DATE) < 0')
        //     ->where('tasks.progress', '<', 1)
        //     ->when($user_department, function ($query, $user_department) {
        //         return $query->where('tasks.department_assignment_id', $user_department);
        //     })
        //     ->when(auth()->user()->functional_area_id, function ($query, $user_fa) {
        //         return $query->where('events.functional_area_id', $user_fa);
        //     })
        //     ->count();

        // $ending_tasks_count = Task::join('events', 'events.id', '=', 'tasks.event_id')
        //     ->whereNull('events.archived')
        //     ->whereRaw('datediff(tasks.due_date, CURRENT_DATE) < 3')
        //     ->whereRaw('datediff(tasks.due_date, CURRENT_DATE) >= 0')
        //     ->where('tasks.progress', '<', 1)
        //     ->when($user_department, function ($query, $user_department) {
        //         return $query->where('tasks.department_assignment_id', $user_department);
        //     })
        //     ->when(auth()->user()->functional_area_id, function ($query, $user_fa) {
        //         return $query->where('events.functional_area_id', $user_fa);
        //     })
        //     ->count();

        // $starting_tasks_count = Task::join('events', 'events.id', '=', 'tasks.event_id')
        //     ->whereNull('events.archived')
        //     ->whereRaw('datediff(tasks.start_date, CURRENT_DATE) < 3')
        //     ->whereRaw('datediff(tasks.start_date, CURRENT_DATE) >= 0')
        //     ->where('tasks.progress', '<', 1)
        //     ->when($user_department, function ($query, $user_department) {
        //         return $query->where('tasks.department_assignment_id', $user_department);
        //     })
        //     ->when(auth()->user()->functional_area_id, function ($query, $user_fa) {
        //         return $query->where('events.functional_area_id', $user_fa);
        //     })
        //     ->count();

        // $total_yearly_budget = OrganizationBudget::where('type', 'year')
        //     ->whereYear('date_from', date('Y'))
        //     ->first();

        // $total_spent_by_department = Task::join('events', 'events.id', '=', 'tasks.event_id')
        //     ->join('department', 'department.id', '=', 'tasks.department_assignment_id')
        //     ->whereNull('events.archived')
        //     ->select('department.name', DB::raw("sum(tasks.actual_budget_allocated) as value"))
        //     ->whereYear('tasks.start_date', date('Y'))
        //     ->groupBy('department.name')
        //     ->when($user_department, function ($query, $user_department) {
        //         return $query->where('tasks.department_assignment_id', $user_department);
        //     })
        //     ->when(auth()->user()->functional_area_id, function ($query, $user_fa) {
        //         return $query->where('events.functional_area_id', $user_fa);
        //     })
        //     ->having('value', '>', '0')
        //     ->get();

        // $total_yearly_spent = Task::select(DB::raw("sum(tasks.actual_budget_allocated) as total_spent"))
        //     ->join('events', 'events.id', '=', 'tasks.event_id')
        //     ->whereNull('events.archived')
        //     ->whereYear('tasks.start_date', date('Y'))
        //     ->first();

        // // $completed_projects_by_month = Event::select(DB::raw('count(*) as total, date_format(end_date, "%m") as month'))
        // //     ->whereYear('end_date', date('Y'))
        // //     ->where('event_status', '=', config('tracki.project_status.completed'))
        // //     ->whereNull('archived')
        // //     ->groupBy('month')
        // //     ->get();

        // // DB::enableQueryLog();
        // $total_sales_by_month = Event::select(DB::raw('IFNULL(sum(events.total_sales), 0) count, cal.month'))
        //     ->rightJoin('cal', function ($join) {
        //         $join
        //             ->on('cal.month_num', DB::raw('date_format(end_date, "%m")'))
        //             ->whereYear('end_date', date('Y'))
        //             ->where('event_status', '=', config('tracki.project_status.completed'))
        //             ->whereNull('archived');
        //     })
        //     ->groupBy('cal.month')
        //     ->orderBy('cal.month_num')
        //     ->get();

        // $completed_projects_by_month = Event::select(DB::raw('IFNULL(count(date_format(end_date, "%m")), 0) count, cal.month'))
        //     ->rightJoin('cal', function ($join) {
        //         $join
        //             ->on('cal.month_num', DB::raw('date_format(end_date, "%m")'))
        //             ->whereYear('end_date', date('Y'))
        //             ->where('event_status', '=', config('tracki.project_status.completed'))
        //             ->whereNull('archived');
        //     })
        //     ->groupBy('cal.month')
        //     ->orderBy('cal.month_num')
        //     ->get();

        // $projects_by_month = DB::table('events')->select(DB::raw('IFNULL(count(date_format(end_date, "%m")), 0) count, cal.month'))
        //     ->rightJoin('cal', function ($join) {
        //         $join
        //             ->on('cal.month_num', DB::raw('date_format(end_date, "%m")'))
        //             ->whereYear('end_date', date('Y'))
        //             ->whereNull('archived');
        //     })
        //     ->groupBy('cal.month')
        //     ->orderBy('cal.month_num')
        //     ->get();

        // // dd(DB::getQueryLog());
        // // dd($completed_projects_by_month1);

        // $budgeted_projects_by_month = Event::select(DB::raw('IFNULL(count(date_format(start_date, "%m")), 0) count, cal.month'))
        //     ->rightJoin('cal', function ($join) {
        //         $join
        //             ->on('cal.month_num', DB::raw('date_format(start_date, "%m")'))
        //             ->whereYear('start_date', date('Y'))
        //             // ->where('event_status', '=', config('tracki.project_status.completed'))
        //             ->where('fund_category_id', '=', '1')
        //             ->whereNull('archived');
        //     })
        //     ->groupBy('cal.month')
        //     ->orderBy('cal.month_num')
        //     ->get();

        // $unbudgeted_projects_by_month = Event::select(DB::raw('IFNULL(count(date_format(start_date, "%m")), 0) count, cal.month'))
        //     ->rightJoin('cal', function ($join) {
        //         $join
        //             ->on('cal.month_num', DB::raw('date_format(start_date, "%m")'))
        //             ->whereYear('start_date', date('Y'))
        //             // ->where('event_status', '=', config('tracki.project_status.completed'))
        //             ->where('fund_category_id', '=', '2')
        //             ->whereNull('archived');
        //     })
        //     ->groupBy('cal.month')
        //     ->orderBy('cal.month_num')
        //     ->get();

        // //  dd($budgeted_projects_by_month);


        // // $fund_projects_by_month = Event::selectRaw('count(*) as total')
        // //     ->selectRaw('count(case when fund_category_id=1 then 1 end) as budgeted')
        // //     ->selectRaw('count(case when fund_category_id=2 then 1 end) as unbudgeted')
        // //     ->selectRaw('date_format(end_date, "%m") as month')
        // //     ->groupBy('month')
        // //     ->whereYear('end_date', date('Y'))
        // //     ->where('event_status', '=', config('tracki.project_status.completed'))
        // //     ->whereNull('archived')
        // //     ->get();


        // $budgeted_monthly = array();
        // $i = 0;
        // foreach ($budgeted_projects_by_month as $cp) {
        //     $budgeted_monthly[$i] = $cp->count;
        //     $i++;
        // }

        // // dd($budgeted_monthly);

        // $unbudgeted_monthly = array();
        // $i = 0;
        // foreach ($unbudgeted_projects_by_month as $cp) {
        //     $unbudgeted_monthly[$i] = $cp->count;
        //     $i++;
        // }

        // $completed_projects_by_month_array = array();
        // $i = 0;
        // foreach ($completed_projects_by_month as $cp) {
        //     $completed_projects_by_month_array[$i] = $cp->count;
        //     $i++;
        // }

        // $projects_by_month_array = array();
        // $i = 0;
        // foreach ($projects_by_month as $cp) {
        //     $projects_by_month_array[$i] = $cp->count;
        //     $i++;
        // }

        // $total_sales_by_month_array = array();
        // $i = 0;
        // foreach ($total_sales_by_month as $cp) {
        //     $total_sales_by_month_array[$i] = $cp->count;
        //     $i++;
        // }

        // if ($total_yearly_budget) {
        //     $remaining_budget = $total_yearly_budget?->budget_amount - $total_yearly_spent?->total_spent;
        //     // $total_yearly_budget->budget_amount

        //     $budget_percentage_used = ($total_yearly_spent?->total_spent / $total_yearly_budget?->budget_amount) * 100;
        // } else {
        //     $remaining_budget = 0;
        //     $budget_percentage_used = 0;
        // }

        // $todo_status_chart = Event::join('statuses', 'statuses.id', '=', 'events.event_status')
        // ->select('statuses.title as name', DB::raw("count(statuses.title) as value"))
        // ->groupBy('statuses.title')
        // ->when($workspace, function ($query, $workspace) {
        //     return $query->where('events.workspace_id', $workspace);
        // })
        // ->having('value', '>', '0')
        // ->get();

        // $project_status_chart = Event::join('statuses', 'statuses.id', '=', 'events.event_status')
        // ->select('statuses.title as name', DB::raw("count(statuses.title) as value"))
        // ->groupBy('statuses.title')
        // ->when($workspace, function ($query, $workspace) {
        //     return $query->where('events.workspace_id', $workspace);
        // })
        // ->having('value', '>', '0')
        // ->get();




        // dump(vsprintf(str_replace(['?'], ['\'%s\''], $total_sales_by_month->toSql()), $total_sales_by_month->getBindings()));

        // dd($total_sales_by_month_array);
        // dd($total_sales_by_month->getBindings());
        // dd($total_sales_by_month->toSql());

        return view('sps.admin.dashboard.index', [
            'booking_scheduled_today' => $booking_scheduled_today,
            'booking_scheduled_tomorrow' => $booking_scheduled_tomorrow,
            'total_bookings' => $total_bookings,
            'total_checked_in' => $total_checked_in,
            'total_users' => $total_users,
            // 'task_count' => $task_count,
            // 'late_tasks_count' => $late_tasks_count,
            // 'ending_tasks_count' => $ending_tasks_count,
            // 'starting_tasks_count' => $starting_tasks_count,
            // 'total_yearly_budget' => $total_yearly_budget,
            // 'total_yearly_spent' => $total_yearly_spent,
            // 'budget_percentage_used' => $budget_percentage_used,
            // 'unbudgeted_proj_count' => $unbudgeted_proj_count,
            // 'remaining_budget' => $remaining_budget,
            // 'total_spent_by_department' => $total_spent_by_department,
            // 'completed_projects_by_month' => $completed_projects_by_month_array,
            // 'projects_by_month' => $projects_by_month_array,
            // 'budgeted_projects_by_month' => $budgeted_monthly,
            // 'unbudgeted_projects_by_month' => $unbudgeted_monthly,
            // 'total_sales_by_month' => $total_sales_by_month_array,
            // 'project_status_chart' => $project_status_chart,
            // 'todo_status_chart' => $todo_status_chart,
            // 'user_workspace' => $user_workspace,
        ]);
    }  //trackiDashboard


}
