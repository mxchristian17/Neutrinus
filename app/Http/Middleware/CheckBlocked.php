<?php

namespace App\Http\Middleware;
use Closure;
class CheckBlocked
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
       if (auth()->check())
       {
       if (date('Y-m-d H:i:s') < auth()->user()->blocked_date) {
          $blocked_days = now()->diffInDays(auth()->user()->blocked_date);
          $message = 'Your account has been blocked.';
          auth()->logout();
          return redirect()->route('login')->withMessage($message);
         }
        }
      return $next($request);
    }
}
