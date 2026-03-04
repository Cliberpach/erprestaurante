<?php

namespace App\Http\Controllers\Tenant\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Api\AlertApp;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Spatie\Multitenancy\Models\Tenant;
use Throwable;

class NotificationController extends Controller
{
    /**
     * 🔔 Listar notificaciones del tenant (paginado)
     */
    public function getNotifications(Request $request)
    {
        $page     = (int) $request->get('page', 1);
        $perPage = 20;

        $notifications = AlertApp::query()
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page);

        $formatted = collect($notifications->items())->map(function ($alert) {
            return [
                'id'          => $alert->id,
                'type'        => $alert->type,
                'content'     => $alert->content,
                'sent_at'     => $alert->sent_at,
                'created_at'  => $alert->created_at,
                'time_ago'    => $this->timeAgo($alert->created_at),
                'icon'        => [
                    'icon' => 'fi fi-rr-bell',
                    'bgClass' => 'bg-primary'
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'notifications' => $formatted,
            'current_page'  => $notifications->currentPage(),
            'per_page'      => $notifications->perPage(),
            'total'         => $notifications->total(),
            'has_more'      => $notifications->hasMorePages(),
        ]);
    }

    /**
     * 🔢 Contador de notificaciones
     */
    public function getNotificationsCount()
    {
        return response()->json([
            'success' => true,
            'count' => AlertApp::count()
        ]);
    }

    /**
     * 👀 Marcar como vista (FRONTEND-ONLY)
     * (opcional – no persiste por usuario)
     */
    public function markAsRead($id)
    {
        // 🔥 No hacemos nada en BD porque es global
        return response()->json([
            'success' => true,
            'message' => 'Notificación marcada como vista (frontend)'
        ]);
    }

    /**
     * ⏱ Helper tiempo humano
     */
    private function timeAgo($date): string
    {
        $date = Carbon::parse($date);

        if ($date->isToday()) {
            return 'Hoy';
        }

        if ($date->isYesterday()) {
            return 'Ayer';
        }

        return 'Hace ' . $date->diffInDays(now()) . ' días';
    }
}
