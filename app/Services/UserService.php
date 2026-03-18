<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Service responsável pelo gerenciamento do perfil do usuário.
 *
 * Gerencia edição de dados pessoais, foto de perfil,
 * preferências de notificação e histórico de eventos.
 */
class UserService
{
    /**
     * Atualiza os dados do perfil do usuário.
     *
     * Apenas os campos enviados serão atualizados.
     */
    public function updateProfile(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }

    /**
     * Faz upload da foto de perfil do usuário.
     *
     * Remove a foto anterior se existir antes de salvar a nova.
     * Salva no diretório users/profiles no storage público.
     */
    public function uploadPhoto(User $user, UploadedFile $file): User
    {
        // Remove foto anterior se existir
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        // Salva a nova foto no storage
        $path = $file->store('users/profiles', 'public');

        $user->update(['photo' => $path]);

        return $user->fresh();
    }

    /**
     * Retorna o histórico de eventos frequentados pelo usuário.
     *
     * Apenas eventos onde o usuário fez check-in são retornados.
     */
/**
 * Retorna o histórico de eventos frequentados pelo usuário.
 *
 * Apenas eventos onde o usuário fez check-in são retornados.
 */
    public function eventHistory(User $user)
    {
        return $user->subscribedEvents()
                    ->wherePivot('checked_in', true)
                    ->with('bar:id,name,city,photo')
                    ->orderByPivot('checked_in_at', 'desc')
                    ->paginate(10)
                    ->through(fn($event) => [
                        'id'             => $event->id,
                        'title'          => $event->title,
                        'event_date'     => $event->event_date,
                        'event_time'     => $event->event_time,
                        'category'       => $event->category,
                        'age_restriction'=> $event->age_restriction,
                        'benefit'        => $event->benefit,
                        'poster_url'     => $event->poster_url,
                        'bar' => [
                            'id'        => $event->bar->id,
                            'name'      => $event->bar->name,
                            'city'      => $event->bar->city,
                            'photo_url' => $event->bar->photo_url,
                        ],
                    ]);
    }

    /**
     * Atualiza as preferências de notificação do usuário.
     */
    public function updateNotifications(User $user, array $data): User
    {
        $user->update([
            'notify_new_events'     => $data['notify_new_events'] ?? $user->notify_new_events,
            'notify_event_reminder' => $data['notify_event_reminder'] ?? $user->notify_event_reminder,
        ]);

        return $user->fresh();
    }

    /**
     * Retorna as preferências de categoria do usuário.
     * 
     * Baseado nos eventos que o usuário frequentou,
     * retorna as categorias mais acessadas.
     */
    public function categoryPreferences(User $user): array
    {
        $categories = $user->subscribedEvents()
            ->wherePivot('checked_in', true)
            ->get()
            ->groupBy('category')
            ->map(fn($events) => $events->count())
            ->sortDesc()
            ->toArray();

        return $categories;
    }

    /**
 * Retorna eventos recomendados baseados nas preferências do usuário.
 *
 * Busca eventos ativos nas categorias que o usuário mais frequentou.
 * Exclui eventos que o usuário já está inscrito.
 * Se o usuário não tiver histórico, retorna os eventos em destaque.
 */
        /**
 * Retorna eventos recomendados baseados nas preferências do usuário.
 *
 * Busca eventos ativos nas categorias que o usuário mais frequentou.
 * Exclui eventos que o usuário já está inscrito.
 * Se o usuário não tiver histórico, retorna os eventos em destaque.
 */
public function recommendedEvents(User $user): \Illuminate\Pagination\LengthAwarePaginator
{
    $categories = array_keys($this->categoryPreferences($user));

    // Se não tiver histórico, retorna eventos em destaque
    if (empty($categories)) {
        return \App\Models\Event::with('bar:id,name,city,photo')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('event_date')
            ->paginate(10);
    }

    // IDs dos eventos que o usuário já está inscrito
    $subscribedIds = $user->subscribedEvents()->pluck('events.id');

    return \App\Models\Event::with('bar:id,name,city,photo')
        ->where('is_active', true)
        ->whereIn('category', $categories)
        ->whereNotIn('id', $subscribedIds)
        ->orderByRaw("CASE " . implode(' ', array_map(
            fn($cat, $i) => "WHEN category = '{$cat}' THEN {$i}",
            $categories,
            array_keys($categories)
        )) . " ELSE 999 END")
        ->orderBy('event_date')
        ->paginate(10);
}
}