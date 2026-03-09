<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\EventService;
use App\Services\MetricsService;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Events
 *
 * Endpoints para listagem e gestão de eventos.
 */
class EventController extends Controller
{
    public function __construct(
        private EventService $eventService,
        private MetricsService $metricsService
    ) {}

    /**
     * Listar eventos
     *
     * Retorna a lista paginada de eventos ativos com filtros opcionais.
     *
     * @unauthenticated
     * @queryParam category string Filtrar por categoria. Example: samba
     * @queryParam city string Filtrar por cidade do bar. Example: Porto Alegre
     * @queryParam date string Filtrar por data (formato Y-m-d). Example: 2026-04-15
     */
    public function index(Request $request): JsonResponse
    {
        $events = $this->eventService->list($request->only([
            'category',
            'city',
            'date',
        ]));

        return response()->json([
            'success' => true,
            'data'    => $events,
            'message' => '',
        ]);
    }

    /**
     * Detalhes do evento
     *
     * Retorna os dados de um evento e registra a visualização.
     *
     * @unauthenticated
     */
    public function show(Request $request, Event $event): JsonResponse
    {
        $this->metricsService->recordView(
            $event,
            $request->user(),
            $request->ip()
        );

        return response()->json([
            'success' => true,
            'data'    => $event->load('bar'),
            'message' => '',
        ]);
    }

    /**
     * Criar evento
     *
     * Cria um novo evento vinculado ao bar do usuário autenticado.
     * Apenas usuários com role bar_owner podem criar eventos.
     */
    public function store(StoreEventRequest $request): JsonResponse
    {
        $bar = $request->user()->bar;

        $event = $this->eventService->store($bar, $request->validated());

        return response()->json([
            'success' => true,
            'data'    => $event,
            'message' => 'Evento criado com sucesso.',
        ], 201);
    }

    /**
     * Atualizar evento
     *
     * Atualiza os dados de um evento. Apenas o dono do bar pode editar.
     */
    public function update(UpdateEventRequest $request, Event $event): JsonResponse
    {
        $event = $this->eventService->update($event, $request->validated());

        return response()->json([
            'success' => true,
            'data'    => $event,
            'message' => 'Evento atualizado com sucesso.',
        ]);
    }

    /**
     * Desativar evento
     *
     * Desativa um evento sem deletá-lo do banco. Apenas o dono do bar pode desativar.
     */
    public function destroy(UpdateEventRequest $request, Event $event): JsonResponse
    {
        $this->eventService->destroy($event);

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'Evento desativado com sucesso.',
        ]);
    }
}