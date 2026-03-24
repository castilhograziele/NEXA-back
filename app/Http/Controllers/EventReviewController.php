<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\EventReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Avaliações de Eventos
 *
 * Endpoints para avaliação de eventos pelo usuário.
 * Apenas usuários que fizeram check-in podem avaliar.
 * Apenas após a data do evento.
 */
class EventReviewController extends Controller
{
    /**
     * @param EventReviewService $reviewService
     */
    public function __construct(private EventReviewService $reviewService) {}

    /**
     * Avaliar evento
     *
     * Cria uma avaliação para um evento.
     * Apenas usuários que fizeram check-in podem avaliar.
     * Apenas após a data do evento.
     * Não pode ser editada após enviada.
     *
     * @urlParam event integer required ID do evento. Example: 1
     */
    public function store(Request $request, Event $event): JsonResponse
    {
        $request->validate([
            'stars'              => ['required', 'integer', 'min:1', 'max:5'],
            'music_rating'       => ['nullable', 'integer', 'min:1', 'max:5'],
            'drink_rating'       => ['nullable', 'integer', 'min:1', 'max:5'],
            'cleanliness_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'comment'            => ['nullable', 'string', 'max:200'],
        ]);

        $review = $this->reviewService->store(
            $request->user(),
            $event,
            $request->only([
                'stars',
                'music_rating',
                'drink_rating',
                'cleanliness_rating',
                'comment',
            ])
        );

        return response()->json([
            'success' => true,
            'data'    => $review,
            'message' => 'Avaliação enviada com sucesso. Obrigado pelo feedback!',
        ], 201);
    }

    /**
     * Avaliações do evento
     *
     * Retorna as avaliações de um evento com média das notas.
     * Endpoint público — qualquer visitante pode ver.
     *
     * @urlParam event integer required ID do evento. Example: 1
     * @unauthenticated
     */
    public function index(Event $event): JsonResponse
    {
        $data = $this->reviewService->getEventReviews($event);

        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => '',
        ]);
    }
}