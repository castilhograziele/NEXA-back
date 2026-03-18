<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Service responsável pelos dados da dashboard do bar_owner.
 *
 * Consolida em uma única chamada todos os dados necessários
 * para renderizar a dashboard: bar, eventos, métricas e check-ins recentes.
 */
class DashboardService
{
    /**
     * Retorna todos os dados consolidados da dashboard do bar_owner.
     *
     * Inclui dados do bar, eventos ativos, métricas do mês atual
     * e os check-ins mais recentes.
     */
    public function getDashboardData(User $user): array
    {
        $bar = $user->bar;

        if (!$bar) {
            return [
                'bar'           => null,
                'events'        => [],
                'metrics'       => null,
                'recent_checkins' => [],
            ];
        }

        // Eventos ativos do bar ordenados por data
        $events = $bar->events()
            ->where('is_active', true)
            ->orderBy('event_date')
            ->get()
            ->map(fn($event) => [
                'id'             => $event->id,
                'title'          => $event->title,
                'event_date'     => $event->event_date,
                'event_time'     => $event->event_time,
                'category'       => $event->category,
                'is_featured'    => $event->is_featured,
                'age_restriction'=> $event->age_restriction,
                'benefit'        => $event->benefit,
                'poster_url'     => $event->poster_url,
                'subscriptions'  => $event->subscriptions()->count(),
                'views'          => $event->views()->count(),
                'checkins'       => $event->subscriptions()->where('checked_in', true)->count(),
            ]);

        // Métricas do mês atual
        $mesAtual = Carbon::now();
        $eventosDoMes = $bar->events()
            ->whereMonth('created_at', $mesAtual->month)
            ->whereYear('created_at', $mesAtual->year)
            ->where('is_active', true)
            ->count();

        $metrics = [
            'plano'              => $bar->plan,
            'limite_mensal'      => $bar->monthlyEventLimit(),
            'eventos_no_mes'     => $eventosDoMes,
            'total_views_mes'    => $bar->events()
                ->with('views')
                ->get()
                ->sum(fn($event) => $event->views()
                    ->whereMonth('created_at', $mesAtual->month)
                    ->whereYear('created_at', $mesAtual->year)
                    ->count()),
            'total_inscricoes_mes' => $bar->events()
                ->with('subscriptions')
                ->get()
                ->sum(fn($event) => $event->subscriptions()
                    ->whereMonth('created_at', $mesAtual->month)
                    ->whereYear('created_at', $mesAtual->year)
                    ->count()),
            'total_checkins_mes' => $bar->events()
                ->with('subscriptions')
                ->get()
                ->sum(fn($event) => $event->subscriptions()
                    ->where('checked_in', true)
                    ->whereMonth('checked_in_at', $mesAtual->month)
                    ->whereYear('checked_in_at', $mesAtual->year)
                    ->count()),
        ];

        // Últimos 5 check-ins realizados
        $recentCheckins = collect();
        foreach ($bar->events as $event) {
            $checkins = $event->subscriptions()
                ->with('user:id,name,phone')
                ->where('checked_in', true)
                ->orderByDesc('checked_in_at')
                ->limit(5)
                ->get()
                ->map(fn($sub) => [
                    'event_title'   => $event->title,
                    'user_name'     => $sub->user->name,
                    'user_phone'    => $sub->user->phone,
                    'checked_in_at' => $sub->checked_in_at?->format('d/m/Y H:i'),
                ]);
            $recentCheckins = $recentCheckins->merge($checkins);
        }

        return [
            'bar' => [
                'id'           => $bar->id,
                'name'         => $bar->name,
                'razao_social' => $bar->razao_social,
                'cnpj'         => $bar->cnpj,
                'phone'        => $bar->phone,
                'city'         => $bar->city,
                'address'      => $bar->address,
                'instagram'    => $bar->instagram,
                'whatsapp'     => $bar->whatsapp,
                'description'  => $bar->description,
                'plan'         => $bar->plan,
                'photo_url'    => $bar->photo_url,
            ],
            'events'          => $events,
            'metrics'         => $metrics,
            'recent_checkins' => $recentCheckins->sortByDesc('checked_in_at')->values()->take(5),
        ];
    }
}