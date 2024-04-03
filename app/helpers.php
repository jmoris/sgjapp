<?php

use App\Tenant;
use Illuminate\Support\Facades\Auth;

function active_class($path, $active = 'active') {
  return call_user_func_array('Request::is', (array)$path) ? $active : '';
}

function is_active_route($path) {
  return call_user_func_array('Request::is', (array)$path) ? 'true' : 'false';
}

function show_class($path) {
  return call_user_func_array('Request::is', (array)$path) ? 'show' : '';
}

function has_permission($tag){

    $tenant = Tenant::current();
    if($tenant==null){
        return false;
    }

    $user = Auth::user();
    $userRol = $user->rol->permisos()->where('tag', $tag)->first();
    if($user!=null&&$userRol!=null)
        return true;

    return false;
}
