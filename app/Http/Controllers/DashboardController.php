<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Dashboard
 *
 * Endpoint consolidado da dashboard do bar_owner.
 * Retorna todos os dados necessários em uma única chamada.
 */
class DashboardController extends Controller
{
    /**
     * @param DashboardService $dashboardService
     */
    public function __construct(private DashboardService $dashboardService) {}

    /**
     * Dashboard do bar_owner
     *
     * Retorna em uma única chamada todos os dados da dashboard:
     * dados do bar, eventos ativos, métricas do mês e check-ins recentes.
     * Apenas bar_owners autenticados têm acesso.
     */
    public function index(Request $request): JsonResponse
    {
        // Verifica se o usuário é bar_owner
        if (!$request->user()->hasRole('bar_owner')) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Apenas donos de bar têm acesso à dashboard.',
            ], 403);
        }

        $data = $this->dashboardService->getDashboardData($request->user());

        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => '',
        ]);
    }
}