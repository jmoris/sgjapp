<?php

namespace App\Http\Middleware;

use App\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$tags): Response
    {
        $tenant = Tenant::current();
        if($tenant==null){
            return redirect()->back();
        }

        // obtenemos el usuario identificado
        $user = Auth::user();
        $userRol = $user->rol->permisos()->whereIn('tag', $tags)->first();
        if($user!=null&&$userRol!=null){
            return $next($request);
        }
        return redirect()->back();
    }
}
