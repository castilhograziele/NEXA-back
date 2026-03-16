<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventSubscription;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class EventSubscriptionService
{
    /**
     * Inscreve o usuário em um evento.
     *
     * Valida se o evento está ativo, se o usuário tem idade suficiente
     * e se ainda não está inscrito antes de criar a inscrição.
     *
     * @throws ValidationException
     */
    public function subscribe(User $user, Event $event): EventSubscription
    {
        if (!$event->is_active) {
            throw ValidationException::withMessages([
                'event' => ['Este evento não está disponível para inscrições.'],
            ]);
        }

        // Bloqueia inscrição se o usuário não tiver a idade mínima exigida
        if (!$event->isAgeAllowed($user)) {
            throw ValidationException::withMessages([
                'event' => [
                    $event->age_restriction === '18'
                        ? 'Este evento é disponível apenas para maiores de 18 anos.'
                        : 'Este evento é disponível apenas para maiores de 21 anos.',
                ],
            ]);
        }

        $already = EventSubscription::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($already) {
            throw ValidationException::withMessages([
                'event' => ['Você já está inscrito neste evento.'],
            ]);
        }

        return EventSubscription::create([
            'event_id' => $event->id,
            'user_id'  => $user->id,
        ]);
    }

    /**
     * Cancela a inscrição do usuário em um evento.
     *
     * @throws ValidationException
     */
    public function unsubscribe(User $user, Event $event): void
    {
        $subscription = EventSubscription::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$subscription) {
            throw ValidationException::withMessages([
                'event' => ['Você não está inscrito neste evento.'],
            ]);
        }

        $subscription->delete();
    }

    /**
     * Retorna os eventos em que o usuário está inscrito, paginados.
     */
    public function userSubscriptions(User $user): LengthAwarePaginator
    {
        return $user->subscribedEvents()
                    ->where('is_active', true)
                    ->orderBy('event_date')
                    ->paginate(10);
    }
}