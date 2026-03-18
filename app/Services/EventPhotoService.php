<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Service responsável pelo upload do cartaz do evento.
 *
 * O cartaz é a imagem de divulgação do evento (flyer).
 * Apenas bares com plano premium podem adicionar cartaz.
 */
class EventPhotoService
{
    /**
     * Faz upload do cartaz do evento.
     *
     * Remove o cartaz anterior se existir antes de salvar o novo.
     * Apenas bares premium podem adicionar cartaz.
     * Salva no diretório events/posters no storage público.
     *
     * @throws ValidationException
     */
    public function uploadPoster(Event $event, UploadedFile $file): Event
    {
        // Apenas premium pode adicionar cartaz
        if (!$event->bar->isPremium()) {
            throw ValidationException::withMessages([
                'poster' => ['Apenas bares com plano premium podem adicionar cartaz ao evento.'],
            ]);
        }

        // Remove cartaz anterior se existir
        if ($event->poster) {
            Storage::disk('public')->delete($event->poster);
        }

        // Salva o novo cartaz no storage
        $path = $file->store('events/posters', 'public');

        $event->update(['poster' => $path]);

        return $event->fresh();
    }

    /**
     * Remove o cartaz do evento.
     *
     * Remove do storage e limpa o campo no banco.
     */
    public function removePoster(Event $event): void
    {
        if ($event->poster) {
            Storage::disk('public')->delete($event->poster);
            $event->update(['poster' => null]);
        }
    }
}