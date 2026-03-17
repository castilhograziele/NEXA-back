<?php

namespace App\Http\Controllers;

use App\Models\Bar;
use App\Models\BarPhoto;
use App\Services\BarPhotoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Fotos do Bar
 *
 * Endpoints para upload e gestão de fotos do bar.
 * Foto de perfil disponível para todos os planos.
 * Galeria de fotos exclusiva para plano premium.
 */
class BarPhotoController extends Controller
{
    /**
     * @param BarPhotoService $barPhotoService
     */
    public function __construct(private BarPhotoService $barPhotoService) {}

    /**
     * Upload de foto de perfil
     *
     * Faz upload da foto de perfil do bar.
     * Substitui a foto anterior se existir.
     * Disponível para todos os planos.
     *
     * @urlParam bar integer required ID do bar. Example: 1
     */
    public function uploadProfilePhoto(Request $request, Bar $bar): JsonResponse
    {
        // Verifica se o bar pertence ao usuário autenticado
        if ($request->user()->bar?->id !== $bar->id) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Você não tem permissão para editar este bar.',
            ], 403);
        }

        $request->validate([
            'photo' => ['required', 'file', 'max:5120'],
        ]);

        $bar = $this->barPhotoService->uploadProfilePhoto($bar, $request->file('photo'));

        return response()->json([
            'success' => true,
            'data'    => [
                'photo_url' => $bar->photo_url,
            ],
            'message' => 'Foto de perfil atualizada com sucesso.',
        ]);
    }

    /**
     * Adicionar foto na galeria
     *
     * Adiciona uma foto na galeria de ambiente do bar.
     * Apenas bares com plano premium podem usar a galeria.
     * Limite de 10 fotos por galeria.
     *
     * @urlParam bar integer required ID do bar. Example: 1
     */
    public function addGalleryPhoto(Request $request, Bar $bar): JsonResponse
    {
        // Verifica se o bar pertence ao usuário autenticado
        if ($request->user()->bar?->id !== $bar->id) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Você não tem permissão para editar este bar.',
            ], 403);
        }

        $request->validate([
            'photo'   => ['required', 'file', 'max:5120'],
            'caption' => ['nullable', 'string', 'max:255'],
        ]);

        $photo = $this->barPhotoService->addGalleryPhoto(
            $bar,
            $request->file('photo'),
            $request->input('caption')
        );

        return response()->json([
            'success' => true,
            'data'    => [
                'id'      => $photo->id,
                'url'     => $photo->url,
                'caption' => $photo->caption,
                'order'   => $photo->order,
            ],
            'message' => 'Foto adicionada à galeria com sucesso.',
        ]);
    }

    /**
     * Remover foto da galeria
     *
     * Remove uma foto da galeria do bar.
     * Apenas o dono do bar pode remover fotos.
     *
     * @urlParam bar integer required ID do bar. Example: 1
     * @urlParam photo integer required ID da foto. Example: 1
     */
    public function removeGalleryPhoto(Request $request, Bar $bar, BarPhoto $photo): JsonResponse
    {
        // Verifica se o bar pertence ao usuário autenticado
        if ($request->user()->bar?->id !== $bar->id) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Você não tem permissão para editar este bar.',
            ], 403);
        }

        $this->barPhotoService->removeGalleryPhoto($bar, $photo);

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'Foto removida da galeria com sucesso.',
        ]);
    }

    /**
     * Listar fotos da galeria
     *
     * Retorna todas as fotos da galeria do bar.
     * Endpoint público — qualquer visitante pode ver.
     *
     * @urlParam bar integer required ID do bar. Example: 1
     */
    public function listGalleryPhotos(Bar $bar): JsonResponse
    {
        $photos = $bar->photos->map(fn($photo) => [
            'id'      => $photo->id,
            'url'     => $photo->url,
            'caption' => $photo->caption,
            'order'   => $photo->order,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $photos,
            'message' => '',
        ]);
    }
}