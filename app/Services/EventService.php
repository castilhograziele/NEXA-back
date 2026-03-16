<?php

namespace App\Services;

use App\Models\Bar;
use App\Models\Event;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class EventService
{
    /**
     * Lista eventos públicos ativos com filtros opcionais.
     *
     * Suporta filtro por categoria, cidade do bar e data.
     * O filtro de data é usado para a seção "Esta noite" no frontend.
     */
    public function list(array $filters): LengthAwarePaginator
    {
        $query = Event::with('bar')
            ->where('is_active', true);

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['city'])) {
            $query->whereHas('bar', function ($q) use ($filters) {
                $q->where('city', $filters['city']);
            });
        }

        if (!empty($filters['date'])) {
            $query->where('event_date', $filters['date']);
        }

        return $query->orderBy('event_date')->paginate(10);
    }

    /**
     * Retorna eventos marcados como destaque.
     *
     * Usado para a seção "Não pode perder" no frontend.
     * Apenas bares premium podem marcar eventos como destaque.
     */
    public function featured(): LengthAwarePaginator
    {
        return Event::with('bar')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('event_date')
            ->paginate(10);
    }

    /**
     * Verifica se o bar atingiu o limite mensal de eventos.
     *
     * Plano free: máximo 2 eventos ativos por mês.
     * Plano premium: sem limite (retorna null).
     *
     * @throws ValidationException
     */
    private function checkMonthlyLimit(Bar $bar): void
    {
        $limit = $bar->monthlyEventLimit();

        if ($limit === null) {
            return;
        }

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

    /**
     * Verifica se o bar pode usar funcionalidades exclusivas do plano premium.
     *
     * Funcionalidades premium: destaque no evento e link do Spotify.
     *
     * @throws ValidationException
     */
    private function checkPremiumFeatures(Bar $bar, array $data): void
    {
        if (!empty($data['is_featured']) && $data['is_featured'] === true) {
            if (!$bar->isPremium()) {
                throw ValidationException::withMessages([
                    'is_featured' => ['Apenas bares com plano premium podem destacar eventos.'],
                ]);
            }
        }

        if (!empty($data['spotify_url'])) {
            if (!$bar->isPremium()) {
                throw ValidationException::withMessages([
                    'spotify_url' => ['Apenas bares com plano premium podem adicionar playlist do Spotify.'],
                ]);
            }
        }
    }

    /**
     * Cria um novo evento vinculado ao bar.
     *
     * Valida o limite mensal e funcionalidades premium antes de criar.
     *
     * @throws ValidationException
     */
    public function store(Bar $bar, array $data): Event
    {
        $this->checkMonthlyLimit($bar);
        $this->checkPremiumFeatures($bar, $data);

        return $bar->events()->create($data);
    }

    /**
     * Atualiza os dados de um evento existente.
     *
     * Valida funcionalidades premium antes de atualizar.
     *
     * @throws ValidationException
     */
    public function update(Event $event, array $data, Bar $bar): Event
    {
        $this->checkPremiumFeatures($bar, $data);

        $event->update($data);

        return $event;
    }

    /**
     * Desativa um evento sem removê-lo do banco.
     *
     * Usado como soft delete lógico — o evento continua existindo
     * mas não aparece nas listagens públicas.
     */
    public function destroy(Event $event): void
    {
        $event->update(['is_active' => false]);
    }
}