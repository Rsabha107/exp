<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Notifications\NewUserNotification;
use Symfony\Component\HttpFoundation\Response;

class CheckFirstLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('CheckFirstLoginMiddleware');
        Log::info('Request: ', $request->all());
        if (auth()->user()->first_login_flag) {
            // dd ('first_login_flag');
            Log::info('first_login_flag');
            $user = auth()->user();
            $token = $user->first_login_token;
            // $token = Str::random(60);
            // $h_token = Hash::make($token);

            Log::info('token: ' . $user->first_login_token);
            Log::info('user: ' . $user->email);

            // $details = [
            //     'name' => $user->name,
            //     'email' => $user->email,
            //     'token' => $user->h_token,
            // ];

            // Log::info('before insert token: '.$token);
            // DB::table('password_reset_tokens')->insert([
            //     'email' => $user->email, // $request->email,
            //     'token' => Hash::make($token),
            //     'created_at' => Carbon::now()
            // ]);

            // Log::info('after insert token: '.$token);
            // $user->notify(new NewUserNotification($details));

            return redirect()->route('auth.first.reset', $token)
                ->with('message', 'An email was sent you with your unique token.');
        }

        return $next($request);
    }
}
