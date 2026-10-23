<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Comentario;
use App\Models\Publicacion;

class PublicacionController extends Controller
{
    private Publicacion $publicacion;
    private Comentario $comentario;

    public function __construct(Database $db)
    {
        parent::__construct($db);
        $this->publicacion = new Publicacion($db);
        $this->comentario = new Comentario($db);
    }

    // Listado; si llega una búsqueda, filtra por título o categoría
    public function index(string $busqueda = ''): void
    {
        $busqueda = trim($busqueda);

        if ($busqueda !== '') {
            $publicaciones = $this->publicacion->buscar($busqueda);
        } else {
            $publicaciones = $this->publicacion->getAll();
        }

        $this->render('publicaciones/index', [
            'publicaciones' => $publicaciones,
            'busqueda' => $busqueda,
        ]);
    }

    // Una publicación con sus comentarios
    public function show(int $id): void
    {
        $publicacion = $this->publicacion->getById($id);
        if ($publicacion === null) {
            $this->abort(404, 'Publicación no encontrada');
        }

        $this->render('publicaciones/show', [
            'publicacion' => $publicacion,
            'comentarios' => $this->comentario->getByPublicacion($id),
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->render('publicaciones/form');
    }

    public function store(): void
    {
        $userId = $this->requireLogin();
        $data = $this->getFormData();

        $error = $this->validate($data);
        if ($error !== null) {
            $this->render('publicaciones/form', ['error' => $error, 'publicacion' => $data]);
            return;
        }

        $data['usuario_id'] = $userId;
        $id = $this->publicacion->insert($data);

        $this->redirect('/publicaciones/' . $id);
    }

    public function edit(int $id): void
    {
        $publicacion = $this->findOwned($id, $this->requireLogin());
        $this->render('publicaciones/form', ['publicacion' => $publicacion]);
    }

    public function update(int $id): void
    {
        $this->findOwned($id, $this->requireLogin());
        $data = $this->getFormData();

        $error = $this->validate($data);
        if ($error !== null) {
            $data['id'] = $id;
            $this->render('publicaciones/form', ['error' => $error, 'publicacion' => $data]);
            return;
        }

        $this->publicacion->update($id, $data);
        $this->redirect('/publicaciones/' . $id);
    }

    public function delete(int $id): void
    {
        $this->findOwned($id, $this->requireLogin());
        $this->publicacion->delete($id);

        $this->redirect('/publicaciones');
    }

    private function getFormData(): array
    {
        return [
            'titulo' => trim($_POST['titulo'] ?? ''),
            'cuerpo' => trim($_POST['cuerpo'] ?? ''),
            'categoria' => trim($_POST['categoria'] ?? ''),
        ];
    }

    // Devuelve el mensaje de error si algún campo no es válido, o null si todo está bien
    private function validate(array $data): ?string
    {
        if ($data['titulo'] === '' || strlen($data['titulo']) > 200) {
            return 'El título es obligatorio (máximo 200 caracteres)';
        }
        if ($data['cuerpo'] === '') {
            return 'El cuerpo es obligatorio';
        }
        if ($data['categoria'] === '' || strlen($data['categoria']) > 50) {
            return 'La categoría es obligatoria (máximo 50 caracteres)';
        }
        return null;
    }

    // Busca la publicación y comprueba que es del usuario logueado
    private function findOwned(int $id, int $userId): array
    {
        $publicacion = $this->publicacion->getById($id);
        if ($publicacion === null) {
            $this->abort(404, 'Publicación no encontrada');
        }
        if ((int) $publicacion['usuario_id'] !== $userId) {
            $this->abort(403, 'No puedes modificar una publicación que no es tuya');
        }
        return $publicacion;
    }
}
