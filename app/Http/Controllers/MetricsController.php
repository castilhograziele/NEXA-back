<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\MetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Métricas
 *
 * Endpoints para visualização de métricas do bar e eventos.
 * Apenas usuários com role bar_owner têm acesso.
 */
class MetricsController extends Controller
{
    public function __construct(private MetricsService $metricsService) {}

    /**
     * Métricas do bar
     *
     * Retorna um resumo de visualizações e inscrições de todos os eventos do bar.
     */
    public function barMetrics(Request $request): JsonResponse
    {
        $metrics = $this->metricsService->barMetrics($request->user());

        return response()->json([
            'success' => true,
            'data'    => $metrics,
            'message' => '',
        ]);
    }

    /**
     * Métricas do evento
     *
     * Retorna métricas detalhadas de um evento, incluindo lista de inscritos.
     * Apenas o dono do bar pode ver as métricas do seu evento.
     */
    public function eventMetrics(Request $request, Event $event): JsonResponse
    {
        if ($request->user()->bar?->id !== $event->bar_id) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Você não tem permissão para ver estas métricas.',
            ], 403);
        }

        $metrics = $this->metricsService->eventMetrics($event);

        return response()->json([
            'success' => true,
            'data'    => $metrics,
            'message' => '',
        ]);
    }
}