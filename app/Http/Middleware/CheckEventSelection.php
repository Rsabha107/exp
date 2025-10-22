<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckEventSelection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('CheckEventSelection');
        if (config('mds.check_event_selection')) {
            // Log::info('CheckEventSelection: Checking event selection: '. session()->has('EVENT_ID'));
            if (!session()->has('EVENT_ID') && auth()->check()) {
                if (auth()->user()->hasRole('SuperAdmin')) {
                    return redirect()->route('chl.admin.pick');
                } elseif (auth()->user()->hasRole('VenueAdmin')) {
                    return redirect()->route('chl.venue.admin.pick');
                } else {
                    Log::info('CheckEventSelection: Redirecting to pick event');
                    return redirect()->route('chl.venue.admin.pick');
                }
            }
        }
        return $next($request);
    }
}
