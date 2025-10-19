<?php

namespace App\Http\Controllers;

use adevesa\SimpleOTP\Models\SimpleOTP;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\SendForgotPasswordMail;
use App\Mail\SendOTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Department;
use App\Models\FunctionalArea;
use App\Models\PasswordResetToken;
use App\Notifications\EmailOtpVerification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use TechEd\SimplOtp\SimplOtp;

class AdminController extends Controller
{

    public function login()
    {
        Auth::guard('web')->logout();
        return view('auth.sign-in');
    }

    public function resendOTP()
    {
        // dd( Session::all());
        $user = auth()->user();
        if (config('settings.otp_enabled')) {

            $otp = SimplOtp::generate($user->email);
            if($otp->status === true){
                $user->notify(new EmailOtpVerification($otp->token));
            }
            $notification = array(
                'message' => 'We have a sent a new OTP to your email.',
                'alert-type' => 'success'
            );

            return redirect('auth/otp')->with($notification);
            // return redirect('tracki/auth/otp')->with('message', 'OTP re-sent to your email');
        }
    }

    public function verifyOtpAndLogin(Request $request)
    {
        Log::info($request->all());
        // $maxAttempts = (int) config('simple-otp.otp_max_attempts');
        // $otp_attempts = SimpleOTP::where('identity', auth()->user()->email)
        //     ->where('validated_at', null)
        //     ->latest()
        //     ->first();

        // if($otp_attempts->attempts >= $maxAttempts){
        //     $notification = array(
        //         'message' => 'Max attempts reached',
        //         'alert-type' => 'error'
        //     );
        //     return redirect('/tracki/auth/signin')->with($notification);
        //     // return redirect('tracki/auth/otp')->with($notification);
        // };

        $user = auth()->user();

        // $isValid = SimpleOTP::verify(auth()->user()->email, $request->otp);
        $isValid = SimplOtp::validate($user->email, $request->otp);
        // dd($isValid);
        if ($isValid->status) {
            session()->put('OTPSESSIONKEY', true);
        }

        $isvalid_string = $isValid ? 'true' : 'false';

        Log::info('AdminController::verifyOtpErrors => isValid: ' . $isvalid_string);
        if (auth()->check() && session()->get('OTPSESSIONKEY')) {
            Log::info('AdminController::verifyOtpErrors => inside if');
            return redirect()->intended('/');
        } else {
            $notification = array(
                'message' => 'Invalid OTP code Entered',
                'alert-type' => 'error'
            );
            return redirect('auth/otp')->with($notification);
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/auth/login');
    } // End method

    public function logoutxx(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        session()->put('OTPSESSIONKEY', false);

        return redirect()->route('auth.signin');
        // return view('tracki.auth.sign-in');
    } // End method

    public function signIn(Request $request)
    {

        // dd(
        //     $_SERVER,
        //     substr($_SERVER['APP_URL'],0,strpos($_SERVER['APP_URL'], ':',5)-1),
        //     request()->ip(),
        //     request()->ips(),
        //     request()->isFromTrustedProxy(),
        //     request()->getClientIp(true),
        //     config('tracki.white_list_ip_address'),
        // );

        Auth::guard('web')->logout();
        return view('auth.sign-in');
    }

    public function showOtp()
    {
        return view('auth.otp');
    }

    public function signUp()
    {
        $departments = Department::all();
        $fa = FunctionalArea::all();
        return view('tracki.auth.sign-up', compact(['departments', 'fa']));
    }

    public function forgotPassword()
    {
        return view('auth.forgot');
    }

    public function submitForgetPasswordForm(Request $request): RedirectResponse
    {

        $rules = [
            'email' => 'required|email|exists:users',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        $token = sha1(time() . config('global.key'));

        try {
            PasswordResetToken::where('email', $request->email)->delete();
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

    public function showResetPasswordForm($token)
    {
        $ptoken = PasswordResetToken::where('token', $token)
            ->where('created_at', '>', Carbon::now()->subHours(1)->toDateTimeString())
            ->first();

        // dd($ptoken);
        if ($ptoken) {
            return view('auth.reset', ['token' => $token]);
        } else {
            return redirect()->route('auth.login')
                ->withInput()
                ->withErrors(__('traki.link_expired'));
        }
    } //showResetPasswordForm

    public function submitResetPasswordForm(Request $request): RedirectResponse
    {
        $rules = [
            'email' => 'required|email|exists:users',
            'password' => ['required', 'confirmed', Password::defaults()],
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
            return back()->withInput()->withErrors(['error' => 'Invalid token!']);
        }

        $user = User::where('email', $request->email)
            ->update([
                'password' => Hash::make($request->password),
                'status' => 1
            ]);

        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        return redirect()->route('auth.login')->with('message', 'Your password has been changed!');
    } //submitResetPasswordForm

    public function createUser(Request $request)
    {

        $rules = [
            'username' => 'required|unique:users',
            'password' => 'required|confirmed|min:8|max:16',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

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
        $data->role = 'user';
        $data->address = 'doha';


        $data->save();

        $notification = array(
            'message'       => 'User created successfully',
            'alert-type'    => 'success'
        );

        // Toastr::success('Has been add successfully :)','Success');
        // return redirect()->back()->with($notification);
        return Redirect::route('auth.signup')->with($notification);
        //mainProfileStore

    }

    public function userProfile()
    {
        // first get the auth user
        $id = Auth::user()->id;
        $profileData = User::find($id);

        // dd($profileData);

        return view('tracki.profile-view', compact('profileData'));
    }


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



}  // end of class
