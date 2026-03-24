<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Perfil do Usuário
 *
 * Endpoints para gerenciamento do perfil do usuário comum.
 * Todos os endpoints exigem autenticação.
 */
class UserController extends Controller
{
    /**
     * @param UserService $userService
     */
    public function __construct(private UserService $userService) {}

    /**
     * Dados do perfil
     *
     * Retorna os dados completos do perfil do usuário autenticado.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                    => $user->id,
                'name'                  => $user->name,
                'phone'                 => $user->phone,
                'email'                 => $user->email,
                'cpf'                   => $user->cpf,
                'birth_date'            => $user->birth_date?->format('d/m/Y'),
                'city'                  => $user->city,
                'photo_url'             => $user->photo_url,
                'notify_new_events'     => $user->notify_new_events,
                'notify_event_reminder' => $user->notify_event_reminder,
                'roles'                 => $user->getRoleNames(),
            ],
            'message' => '',
        ]);
    }

    /**
     * Atualizar perfil
     *
     * Atualiza os dados do perfil do usuário autenticado.
     * Apenas os campos enviados serão atualizados.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'name'  => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'city'  => ['nullable', 'string', 'max:255'],
        ]);

        $user = $this->userService->updateProfile(
            $request->user(),
            $request->only(['name', 'email', 'city'])
        );

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                    => $user->id,
                'name'                  => $user->name,
                'phone'                 => $user->phone,
                'email'                 => $user->email,
                'city'                  => $user->city,
                'photo_url'             => $user->photo_url,
                'notify_new_events'     => $user->notify_new_events,
                'notify_event_reminder' => $user->notify_event_reminder,
            ],
            'message' => 'Perfil atualizado com sucesso.',
        ]);
    }
    /**
     * Upload de foto de perfil
     *
     * Faz upload da foto de perfil do usuário.
     * Substitui a foto anterior se existir.
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => ['required', 'file', 'max:5120'],
        ]);

        $user = $this->userService->uploadPhoto(
            $request->user(),
            $request->file('photo')
        );

        return response()->json([
            'success' => true,
            'data'    => [
                'photo_url' => $user->photo_url,
            ],
            'message' => 'Foto de perfil atualizada com sucesso.',
        ]);
    }

    /**
     * Histórico de eventos
     *
     * Retorna os eventos que o usuário frequentou (check-in confirmado).
     * Paginado em 10 por página.
     */
    public function eventHistory(Request $request): JsonResponse
    {
        $history = $this->userService->eventHistory($request->user());

        return response()->json([
            'success' => true,
            'data'    => $history,
            'message' => '',
        ]);
    }

    /**
     * Preferências de notificação
     *
     * Atualiza as preferências de notificação do usuário.
     */
    public function updateNotifications(Request $request): JsonResponse
    {
        $request->validate([
            'notify_new_events'     => ['sometimes', 'boolean'],
            'notify_event_reminder' => ['sometimes', 'boolean'],
        ]);

        $user = $this->userService->updateNotifications(
            $request->user(),
            $request->only(['notify_new_events', 'notify_event_reminder'])
        );

        return response()->json([
            'success' => true,
            'data'    => [
                'notify_new_events'     => $user->notify_new_events,
                'notify_event_reminder' => $user->notify_event_reminder,
            ],
            'message' => 'Preferências de notificação atualizadas.',
        ]);
    }

    /**
     * Preferências de categoria
     *
     * Retorna as categorias de eventos mais frequentadas pelo usuário.
     * Útil para personalizar recomendações no frontend.
     */
    public function categoryPreferences(Request $request): JsonResponse
    {
        $preferences = $this->userService->categoryPreferences($request->user());

        return response()->json([
            'success' => true,
            'data'    => $preferences,
            'message' => '',
        ]);
    }

    /**
 * Eventos recomendados
 *
 * Retorna eventos recomendados baseados nas categorias que o usuário mais frequentou.
 * Se o usuário não tiver histórico, retorna os eventos em destaque.
 */
/**
 * Eventos recomendados
 *
 * Retorna eventos recomendados baseados nas categorias que o usuário mais frequentou.
 * Se o usuário não tiver histórico, retorna os eventos em destaque.
 */
    public function recommendedEvents(Request $request): JsonResponse
    {
        $events = $this->userService->recommendedEvents($request->user());

        // Formata a resposta removendo campos desnecessários
        $events->through(fn($event) => [
            'id'             => $event->id,
            'title'          => $event->title,
            'description'    => $event->description,
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

        return response()->json([
            'success' => true,
            'data'    => $events,
            'message' => '',
        ]);
    }

}