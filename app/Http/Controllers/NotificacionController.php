<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    public function getAll(){
        $user = Auth::user();
        $notificaciones = $user->unreadNotifications;

        return response()->json([
            'success' => true,
            'count' => count($notificaciones),
            'data' => $notificaciones
        ]);
    }

    public function marcarNotificaciones(){
        $user = Auth::user();
        $notificaciones = $user->unreadNotifications;
        foreach($notificaciones as $notificacion){
            $notificacion->markAsRead();
        }

        return response()->json([
            'success' => true,
            'msg' => 'Notificaciones marcadas como leidas correctamente'
        ]);
    }
}
