<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventReview;
use App\Models\EventSubscription;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * Service responsável pelas avaliações de eventos.
 *
 * Gerencia criação e listagem de avaliações.
 * Apenas usuários que fizeram check-in podem avaliar.
 * Apenas após a data do evento.
 * Não pode ser editada após enviada.
 */
class EventReviewService
{
    /**
     * Cria uma avaliação para um evento.
     *
     * Valida se o usuário fez check-in e se o evento já aconteceu.
     *
     * @throws ValidationException
     */
    public function store(User $user, Event $event, array $data): EventReview
    {
        // Verifica se o evento já aconteceu
        if (Carbon::parse($event->event_date)->isFuture()) {
            throw ValidationException::withMessages([
                'event' => ['Você só pode avaliar eventos que já aconteceram.'],
            ]);
        }

        // Verifica se o usuário fez check-in
        $subscription = EventSubscription::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->where('checked_in', true)
            ->first();

        if (!$subscription) {
            throw ValidationException::withMessages([
                'event' => ['Você só pode avaliar eventos em que fez check-in.'],
            ]);
        }

        // Verifica se o usuário já avaliou este evento
        $alreadyReviewed = EventReview::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyReviewed) {
            throw ValidationException::withMessages([
                'event' => ['Você já avaliou este evento.'],
            ]);
        }

        return EventReview::create([
            'event_id'           => $event->id,
            'user_id'            => $user->id,
            'stars'              => $data['stars'],
            'music_rating'       => $data['music_rating'] ?? null,
            'drink_rating'       => $data['drink_rating'] ?? null,
            'cleanliness_rating' => $data['cleanliness_rating'] ?? null,
            'comment'            => $data['comment'] ?? null,
        ]);
    }

    /**
     * Retorna as avaliações de um evento com média das notas.
     *
     * Endpoint público — qualquer visitante pode ver as avaliações.
     */
    /**
 * Retorna as avaliações de um evento com média das notas.
 *
 * Endpoint público — qualquer visitante pode ver as avaliações.
 */
    public function getEventReviews(Event $event): array
    {
        $reviews = $event->reviews()
            ->with('user:id,name,photo')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->through(fn($review) => [
                'id'                  => $review->id,
                'stars'               => $review->stars,
                'music_rating'        => $review->music_rating,
                'drink_rating'        => $review->drink_rating,
                'cleanliness_rating'  => $review->cleanliness_rating,
                'comment'             => $review->comment,
                'created_at'          => $review->created_at->format('d/m/Y H:i'),
                'user' => [
                    'name'      => $review->user->name,
                    'photo_url' => $review->user->photo_url,
                ],
            ]);

        // Calcula as médias das avaliações
        $averages = [
            'stars'              => round($event->reviews()->avg('stars'), 1),
            'music_rating'       => round($event->reviews()->avg('music_rating'), 1),
            'drink_rating'       => round($event->reviews()->avg('drink_rating'), 1),
            'cleanliness_rating' => round($event->reviews()->avg('cleanliness_rating'), 1),
            'total_reviews'      => $event->reviews()->count(),
        ];

        return [
            'averages' => $averages,
            'reviews'  => $reviews,
        ];
    }
}