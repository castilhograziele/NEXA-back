<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Services\EventSubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Check-in
 *
 * Endpoints para gerenciamento de check-in em eventos.
 * O self-checkin pode ser feito via QR Code ou pelo botão no app.
 */
class CheckinController extends Controller
{
    /**
     * @param EventSubscriptionService $subscriptionService
     */
    public function __construct(private EventSubscriptionService $subscriptionService) {}

    /**
     * Self check-in
     *
     * O próprio usuário realiza o check-in ao escanear o QR Code
     * ou clicar no botão "Faça seu check-in" dentro do evento.
     * O benefício é liberado automaticamente após a confirmação.
     *
     * @urlParam event integer required ID do evento. Example: 3
     */
    public function selfCheckin(Request $request, Event $event): JsonResponse
    {
        $subscription = $this->subscriptionService->selfCheckin(
            $request->user(),
            $event
        );

        return response()->json([
            'success' => true,
            'data'    => [
                'subscription' => $subscription,
                'benefit'      => $event->benefit,
            ],
            'message' => 'Check-in realizado! Benefício liberado: ' . ($event->benefit ?? 'Nenhum benefício cadastrado.'),
        ]);
    }

    /**
     * Dados do check-in do usuário
     *
     * Retorna os dados para exibição na tela de resgate após o check-in.
     * Exibe nome, CPF, data de nascimento, status e benefício.
     * Usado tanto no fluxo do QR Code quanto no botão do app.
     *
     * @urlParam event integer required ID do evento. Example: 3
     */
    public function myCheckin(Request $request, Event $event): JsonResponse
    {
        $data = $this->subscriptionService->myCheckin(
            $request->user(),
            $event
        );

        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => '',
        ]);
    }

    /**
     * Confirmar check-in manualmente
     *
     * O bar_owner confirma a presença de um usuário inscrito no evento.
     * Alternativa ao self-checkin para casos especiais.
     *
     * @urlParam event integer required ID do evento. Example: 3
     * @urlParam user integer required ID do usuário. Example: 2
     */
    public function checkin(Request $request, Event $event, User $user): JsonResponse
    {
        // Verifica se o evento pertence ao bar do usuário autenticado
        if ($request->user()->bar?->id !== $event->bar_id) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Você não tem permissão para confirmar check-ins neste evento.',
            ], 403);
        }

        $subscription = $this->subscriptionService->checkin($event, $user);

        return response()->json([
            'success' => true,
            'data'    => [
                'subscription' => $subscription,
                'benefit'      => $event->benefit,
            ],
            'message' => 'Check-in confirmado. Benefício liberado: ' . ($event->benefit ?? 'Nenhum benefício cadastrado.'),
        ]);
    }

    /**
     * Listar check-ins do evento
     *
     * Retorna a lista de usuários que fizeram check-in no evento.
     * Apenas o bar_owner dono do evento tem acesso.
     *
     * @urlParam event integer required ID do evento. Example: 3
     */
    public function eventCheckins(Request $request, Event $event): JsonResponse
    {
        if ($request->user()->bar?->id !== $event->bar_id) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Você não tem permissão para ver os check-ins deste evento.',
            ], 403);
        }

        $checkins = $this->subscriptionService->eventCheckins($event);

        return response()->json([
            'success' => true,
            'data'    => $checkins,
            'message' => '',
        ]);
    }
}