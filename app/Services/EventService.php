<?php

namespace App\Services;

use App\Models\Bar;
use App\Models\Event;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

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

        // Filtro por data — usado para "Esta noite"
        if (!empty($filters['date'])) {
            $query->where('event_date', $filters['date']);
        }

        return $query->orderBy('event_date')->paginate(10);
    }

    // Retorna eventos destacados — "Não pode perder"
    public function featured(): LengthAwarePaginator
    {
        return Event::with('bar')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('event_date')
            ->paginate(10);
    }

    // Verifica se o bar atingiu o limite de eventos do mês
    private function checkMonthlyLimit(Bar $bar): void
    {
        $limit = $bar->monthlyEventLimit();

        // null significa ilimitado (premium)
        if ($limit === null) {
            return;
        }

        // Conta eventos ativos criados no mês atual
        $count = $bar->events()
            ->where('is_active', true)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        if ($count >= $limit) {
            throw ValidationException::withMessages([
                'events' => ["Você atingiu o limite de {$limit} eventos ativos por mês do plano free. Faça upgrade para o plano premium para criar eventos ilimitados."],
            ]);
        }
    }

    // Verifica se o bar pode usar funcionalidades premium
    private function checkPremiumFeatures(Bar $bar, array $data): void
    {
        // Apenas premium pode destacar eventos
        if (!empty($data['is_featured']) && $data['is_featured'] === true) {
            if (!$bar->isPremium()) {
                throw ValidationException::withMessages([
                    'is_featured' => ['Apenas bares com plano premium podem destacar eventos.'],
                ]);
            }
        }

        // Apenas premium pode adicionar link do Spotify
        if (!empty($data['spotify_url'])) {
            if (!$bar->isPremium()) {
                throw ValidationException::withMessages([
                    'spotify_url' => ['Apenas bares com plano premium podem adicionar playlist do Spotify.'],
                ]);
            }
        }
    }

    // Cria um novo evento vinculado ao bar do usuário
    public function store(Bar $bar, array $data): Event
    {
        // Verifica limite mensal de eventos
        $this->checkMonthlyLimit($bar);

        // Verifica funcionalidades premium
        $this->checkPremiumFeatures($bar, $data);

        return $bar->events()->create($data);
    }

    // Atualiza um evento existente
    public function update(Event $event, array $data, Bar $bar): Event
    {
        // Verifica funcionalidades premium
        $this->checkPremiumFeatures($bar, $data);

        $event->update($data);

        return $event;
    }

    // Desativa um evento (soft delete lógico)
    public function destroy(Event $event): void
    {
        $event->update(['is_active' => false]);
    }
}