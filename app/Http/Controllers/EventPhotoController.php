<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\EventPhotoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Cartaz do Evento
 *
 * Endpoints para upload e remoção do cartaz de divulgação do evento.
 * Exclusivo para bares com plano premium.
 */
class EventPhotoController extends Controller
{
    /**
     * @param EventPhotoService $eventPhotoService
     */
    public function __construct(private EventPhotoService $eventPhotoService) {}

    /**
     * Upload do cartaz
     *
     * Faz upload do cartaz de divulgação do evento.
     * Substitui o cartaz anterior se existir.
     * Exclusivo para bares com plano premium.
     *
     * @urlParam event integer required ID do evento. Example: 1
     */
    public function uploadPoster(Request $request, Event $event): JsonResponse
    {
        // Verifica se o evento pertence ao bar do usuário autenticado
        if ($request->user()->bar?->id !== $event->bar_id) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Você não tem permissão para editar este evento.',
            ], 403);
        }

        $request->validate([
            'poster' => ['required', 'file', 'max:5120'],
        ]);

        $event = $this->eventPhotoService->uploadPoster($event, $request->file('poster'));

        return response()->json([
            'success' => true,
            'data'    => [
                'poster_url' => $event->poster_url,
            ],
            'message' => 'Cartaz do evento atualizado com sucesso.',
        ]);
    }

    /**
     * Remover cartaz
     *
     * Remove o cartaz de divulgação do evento.
     * Apenas o dono do bar pode remover.
     *
     * @urlParam event integer required ID do evento. Example: 1
     */
    public function removePoster(Request $request, Event $event): JsonResponse
    {
        // Verifica se o evento pertence ao bar do usuário autenticado
        if ($request->user()->bar?->id !== $event->bar_id) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Você não tem permissão para editar este evento.',
            ], 403);
        }

        $this->eventPhotoService->removePoster($event);

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'Cartaz removido com sucesso.',
        ]);
    }
}