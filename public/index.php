<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\ComentarioController;
use App\Controllers\PublicacionController;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Router;

session_start();

// Todas las peticiones POST deben traer el token CSRF del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Csrf::verify($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Token CSRF no válido');
}

$db = new Database();
$router = new Router();

// Autenticación
$router->get('/register', fn() => (new AuthController($db))->showRegisterForm());
$router->post('/register', fn() => (new AuthController($db))->register());
$router->get('/login', fn() => (new AuthController($db))->showLoginForm());
$router->post('/login', fn() => (new AuthController($db))->login());
$router->get('/logout', fn() => (new AuthController($db))->logout());

// Publicaciones
$router->get('/', fn() => (new PublicacionController($db))->index($_GET['busqueda'] ?? ''));
$router->get('/publicaciones', fn() => (new PublicacionController($db))->index($_GET['busqueda'] ?? ''));
$router->get('/publicaciones/crear', fn() => (new PublicacionController($db))->create());
$router->post('/publicaciones/crear', fn() => (new PublicacionController($db))->store());
$router->get('/publicaciones/{id}', fn($id) => (new PublicacionController($db))->show($id));
$router->get('/publicaciones/{id}/editar', fn($id) => (new PublicacionController($db))->edit($id));
$router->post('/publicaciones/{id}/editar', fn($id) => (new PublicacionController($db))->update($id));
$router->post('/publicaciones/{id}/borrar', fn($id) => (new PublicacionController($db))->delete($id));

// Comentarios
$router->post('/publicaciones/{id}/comentarios', fn($id) => (new ComentarioController($db))->store($id));
$router->post('/comentarios/{id}/borrar', fn($id) => (new ComentarioController($db))->delete($id));

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
