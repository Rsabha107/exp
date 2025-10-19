<?php

namespace App\Http\Controllers\Chl\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Mail\SendForgotPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\ItemCategory;
use App\Models\LogicalSpaceCategory;
use App\Models\LogicalSpaceSubcategory;
use App\Models\LogicalSpaceName;
use App\Models\ItemSubcategory;
use App\Models\Product;
use App\Models\SiteCategory;
use App\Models\Site;
use App\Models\VenueType;
use App\Models\Department;
use App\Models\Setting\Event;
use App\Models\FunctionalArea;
use App\Models\Mds\FunctionalArea as MdsFunctionalArea;
use App\Models\Mds\MdsEvent;
use App\Models\OrganizationBudget;
use App\Models\Setting\Event as SettingEvent;
use App\Models\Setting\Venue;
use App\Models\Task;
use App\Notifications\EmailOtpVerification;
use Beta\Microsoft\Graph\Model\Storage as ModelStorage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Dcblogdev\MsGraph\Facades\MsGraph;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use TechEd\SimplOtp\SimplOtp;
use TechEd\SimplOtp\Models\SimplOtp as OTPModel;
use Illuminate\Support\Str;

// use Brian2694\Toastr\Facades\Toastr;


class AdminController extends Controller
{
    //
    // public function adminDashboard(){

