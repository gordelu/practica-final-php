<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Comentario;
use App\Models\Publicacion;

class ComentarioController extends Controller
{
    private Comentario $comentario;
    private Publicacion $publicacion;

    public function __construct(Database $db)
    {
        parent::__construct($db);
        $this->comentario = new Comentario($db);
        $this->publicacion = new Publicacion($db);
    }

    // Añade un comentario a una publicación
    public function store(int $publicacionId): void
    {
        $userId = $this->requireLogin();

        if ($this->publicacion->getById($publicacionId) === null) {
            $this->abort(404, 'Publicación no encontrada');
        }

        $contenido = trim($_POST['contenido'] ?? '');
        if ($contenido !== '') {
            $this->comentario->insert([
                'publicacion_id' => $publicacionId,
                'usuario_id' => $userId,
                'contenido' => $contenido,
            ]);
        }

        $this->redirect('/publicaciones/' . $publicacionId);
    }

    // Solo el autor del comentario puede borrarlo
    public function delete(int $id): void
    {
        $userId = $this->requireLogin();

        $comentario = $this->comentario->getById($id);
        if ($comentario === null) {
            $this->abort(404, 'Comentario no encontrado');
        }
        if ((int) $comentario['usuario_id'] !== $userId) {
            $this->abort(403, 'No puedes borrar un comentario que no es tuyo');
        }

        $this->comentario->delete($id);
        $this->redirect('/publicaciones/' . $comentario['publicacion_id']);
    }
}
