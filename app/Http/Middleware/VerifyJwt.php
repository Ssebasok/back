<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class VerifyJwt
{
    public function handle(Request $request, Closure $next)
    {
        // Obtener el token del header Authorization
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json([
                'success' => 0,
                'message' => 'Bearer token es requerido'
            ], 401);
        }

        try {
            // Configurar leeway para tolerancia de tiempo
            JWT::$leeway = 60;
            
            // Obtener algoritmo y secret desde .env
            $algo = env('JWT_ALGO', 'HS256');
            $secret = env('JWT_SECRET');
            
            if (!$secret) {
                return response()->json([
                    'success' => 0,
                    'message' => 'JWT_SECRET no configurado'
                ], 500);
            }

            // Decodificar el token
            if ($algo === 'HS256') {
                $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            } else {
                // Para algoritmos asimétricos (RS256, etc.)
                $publicKey = file_get_contents(storage_path('app/keys/jwt_public.pem'));
                $decoded = JWT::decode($token, new Key($publicKey, $algo));
            }

            // Agregar datos del JWT a la request para uso posterior
            $request->attributes->set('jwt', (array) $decoded);
            
            // Log para debugging (opcional)
            \Log::info('JWT validado correctamente', [
                'user_id' => $decoded->sub ?? null,
                'exp' => $decoded->exp ?? null
            ]);

        } catch (\Firebase\JWT\ExpiredException $e) {
            return response()->json([
                'success' => 0,
                'message' => 'Token expirado'
            ], 401);
            
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return response()->json([
                'success' => 0,
                'message' => 'Token con firma inválida'
            ], 401);
            
        } catch (\Firebase\JWT\BeforeValidException $e) {
            return response()->json([
                'success' => 0,
                'message' => 'Token no válido aún'
            ], 401);
            
        } catch (\Throwable $e) {
            \Log::error('Error validando JWT', [
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'token_preview' => substr($token, 0, 20) . '...',
                'algo' => $algo,
                'secret_length' => strlen($secret ?? '')
            ]);
            
            return response()->json([
                'success' => 0,
                'message' => 'Token inválido',
                'debug' => [
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e)
                ]
            ], 401);
        }

        return $next($request);
    }
}
