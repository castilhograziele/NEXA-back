<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use App\Models\EventSubscription;
use Illuminate\Validation\ValidationException;

class EventSubscriptionService
{
    // Inscreve o usuário em um evento
    public function subscribe(User $user, Event $event): EventSubscription
    {
        // Verifica se o evento está ativo
        if (!$event->is_active) {
            throw ValidationException::withMessages([
                'event' => ['Este evento não está disponível para inscrições.'],
            ]);
        }

        // Verifica se o usuário já está inscrito
        $already = EventSubscription::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($already) {
            throw ValidationException::withMessages([
                'event' => ['Você já está inscrito neste evento.'],
            ]);
        }

        // Cria a inscrição
        return EventSubscription::create([
            'event_id' => $event->id,
            'user_id'  => $user->id,
        ]);
    }

    // Cancela a inscrição do usuário em um evento
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

    // Lista os eventos em que o usuário está inscrito
    public function userSubscriptions(User $user)
    {
        return $user->subscribedEvents()
                    ->where('is_active', true)
                    ->orderBy('event_date')
                    ->paginate(10);
    }
}