<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Service responsável pelo upload do cartaz do evento.
 *
 * O cartaz é a imagem de divulgação do evento (flyer).
 * Disponível para todos os planos.
 */
class EventPhotoService
{
    /**
     * Faz upload do cartaz do evento.
     *
     * Remove o cartaz anterior se existir antes de salvar o novo.
     * Disponível para todos os planos.
     * Salva no diretório events/posters no storage público.
     */
    public function uploadPoster(Event $event, UploadedFile $file): Event
    {
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