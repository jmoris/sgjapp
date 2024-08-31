<?php

use App\Tenant;
use Illuminate\Support\Facades\Auth;

if (!function_exists('active_class')) {
    function active_class($path, $active = 'active')
    {
        return call_user_func_array('Request::is', (array)$path) ? $active : '';
    }
}

if (!function_exists('is_active_route')) {
    function is_active_route($path)
    {
        return call_user_func_array('Request::is', (array)$path) ? 'true' : 'false';
    }
}

if (!function_exists('show_class')) {
    function show_class($path)
    {
        return call_user_func_array('Request::is', (array)$path) ? 'show' : '';
    }
}

if (!function_exists('has_permission')) {
    function has_permission($tag)
    {
        $tenant = Tenant::current();
        if ($tenant == null) {
            return false;
        }

        $user = Auth::user();
        $userRol = $user->rol->permisos()->where('tag', $tag)->first();

        if ($user != null && $userRol != null){
            return true;
        }
        return false;
    }
}
