<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventSubscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
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

    /**
     * Confirma o check-in de um usuário em um evento.
     *
     * Apenas o bar_owner dono do evento pode confirmar check-ins.
     * O benefício é liberado após a confirmação.
     *
     * @throws ValidationException
     */
    public function checkin(Event $event, User $user): EventSubscription
    {
        $subscription = EventSubscription::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$subscription) {
            throw ValidationException::withMessages([
                'user' => ['Este usuário não está inscrito neste evento.'],
            ]);
        }

        if ($subscription->isCheckedIn()) {
            throw ValidationException::withMessages([
                'user' => ['Este usuário já fez check-in neste evento.'],
            ]);
        }

        // Registra o check-in com data e hora atual
        $subscription->update([
            'checked_in'    => true,
            'checked_in_at' => now(),
        ]);

        return $subscription->fresh();
    }

    /**
     * Retorna a lista de check-ins confirmados de um evento.
     *
     * Inclui dados do usuário para facilitar a identificação na entrada.
     */
    public function eventCheckins(Event $event): Collection
    {
        return $event->subscriptions()
                     ->with('user:id,name,phone')
                     ->where('checked_in', true)
                     ->orderBy('checked_in_at')
                     ->get();
    }

    /**
     * Realiza o check-in do próprio usuário autenticado.
     *
     * Chamado quando o usuário escaneia o QR Code ou clica no botão
     * "Faça seu check-in" dentro do evento no app.
     *
     * @throws ValidationException
     */
    public function selfCheckin(User $user, Event $event): EventSubscription
    {
        $subscription = EventSubscription::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$subscription) {
            throw ValidationException::withMessages([
                'event' => ['Você não está inscrito neste evento.'],
            ]);
        }

        if ($subscription->isCheckedIn()) {
            throw ValidationException::withMessages([
                'event' => ['Você já realizou o check-in neste evento.'],
            ]);
        }

        $subscription->update([
            'checked_in'    => true,
            'checked_in_at' => now(),
        ]);

        return $subscription->fresh();
    }

    /**
     * Retorna os dados do check-in do usuário autenticado para a tela de resgate.
     *
     * Exibe nome, CPF, data de nascimento, status e benefício.
     * Usado tanto no fluxo do QR Code quanto no botão do app.
     *
     * @throws ValidationException
     */
    public function myCheckin(User $user, Event $event): array
    {
        $subscription = EventSubscription::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$subscription) {
            throw ValidationException::withMessages([
                'event' => ['Você não está inscrito neste evento.'],
            ]);
        }

        return [
            'user' => [
                'name'       => $user->name,
                'cpf'        => $user->cpf,
                'birth_date' => $user->birth_date?->format('d/m/Y'),
            ],
            'checkin' => [
                'status'        => $subscription->isCheckedIn() ? 'confirmado' : 'pendente',
                'checked_in_at' => $subscription->checked_in_at?->format('d/m/Y H:i'),
            ],
            'event' => [
                'title'   => $event->title,
                'benefit' => $event->benefit ?? 'Nenhum benefício cadastrado.',
            ],
        ];
    }
}