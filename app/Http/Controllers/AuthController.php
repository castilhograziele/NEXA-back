<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Autenticação
 *
 * Endpoints para registro, login e logout via OTP por SMS.
 */
class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    /**
     * Registrar usuário
     *
     * Cria um novo usuário e envia código OTP por SMS.
     * O token de acesso só é retornado após verificar o código.
     *
     * @unauthenticated
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'success' => true,
            'data'    => $result,
            'message' => 'Código enviado por SMS.',
        ], 201);
    }

    /**
     * Login
     *
     * Solicita código OTP para o telefone informado.
     * O token de acesso só é retornado após verificar o código.
     *
     * @unauthenticated
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return response()->json([
            'success' => true,
            'data'    => $result,
            'message' => 'Se este número estiver cadastrado, um código foi enviado.',
        ]);
    }

    /**
     * Verificar OTP
     *
     * Valida o código recebido por SMS e retorna o token de acesso.
     *
     * @unauthenticated
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->authService->verifyOtp(
            $request->validated('phone'),
            $request->validated('code'),
        );

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