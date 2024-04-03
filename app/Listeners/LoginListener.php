<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cookie;

class LoginListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        /*/** @var User $authUser */
        /*$authUser = $event->user;

        Cookie::forget('tenant');
        Cookie::queue(Cookie::forever('tenant', encrypt($authUser->tenant_id)));*/
    }
}
