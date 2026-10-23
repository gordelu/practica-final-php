<?php
namespace App\Core;

// Protección CSRF: cada sesión tiene un token secreto que deben enviar todos los formularios POST.
// Una web ajena no puede conocerlo, así que no puede enviar formularios en nombre del usuario.
class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Campo oculto para poner dentro de cada <form method="POST">
    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::token()) . '">';
    }

    // hash_equals compara en tiempo constante para no dar pistas sobre el token
    public static function verify(?string $token): bool
    {
        return is_string($token)
            && !empty($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $token);
    }
}
