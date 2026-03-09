<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\EventSubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Inscrições
 *
 * Endpoints para inscrição e cancelamento em eventos.
 */
class EventSubscriptionController extends Controller
{
    public function __construct(private EventSubscriptionService $subscriptionService) {}

    /**
     * Inscrever em evento
     *
     * Inscreve o usuário autenticado em um evento.
     */
    public function subscribe(Request $request, Event $event): JsonResponse
    {
        $subscription = $this->subscriptionService->subscribe($request->user(), $event);

        return response()->json([
            'success' => true,
            'data'    => $subscription,
            'message' => 'Inscrição realizada com sucesso.',
        ], 201);
    }

    /**
     * Cancelar inscrição
     *
     * Cancela a inscrição do usuário autenticado em um evento.
     */
    public function unsubscribe(Request $request, Event $event): JsonResponse
    {
        $this->subscriptionService->unsubscribe($request->user(), $event);

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'Inscrição cancelada com sucesso.',
        ]);
    }

    /**
     * Minhas inscrições
     *
     * Retorna a lista paginada de eventos em que o usuário está inscrito.
     */
    public function mySubscriptions(Request $request): JsonResponse
    {
        $subscriptions = $this->subscriptionService->userSubscriptions($request->user());

        return response()->json([
            'success' => true,
            'data'    => $subscriptions,
            'message' => '',
        ]);
    }
}