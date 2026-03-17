<?php

namespace App\Services;

use App\Models\Bar;
use App\Models\BarPhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

/**
 * Service responsável pelo upload e gestão de fotos do bar.
 *
 * Gerencia tanto a foto de perfil quanto a galeria de ambiente.
 */
class BarPhotoService
{
    /**
     * Faz upload da foto de perfil do bar.
     *
     * Remove a foto anterior se existir antes de salvar a nova.
     * Salva no diretório bars/profiles no storage público.
     */
    public function uploadProfilePhoto(Bar $bar, UploadedFile $file): Bar
    {
        // Remove foto anterior se existir
        if ($bar->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($bar->photo);
        }

        // Salva a nova foto no storage
        $path = $file->store('bars/profiles', 'public');

        $bar->update(['photo' => $path]);

        return $bar->fresh();
    }

    /**
     * Adiciona uma foto na galeria do bar.
     *
     * Apenas bares premium podem ter galeria de fotos.
     * Limite de 10 fotos por galeria.
     *
     * @throws ValidationException
     */
public function addGalleryPhoto(Bar $bar, UploadedFile $file, ?string $caption = null): BarPhoto
{
    // Apenas premium pode ter galeria
    if (!$bar->isPremium()) {
        throw ValidationException::withMessages([
            'photo' => ['Apenas bares com plano premium podem adicionar fotos na galeria.'],
        ]);
    }

    // Limite de 10 fotos na galeria
    $count = $bar->photos()->count();
    if ($count >= 10) {
        throw ValidationException::withMessages([
            'photo' => ['Limite de 10 fotos na galeria atingido.'],
        ]);
    }

    // Salva a foto no storage
    $path = $file->store('bars/gallery', 'public');

    // Log temporário para debug
    \Log::info('Tentando criar BarPhoto', [
        'bar_id'  => $bar->id,
        'path'    => $path,
        'caption' => $caption,
        'count'   => $count,
    ]);

    $photo = BarPhoto::create([
        'bar_id'  => $bar->id,
        'path'    => $path,
        'caption' => $caption,
        'order'   => $count + 1,
    ]);

    \Log::info('BarPhoto criado', ['photo' => $photo->toArray()]);

    return $photo;
}
    /**
     * Remove uma foto da galeria do bar.
     *
     * Verifica se a foto pertence ao bar antes de remover.
     *
     * @throws ValidationException
     */
    public function removeGalleryPhoto(Bar $bar, BarPhoto $photo): void
    {
        // Verifica se a foto pertence ao bar
        if ($photo->bar_id !== $bar->id) {
            throw ValidationException::withMessages([
                'photo' => ['Esta foto não pertence ao seu bar.'],
            ]);
        }

        // Remove do storage e do banco
        \Illuminate\Support\Facades\Storage::disk('public')->delete($photo->path);
        $photo->delete();
    }
}