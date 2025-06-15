<?php
// src/utils/jwt.php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class JWTHandler {
    private static string $secret_key = 'chave_secreta_123'; // mova para .env futuramente
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

    public static function validarJWT(string $token): bool {
        try {
            JWT::decode($token, new Key(self::$secret_key, self::$algoritmo));
            return true;
        } catch (ExpiredException | SignatureInvalidException | \Exception $e) {
            return false;
        }
    }
}
