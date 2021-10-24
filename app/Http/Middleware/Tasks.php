<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Tasks
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
      $tasks = app('App\Http\Controllers\TaskController')->activateNewTasks();
      \View::share('tasks', $tasks);
      return $next($request);
    }
}