    //     return view('admin.index');
    // }  // End method


    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/mds/auth/login');
    } // End method

    public function login()
    {
        Auth::guard('web')->logout();
        return view('mds.auth.sign-in');
    }

    public function verifyOtpAndLogin(Request $request)
    {
        Log::info($request->all());

        $user = auth()->user();
        $key = 'otp-attempts:' . $user->id;
        Log::info('AdminController::verifyOtpErrors => key: ' . $key);
        // $remaining = max(0, 5 - RateLimiter::attempts($key));

        // 1. Check if user is locked
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            Log::info('AdminController::verifyOtpErrors => tooManyAttempts: ' . $seconds);
            OTPModel::where('identifier', $user->email)->where('is_valid', true)->delete();
            $notification = [
                'message' => "Too many invalid OTP attempts.",
                'alert-type' => 'danger'
            ];
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            return redirect()->route('login')->with($notification);
        }

        // 2. Verify OTP
        $isValid = SimplOtp::validate($user->email, $request->otp);

        // $isvalid_string = $isValid->status ? 'true' : 'false';
        // Log::info('AdminController::verifyOtpErrors => isValid: ' . $isvalid_string);

        if ($isValid->status) {
            // ✅ Success: reset attempts
            RateLimiter::clear($key);
            session()->put('OTPSESSIONKEY', true);

            if (auth()->check() && session()->get('OTPSESSIONKEY')) {
                Log::info('AdminController::verifyOtpErrors => inside if');
                return redirect()->intended('/');
            }
        }

        // 3. Invalid OTP → count attempt + lock if max reached
        RateLimiter::hit($key, 100); // lock for 15 minutes

        $remaining = 5 - RateLimiter::attempts($key);

        $notification = [
            'message' => "Invalid OTP code entered. Attempts left: {$remaining}",
            'alert-type' => 'warning',
        ];
        return redirect('mds/auth/otp')->with($notification);
    }

    public function showOtp()
    {
        return view('mds.auth.otp');
    }

    public function resendOTP()
    {
        // dd( Session::all());
        $user = auth()->user();
        if (config('settings.otp_enabled')) {

            $key = Str::lower($user->id);

            // Allow 3 attempts every 5 minutes
            if (RateLimiter::tooManyAttempts($key, 3)) {
                $seconds = RateLimiter::availableIn($key);
                $minutes = floor($seconds / 60);
                $remainingSeconds = $seconds % 60;

                $timeMessage = $minutes > 0
                    ? "{$minutes} minute(s) and {$remainingSeconds} second(s)"
                    : "{$remainingSeconds} second(s)";

                $notification = array(
                    'message' => 'Too many OTP requests. Try again in ' . $timeMessage,
                    'alert-type' => 'danger'
                );

                return redirect('mds/auth/otp')->with($notification);

                return response()->json([
                    'message' => 'Too many OTP requests. Try again in ' . $seconds . ' seconds.'
                ], 429);
            }

            // Hit the rate limiter
            RateLimiter::hit($key, 300); // 300 seconds = 5 minutes


            $otp = SimplOtp::generate($user->email);
            if ($otp->status === true) {
                $details = [
                    'otp_token' => $otp->token,
                ];
                Mail::to($user->email)->send(new OtpMail($details));
                // $user->notify(new EmailOtpVerification($otp->token));
            }
            $notification = array(
                'message' => 'We have a sent a new OTP code to your email, please check',
                'alert-type' => 'success'
            );

            return redirect('mds/auth/otp')->with($notification);
            // return redirect('tracki/auth/otp')->with('message', 'OTP re-sent to your email');
        }
    }

    public function signUp()
    {
        $events = MdsEvent::all();
        $clients = MdsFunctionalArea::all();
        return view('mds.auth.sign-up', compact('events', 'clients'));
    }

    public function msSignUp()
    {
        $events = Event::all();
        $venues = Venue::all();
        // $clients = MdsFunctionalArea::all();
        $roles = Role::all();
        return view('auth.ms-sign-up', compact('events', 'venues', 'roles'));
    }

    public function forgotPassword()
    {
        return view('tracki.auth.forgot');
    }

    public function submitForgetPasswordForm(Request $request): RedirectResponse
    {
        // Log::info('inside submitForgetPasswordForm');
        $rules = [
            'email' => 'required|email|exists:users',
        ];

        $validator = Validator::make($request->all(), $rules);

        $reset_token = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email
            ])
            ->first();

        if ($reset_token) {
            DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();
        }

        if ($validator->fails()) {

            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        $token = sha1(time() . config('global.key'));

        // Log::info('token: '.$token);
        try {
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors('A reset password was already sent to your email.  please check your inbox');
            // return $e->getMessage();
        }

        // Log::info('after insert');

        $content = [
            'token'     => $token,
            'subject'   => 'Tracki: Reset Password Link',
            'url'       => "route('reset.password.get', $token)",
        ];

        Mail::to($request->email)->queue(new SendForgotPasswordMail($content));

        // Mail::send('emails.forgetPassword', ['token' => $token], function($message) use($request){
        //     $message->to($request->email);
        //     $message->subject('Reset Password');
        // });

        return back()->with('message', 'We have e-mailed your password reset link!');
    } //submitForgetPasswordForm

    public function showResetPasswordForm($token): View
    {
        return view('mds.auth.freset', ['token' => $token]);
    } //showResetPasswordForm

    public function submitResetPasswordForm(Request $request): RedirectResponse
    {
        $rules = [
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        $updatePassword = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])
            ->first();

        if (!$updatePassword) {
            // Log::info('update failed');
            return back()->withInput()->withErrors(['error' => 'Invalid token!']);
        }

        $user = User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        return redirect('/tracki/auth/login')->with('message', 'Your password has been changed!');
    } //submitResetPasswordForm

    public function createUser(Request $request)
    {

        $rules = [
            'username' => 'required|unique:users',
            'password' => 'required|confirmed|min:8|max:16',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            //return ($request->get('password').' - '.$request->get('password_confirmation'));
            //return ($request->input());
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        $activate_value = sha1(time() . config('global.key'));

        // $id = Auth::user()->id;
        $data = new User;

        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->phone = $request->phone;
        $data->department_assignment_id = $request->department_id;
        $data->password = Hash::make($request->password);
        $data->department_assignment_id = $request->department_id;
        $data->functional_area_id = $request->functional_area_id;
        $data->status = 'active';
        $data->role = 'admin';
        $data->address = 'doha';


        $data->save();

        $notification = array(
            'message'       => 'User created successfully',
            'alert-type'    => 'success'
        );

        // Toastr::success('Has been add successfully :)','Success');
        // return redirect()->back()->with($notification);
        return Redirect::route('tracki.auth.signup')->with($notification);
        //mainProfileStore

    }

    public function store(Request $request)
    {

        $rules = [
            'username' => 'required|unique:users',
            'password' => 'required|confirmed|min:8|max:16',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            //return ($request->get('password').' - '.$request->get('password_confirmation'));
            //return ($request->input());
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        $activate_value = sha1(time() . config('global.key'));

        // $id = Auth::user()->id;
        $data = new User;

        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->phone = $request->phone;
        $data->department_assignment_id = $request->department_id;
        $data->password = Hash::make($request->password);
        $data->department_assignment_id = $request->department_id;
        $data->functional_area_id = $request->functional_area_id;
        $data->status = 'active';
        $data->role = 'admin';
        $data->address = 'doha';


        $data->save();

        $notification = array(
            'message'       => 'User created successfully',
            'alert-type'    => 'success'
        );

        // Toastr::success('Has been add successfully :)','Success');
        // return redirect()->back()->with($notification);
        return Redirect::route('tracki.auth.signup')->with($notification);
        //mainProfileStore

    } // store

    public function reportList()
    {
        return view('tracki.report');
    }

    public function orderForm()
    {

        // $country_codes = DB::table('item_category')->orderBy('arabic_value', 'asc')->get();

        $venue_type = VenueType::all();
        // dd($venue_name);
        $item_category = ItemCategory::all();
        $logical_space_categories = LogicalSpaceCategory::all();
        return view('tracki.order-form', [
            'item_category'  => $item_category,
            'venue_type'    => $venue_type,
            'logical_space_categories'    => $logical_space_categories,
        ]);
    }

    public function getSiteCategory(Request $request)
    {
        $all_site_categories = SiteCategory::where('venue_type_id', $request->venue_type_id)->get();
        return response()->json([
            'status'   => 'success',
            'all_site_categories' => $all_site_categories,
        ]);
    }

    public function getSiteCode(Request $request)
    {
        $all_site_codes = Site::where('site_category_id', $request->venue_id)->get();
        return response()->json([
            'status'   => 'success',
            'all_site_codes' => $all_site_codes,
        ]);
    }

    public function getSiteData(Request $request)
    {
        $all_site_codes = Site::where('site_id', $request->site_id)->get();
        return response()->json([
            'status'   => 'success',
            'all_site_codes' => $all_site_codes,
        ]);
    }

    public function getLogicalSpaceSubcategory(Request $request)
    {
        $all_ls_subcat = LogicalSpaceSubcategory::where('category_id', $request->category_id)
            ->where('active_flag', '1')
            ->get();
        return response()->json([
            'status'   => 'success',
            'all_ls_subcat' => $all_ls_subcat,
        ]);
    }

    public function getLogicalSpaceName(Request $request)
    {
        $all_ls_name = LogicalSpaceName::where('subcat_id', $request->subcat_id)
            ->where('active_flag', '1')
            ->get();
        return response()->json([
            'status'   => 'success',
            'all_ls_name' => $all_ls_name,
        ]);
    }

    public function getLogicalSpaceCode(Request $request)
    {
        $all_ls_code = LogicalSpaceName::where('logical_space_id', $request->logical_space_id)
            ->where('active_flag', '1')
            ->get();
        return response()->json([
            'status'   => 'success',
            'all_ls_code' => $all_ls_code,
        ]);
    }

    public function getItemSubcategory(Request $request)
    {
        $all_item_subcategory = ItemSubcategory::where('item_category_id', $request->item_category_id)
            ->where('active_flag', '1')
            ->get();
        return response()->json([
            'status'   => 'success',
            'all_item_subcategory' => $all_item_subcategory,
        ]);
    }

    public function getItemName(Request $request)
    {
        $all_item_name = Product::where('item_subcat_id', $request->item_subcat_id)
            ->where('active', '1')
            ->get();
        return response()->json([
            'status'   => 'success',
            'all_item_name' => $all_item_name,
        ]);
    }

    public function userProfile()
    {
        // first get the auth user
        $id = Auth::user()->id;
        $profileData = User::find($id);

        // dd($profileData);

        return view('tracki.profile-view', compact('profileData'));
    }

    public function mainOrderStore(Request $request)
    {
        // Log::debug('*****************mainOrderStore********session exists?? 1 is ok, 0 is not' . session()->has('user_session'));

        $id = Auth::user()->id;

        $rules = [
            'site_type' => 'required|integer',
            // 'site_type' => 'required|alpha_dash|min:3|max:25',
            'site_category' => 'required|integer',
            'site_code' => 'required',
            'site_name' => 'required',
            'logical_space_category' => 'required|alpha_dash',
            'logical_space_subcategory' => 'required',
            'logical_space_name' => 'required',
            'logical_space_code' => 'required',
        ];

        // $validator = Validator::make($request->all(), $rules);



        $order = new Order;
        $order->user_id = $id;
        $order->venue_type_id = $request->site_type;
        $order->site_category_id = $request->site_category;
        $order->site_id = $request->site_code;
        $order->project_id = 102;
        // $order->languages_known = implode(',', $request->languages_known);

        $save = $order->save();

        $notification = array(
            'message'       => 'Order# ' . $order->id . ' created successfully',
            'alert-type'    => 'success'
        );

        // Toastr::success('Has been add successfully :)','Success');
        return redirect()->back()->with($notification);
        if ($order) {
            return redirect()->back()->with($notification);
        } else {
            return back()->with('fail', 'Something went wrong, try again later');
        }
    }  // mainOrderStore


    public function mainProfileStore(Request $request)
    {

        $id = Auth::user()->id;
        $data = User::find($id);

        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->phone = $request->phone;
        $data->address = $request->address;

        if ($request->file('photo')) {
            $file = $request->file('photo');
            $filename = rand() . date('ymdHis') . $file->getClientOriginalName();
            $file->move(public_path('upload/admin_images'), $filename);
            $data['photo'] = $filename;
        }

        $data->save();

        $notification = array(
            'message'       => 'Profile updated successfully',
            'alert-type'    => 'success'
        );

        // Toastr::success('Has been add successfully :)','Success');
        return redirect()->back()->with($notification);
    }  //mainProfileStore

    public function getOrderData(Request $request)
    {
        // dd('getPlannerData');
        $draw            = $request->get('draw');
        $start           = $request->get("start");
        $rowPerPage      = $request->get("length"); // total number of rows per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $order_arr       = $request->get('order');
        $search_arr      = $request->get('search');

        // dd($search_arr);
        // Log::info($draw.' '.$start.' '.$rowPerPage.' '.$columnIndex_arr.' '.$order_arr.' '.$search_arr);
        // echo $draw.' '.$start.' '.$rowPerPage;

        // Log::info('request values from getDataList: ',[$request]);

        $columnIndex     = $columnIndex_arr[0]['column']; // Column index

        // log::debug('colunmIndex: '.$columnIndex);

        $columnName      = $columnName_arr[$columnIndex]['data']; // Column name
        // log::debug('columnName: '.$columnName);

        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue     = $search_arr['value']; // Search value

        $orderDetails = DB::table('order_h');

        $totalRecords = $orderDetails
            ->join('order_item_h', 'order_h.order_id', '=', 'order_item_h.order_id')
            ->join('product', 'order_item_h.product_id', '=', 'product.product_id')
            ->join('project', 'order_h.project_id', '=', 'project.project_id')
            ->select(
                'order_h.order_id',
                'order_item_h.item_order_status',
                'project.project_name',
                'product.product_name as item_name'
            )->count();

        // Log::debug("totalRecords: " . $totalRecords);

        $totalRecordsWithFilter = $orderDetails->where(function ($query) use ($searchValue) {
            $query->join('order_item_h', 'order_h.order_id', '=', 'order_item_h.order_id');
            $query->join('product', 'order_item_h.product_id', '=', 'product.product_id');
            $query->join('project', 'order_h.project_id', '=', 'project.project_id');
            $query->select(
                'order_h.order_id',
                'order_item_h.item_order_status',
                'project.project_name',
                'product.product_name as item_name'
            );
            $query->where('order_h.order_id', 'like', '%' . $searchValue . '%');
            $query->orWhere('item_order_status', 'like', '%' . $searchValue . '%');
            $query->orWhere('project_name', 'like', '%' . $searchValue . '%');
            $query->orWhere('product_name', 'like', '%' . $searchValue . '%');
        })->count();

        // Log::debug("totalRecordsWithFilter: " . $totalRecordsWithFilter);

        $records = $orderDetails->orderBy($columnName, $columnSortOrder)
            ->where(function ($query) use ($searchValue) {
                $query->join('order_item_h', 'order_h.order_id', '=', 'order_item_h.order_id');
                $query->join('product', 'order_item_h.product_id', '=', 'product.product_id');
                $query->join('project', 'order_h.project_id', '=', 'project.project_id');
                $query->select(
                    'order_h.order_id',
                    'order_item_h.item_order_status',
                    'project.project_name',
                    'product.product_name as item_name'
                );
                $query->where('order_h.order_id', 'like', '%' . $searchValue . '%');
                $query->orWhere('item_order_status', 'like', '%' . $searchValue . '%');
                $query->orWhere('project_name', 'like', '%' . $searchValue . '%');
                $query->orWhere('product_name', 'like', '%' . $searchValue . '%');
            })
            ->skip($start)
            ->take($rowPerPage)
            ->get();

        // Log::debug("records: ".$records);

        $data_arr = [];
        // $records = $orderDetails;

        foreach ($records as $key => $record) {

            if ($record->item_order_status == '1') {
                $status = '<td><span class="badge badge-phoenix badge-phoenix-success">Approved</span></td>';
            } else {
                $status = '<td><span class="badge badge-phoenix badge-phoenix-warning">Rejected</span></td>';
            }

            $hidden_id = '<td hidden class="user_id">' . $record->order_id . '</td>';

            $modify = '
                <td class="text-end">
                    <div class="actions">
                        <a href="#" class="btn btn-sm bg-danger-light">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <a class="btn btn-sm bg-danger-light delete user_id" data-bs-toggle="modal" data-user_id="' . $record->order_id . '" data-bs-target="#plannerDelete">
                        <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                </td>
            ';

            $data_arr[] = [
                "order_id"         => $record->order_id,
                "status"        => $status, //$record->item_order_status,
                "project_name"  => $record->project_name,
                "item"          => $record->item_name,
                // "active_flag"       => $status,
                "modify"        => $modify,
            ];
        }

        $response = [
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordsWithFilter,
            "aaData"               => $data_arr
        ];

        // Log::info('request values from getDataList: ',[response()->json($response)]);
        // dd(response()->json($response));
        return response()->json($response);
    }  //getPlannerData

    public function getProjectData(Request $request)
    {
        // dd('getPlannerData');
        $draw            = $request->get('draw');
        $start           = $request->get("start");
        $rowPerPage      = $request->get("length"); // total number of rows per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $order_arr       = $request->get('order');
        $search_arr      = $request->get('search');

        // dd($search_arr);
        // Log::info($draw.' '.$start.' '.$rowPerPage.' '.$columnIndex_arr.' '.$order_arr.' '.$search_arr);
        // echo $draw.' '.$start.' '.$rowPerPage;

        // Log::info('request values from getDataList: ',[$request]);

        $columnIndex     = $columnIndex_arr[0]['column']; // Column index

        // log::debug('colunmIndex: '.$columnIndex);

        $columnName      = $columnName_arr[$columnIndex]['data']; // Column name
        // log::debug('columnName: '.$columnName);

        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue     = $search_arr['value']; // Search value

        $orderDetails = DB::table('order_h');

        $totalRecords = $orderDetails
            ->join('order_item_h', 'order_h.order_id', '=', 'order_item_h.order_id')
            ->join('product', 'order_item_h.product_id', '=', 'product.product_id')
            ->join('project', 'order_h.project_id', '=', 'project.project_id')
            ->select(
                'order_h.order_id',
                'order_item_h.item_order_status',
                'project.project_name',
                'product.product_name as item_name'
            )->count();

        // Log::debug("totalRecords: " . $totalRecords);

        $totalRecordsWithFilter = $orderDetails->where(function ($query) use ($searchValue) {
            $query->join('order_item_h', 'order_h.order_id', '=', 'order_item_h.order_id');
            $query->join('product', 'order_item_h.product_id', '=', 'product.product_id');
            $query->join('project', 'order_h.project_id', '=', 'project.project_id');
            $query->select(
                'order_h.order_id',
                'order_item_h.item_order_status',
                'project.project_name',
                'product.product_name as item_name'
            );
            $query->where('order_h.order_id', 'like', '%' . $searchValue . '%');
            $query->orWhere('item_order_status', 'like', '%' . $searchValue . '%');
            $query->orWhere('project_name', 'like', '%' . $searchValue . '%');
            $query->orWhere('product_name', 'like', '%' . $searchValue . '%');
        })->count();

        // Log::debug("totalRecordsWithFilter: " . $totalRecordsWithFilter);

        $records = $orderDetails->orderBy($columnName, $columnSortOrder)
            ->where(function ($query) use ($searchValue) {
                $query->join('order_item_h', 'order_h.order_id', '=', 'order_item_h.order_id');
                $query->join('product', 'order_item_h.product_id', '=', 'product.product_id');
                $query->join('project', 'order_h.project_id', '=', 'project.project_id');
                $query->select(
                    'order_h.order_id',
                    'order_item_h.item_order_status',
                    'project.project_name',
                    'product.product_name as item_name'
                );
                $query->where('order_h.order_id', 'like', '%' . $searchValue . '%');
                $query->orWhere('item_order_status', 'like', '%' . $searchValue . '%');
                $query->orWhere('project_name', 'like', '%' . $searchValue . '%');
                $query->orWhere('product_name', 'like', '%' . $searchValue . '%');
            })
            ->skip($start)
            ->take($rowPerPage)
            ->get();

        // Log::debug("records: ".$records);

        $data_arr = [];
        // $records = $orderDetails;

        foreach ($records as $key => $record) {

            if ($record->item_order_status == '1') {
                $status = '<td><span class="badge badge-phoenix badge-phoenix-success">Approved</span></td>';
            } else {
                $status = '<td><span class="badge badge-phoenix badge-phoenix-warning">Rejected</span></td>';
            }

            $hidden_id = '<td hidden class="user_id">' . $record->order_id . '</td>';

            $modify = '
                <td class="text-end">
                    <div class="actions">
                        <a href="#" class="btn btn-sm bg-danger-light">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <a class="btn btn-sm bg-danger-light delete user_id" data-bs-toggle="modal" data-user_id="' . $record->order_id . '" data-bs-target="#plannerDelete">
                        <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                </td>
            ';

            $data_arr[] = [
                "order_id"         => $record->order_id,
                "status"        => $status, //$record->item_order_status,
                "project_name"  => $record->project_name,
                "item"          => $record->item_name,
                // "active_flag"       => $status,
                "modify"        => $modify,
            ];
        }

        $response = [
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordsWithFilter,
            "aaData"               => $data_arr
        ];

        // Log::info('request values from getDataList: ',[response()->json($response)]);
        // dd(response()->json($response));
        return response()->json($response);
    }  //getPlannerData


}  // end of class
