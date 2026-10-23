<?php
namespace App\Models;

class Publicacion extends BaseModel
{
    protected string $table = 'publicaciones';
    protected array $fillable = ['usuario_id', 'titulo', 'cuerpo', 'categoria'];

    // Consulta base: cada publicación con el nombre de su autor
    private string $select = "SELECT p.*, u.nombre AS autor
                              FROM publicaciones p
                              JOIN usuarios u ON u.id = p.usuario_id";

    // Las más recientes primero
    public function getAll(): array
    {
        return $this->db->fetchAll("{$this->select} ORDER BY p.created_at DESC");
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetch("{$this->select} WHERE p.id = :id", ['id' => $id]);
    }

    // Busca el texto en el título o en la categoría
    public function buscar(string $texto): array
    {
        return $this->db->fetchAll(
            "{$this->select} WHERE p.titulo LIKE :titulo OR p.categoria LIKE :categoria ORDER BY p.created_at DESC",
            ['titulo' => "%$texto%", 'categoria' => "%$texto%"]
        );
    }

    // Búsqueda parcial: "php" encuentra "Aprendiendo PHP"
    public function buscarPorTitulo(string $titulo): array
    {
        return $this->db->fetchAll(
            "{$this->select} WHERE p.titulo LIKE :titulo ORDER BY p.created_at DESC",
            ['titulo' => "%$titulo%"]
        );
    }

    public function getByCategoria(string $categoria): array
    {
        return $this->db->fetchAll(
            "{$this->select} WHERE p.categoria = :categoria ORDER BY p.created_at DESC",
            ['categoria' => $categoria]
        );
    }

    public function getByUsuario(int $usuarioId): array
    {
        return $this->db->fetchAll(
            "{$this->select} WHERE p.usuario_id = :usuario_id ORDER BY p.created_at DESC",
            ['usuario_id' => $usuarioId]
        );
    }
}
