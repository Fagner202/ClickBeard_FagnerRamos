<?php
// src/utils/jwt.php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHandler {
    private static string $secret_key = 'chave_secreta_123'; // coloque em .env futuramente
    private static string $algoritmo = 'HS256';

    public static function gerarToken(array $dados, int $expiracaoSegundos = 3600): string {
        $agora = time();
        $payload = [
            'iat' => $agora,
            'exp' => $agora + $expiracaoSegundos,
            'data' => $dados
        ];

        return JWT::encode($payload, self::$secret_key, self::$algoritmo);
    }

    function validarJWT($token) {
        // Implemente sua validação de token
        // Exemplo simplificado:
        $partes = explode('.', $token);
        if (count($partes) !== 3) return false;
        
        $payload = json_decode(base64_decode($partes[1]), true);
        return ($payload['exp'] > time());
    }
}
