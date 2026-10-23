<?php
namespace App\Models;

class Comentario extends BaseModel
{
    protected string $table = 'comentarios';
    protected array $fillable = ['publicacion_id', 'usuario_id', 'contenido'];

    // Comentarios de una publicación con el nombre de su autor, del más antiguo al más nuevo
    public function getByPublicacion(int $publicacionId): array
    {
        return $this->db->fetchAll(
            "SELECT c.*, u.nombre AS autor
             FROM comentarios c
             JOIN usuarios u ON u.id = c.usuario_id
             WHERE c.publicacion_id = :publicacion_id
             ORDER BY c.created_at ASC",
            ['publicacion_id' => $publicacionId]
        );
    }
}
