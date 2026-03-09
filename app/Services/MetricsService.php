<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventView;
use App\Models\User;
use Illuminate\Support\Collection;

class MetricsService
{
    // Registra uma visualização no evento
    public function recordView(Event $event, ?User $user, string $ip): void
    {
        // Evita contar a mesma visualização do mesmo ip no mesmo evento
        $already = EventView::where('event_id', $event->id)
            ->where('ip_address', $ip)
            ->exists();

        if (!$already) {
            EventView::create([
                'event_id'   => $event->id,
                'user_id'    => $user?->id,
                'ip_address' => $ip,
            ]);
        }
    }

    // Retorna métricas completas de um evento
    public function eventMetrics(Event $event): array
    {
        return [
            'event_id'           => $event->id,
            'title'              => $event->title,
            'total_views'        => $event->views()->count(),
            'total_subscriptions'=> $event->subscriptions()->count(),
            'subscribers'        => $event->subscriptions()
                                        ->with('user:id,name,email')
                                        ->get()
                                        ->map(fn($s) => $s->user),
        ];
    }

    // Retorna métricas de todos os eventos do bar
    public function barMetrics(User $user): Collection
    {
        $bar = $user->bar;

        return $bar->events()
            ->withCount(['views', 'subscriptions'])
            ->get()
            ->map(fn($event) => [
                'event_id'            => $event->id,
                'title'               => $event->title,
                'event_date'          => $event->event_date,
                'is_active'           => $event->is_active,
                'total_views'         => $event->views_count,
                'total_subscriptions' => $event->subscriptions_count,
            ]);
    }
}