<?php
namespace App\Controllers;

use App\Core\Database;
use App\Models\Usuario;
use PDOException;

class AuthController
{
    private Usuario $user;

    public function __construct(Database $db)
    {
        $this->user = new Usuario($db);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function showRegisterForm(): void
    {
        $this->render('auth/register');
    }

    public function register(): void
    {
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
            $this->render('auth/register', ['error' => 'Rellena todos los campos (contraseña mínimo 6 caracteres)']);
            return;
        }

        try {
            $this->user->insert([
                'nombre' => $nombre,
                'email' => $email,
                'password' => $password, // Usuario::insert ya la hashea
            ]);
        } catch (PDOException $e) {
            // 23000: el email ya existe (columna UNIQUE)
            if ($e->getCode() === '23000') {
                $this->render('auth/register', ['error' => 'Ese email ya está registrado']);
                return;
            }
            throw $e;
        }

        $this->redirect('/login');
    }

    public function showLoginForm(): void
    {
        $this->render('auth/login');
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $id = $this->user->findByEmail($email, $password);

        if ($id === null) {
            $this->render('auth/login', ['error' => 'Email o contraseña incorrectos']);
            return;
        }

        // Nuevo id de sesión al iniciar sesión para evitar robo de sesión
        session_regenerate_id(true);
        $_SESSION['user_id'] = $id;

        $this->redirect('/');
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();

        $this->redirect('/login');
    }

    // Carga la vista y le pasa las variables del array $data
    private function render(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../Views/' . $view . '.php';
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
