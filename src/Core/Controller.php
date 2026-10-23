<?php
namespace App\Core;

// Funciones comunes a todos los controladores
abstract class Controller
{
    public function __construct(protected Database $db)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Carga la vista y le pasa las variables del array $data
    protected function render(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../Views/' . $view . '.php';
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    // Devuelve el id del usuario logueado; si no hay sesión, lo manda al login
    protected function requireLogin(): int
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
        return (int) $_SESSION['user_id'];
    }

    // Corta la ejecución con un código de error HTTP (404, 403...)
    protected function abort(int $code, string $message): void
    {
        http_response_code($code);
        echo htmlspecialchars($message);
        exit;
    }
}
