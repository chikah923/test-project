<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use View;

class CheckName
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
        $userName = Auth::user();
        if (isset($userName)) {
           view()->share([
               'username' => $userName->name,
               'email' => $userName->email
           ]);
        }
        return $next($request);
    }
}
