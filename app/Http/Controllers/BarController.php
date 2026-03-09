<?php

namespace App\Http\Controllers;

use App\Models\Bar;
use App\Services\BarService;
use App\Http\Requests\Bar\StoreBarRequest;
use App\Http\Requests\Bar\UpdateBarRequest;
use Illuminate\Http\JsonResponse;

/**
 * @group Bars
 *
 * Endpoints para cadastro e gestão de bares.
 */
class BarController extends Controller
{
    public function __construct(private BarService $barService) {}

    /**
     * Cadastrar bar
     *
     * Cria um novo bar vinculado ao usuário autenticado.
     * Apenas usuários com role bar_owner podem cadastrar um bar.
     */
    public function store(StoreBarRequest $request): JsonResponse
    {
        $bar = $this->barService->store($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'data'    => $bar,
            'message' => 'Bar cadastrado com sucesso.',
        ], 201);
    }

    /**
     * Detalhes do bar
     *
     * Retorna os dados de um bar específico.
     *
     * @unauthenticated
     */
    public function show(Bar $bar): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $bar,
            'message' => '',
        ]);
    }

    /**
     * Atualizar bar
     *
     * Atualiza os dados do bar. Apenas o dono pode editar.
     */
    public function update(UpdateBarRequest $request, Bar $bar): JsonResponse
    {
        $bar = $this->barService->update($bar, $request->validated());

        return response()->json([
            'success' => true,
            'data'    => $bar,
            'message' => 'Bar atualizado com sucesso.',
        ]);
    }
}