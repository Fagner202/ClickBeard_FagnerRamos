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

    public static function validarToken(string $jwt): ?array {
        try {
            $decodificado = JWT::decode($jwt, new Key(self::$secret_key, self::$algoritmo));
            return (array) $decodificado->data;
        } catch (Exception $e) {
            return null;
        }
    }
}
