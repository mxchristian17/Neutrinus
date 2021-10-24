<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Reminders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $reminders = app('App\Http\Controllers\ReminderController')->activateNewReminders();
        \View::share('reminders', $reminders);
        return $next($request);
    }
}
