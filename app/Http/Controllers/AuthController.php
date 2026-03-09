<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Autenticação
 *
 * Endpoints para registro, login e logout de usuários.
 */
class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    /**
     * Registrar usuário
     *
     * Cria um novo usuário e retorna o token de acesso.
     *
     * @unauthenticated
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'success' => true,
            'data'    => $result,
            'message' => 'Usuário cadastrado com sucesso.',
        ], 201);
    }

    /**
     * Login
     *
     * Autentica o usuário e retorna o token de acesso.
     *
     * @unauthenticated
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return response()->json([
            'success' => true,
            'data'    => $result,
            'message' => 'Login realizado com sucesso.',
        ]);
    }

    /**
     * Logout
     *
     * Revoga todos os tokens do usuário autenticado.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'Logout realizado com sucesso.',
        ]);
    }

    /**
     * Usuário autenticado
     *
     * Retorna os dados do usuário autenticado.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $request->user(),
            'message' => '',
        ]);
    }
}