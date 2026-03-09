<?php

namespace App\Services;

use App\Models\Bar;
use App\Models\Event;
use Illuminate\Pagination\LengthAwarePaginator;

class EventService
{
    // Lista eventos públicos com filtros opcionais
    public function list(array $filters): LengthAwarePaginator
    {
        $query = Event::with('bar')
            ->where('is_active', true);

        // Filtro por categoria
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        // Filtro por cidade (via bar)
        if (!empty($filters['city'])) {
            $query->whereHas('bar', function ($q) use ($filters) {
                $q->where('city', $filters['city']);
            });
        }

        // Filtro por data
        if (!empty($filters['date'])) {
            $query->where('event_date', $filters['date']);
        }

        return $query->orderBy('event_date')->paginate(10);
    }

    // Cria um novo evento vinculado ao bar do usuário
    public function store(Bar $bar, array $data): Event
    {
        return $bar->events()->create($data);
    }

    // Atualiza um evento existente
    public function update(Event $event, array $data): Event
    {
        $event->update($data);

        return $event;
    }

    // Desativa um evento (soft delete lógico)
    public function destroy(Event $event): void
    {
        $event->update(['is_active' => false]);
    }
}