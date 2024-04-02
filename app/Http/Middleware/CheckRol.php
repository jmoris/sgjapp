<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        // obtenemos el usuario identificado
        $user = auth()->user();
        $userRol = auth()->user()->rol->permisos()->whereIn('tag', $tags)->first();
        if($userRol!=null){
            return $next($request);
        }
        return redirect()->back();
    }
}
