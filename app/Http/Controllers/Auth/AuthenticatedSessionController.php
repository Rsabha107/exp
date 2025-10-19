<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use TechEd\SimplOtp\SimplOtp;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use App\Models\Event;
use App\Models\User;
use App\Notifications\EmailOtpVerification;
use Laravel\Socialite\Facades\Socialite;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        Log::info('AuthenticatedSessionController:create');
        return view('auth.sign-in');
    }

    /**
     * Handle an incoming authentication request.
     */

    public function store(LoginRequest $request)
    // : RedirectResponse
    {
        Log::info('AuthenticatedSessionController:store');
        Log::info($request);

        session()->forget('OTPSESSIONKEY');

        $request->authenticate();

        // $user = User::find(Auth::user()->id);
        $user =  auth()->user();

        $request->session()->regenerate();

        Auth::logoutOtherDevices($request->password);

        Log::info('AuthenticatedSessionController:store user: ' . $user);
        Log::info('settings.enable_otp: ' . config('settings.enable_otp'));
        if (config('settings.otp_enabled')) {
            // $this->showOtp();
            // $simpleOTP = new SimpleOTP();
            // $code = $simpleOTP->create(auth()->user()->email);
            $otp = SimplOtp::generate(auth()->user()->email);
            if ($otp->status === true) {
                $user->notify(new EmailOtpVerification($otp->token));
            }
            return view('auth/otp');
        }

        // //set the default workspace as set during user creation
        // session()->put('workspace_id', $request->user()->workspace_id);
        // Log::info('AuthenticatedSessionController:store workspace_id: '.$request->user()->workspace_id);

        // Log::info($request->authenticate());
        // Log::info($request->user()->role);
        $url = '';
        if ($request->user()->hasRole('SuperAdmin')) {
            Log::info('AuthenticatedSessionController:store user is admin');
            $url = '/chl/admin/tasks';
            return redirect()->intended($url);
        } elseif ($request->user()->hasRole('VenueAdmin')) {
            Log::info('AuthenticatedSessionController:store user is not admin');
            $url = '/chl/venue-admin/tasks';
            return redirect()->intended($url);
        }

        // if ($request->user()->role === 'admin'){
        //     $url = 'mds/admin/booking';
        //     return redirect()->intended($url);
        // } elseif  ($request->user()->role === 'user'){
        //     $url = 'mds/customer/booking';
        //     return redirect()->intended($url);
        // }

        // return back()->withErrors([
        //     'email' => 'Username and password don\'t match.',
        // ])->onlyInput('email');
        Log::info('AuthenticatedSessionController:store url: ' . $url);

        return redirect()->intended($url);
        // return redirect()->intended(RouteServiceProvider::HOME);.
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Log::info('AuthenticatedSessionController:destroy');
        $provider = $request->user()->provider;

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($provider === 'local') {
            appLog('Local login - redirecting to login page');
            session()->forget('login_method');
            return redirect('/');
        }

        session()->forget('login_method');
        $microsoftLogoutUrl = Socialite::driver('microsoft')->getLogoutUrl(route('login')); // Replace 'azure' with your Microsoft Socialite driver name if different, and 'login' with your desired redirect URI after Microsoft logout.
        Log::info('Redirecting to Microsoft logout URL: ' . $microsoftLogoutUrl);
        return redirect($microsoftLogoutUrl);

        // return redirect('/');
    }
}
