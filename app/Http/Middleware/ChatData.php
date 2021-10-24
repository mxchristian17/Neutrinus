<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class ChatData
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
        $contacts = User::where('id', '!=', auth()->user()->id)->get();
        foreach($contacts as $key => $contact)
        {
          if(!$contact->permissionUseChat->state) unset($contacts[$key]);
        }
        
        \View::share('chatUsers', $contacts);
        return $next($request);
    }
}
