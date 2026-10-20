<?php
namespace App\Models;

use App\Core\Database;

abstract class BaseModel
{
    // Cada modelo hijo define su tabla y las columnas que se pueden escribir
    protected string $table;
    protected array $fillable = [];
    protected string $primaryKey = 'id';

    public function __construct(protected Database $db)
    {
    }

    public function getAll(): array
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table}");
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id",
            ['id' => $id]
        );
    }

    // Devuelve el id de la fila creada
    public function insert(array $data): int
    {
        $data = $this->filter($data);
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $this->db->execute(
            "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)",
            $data
        );
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $data = $this->filter($data);

        // Convierte cada columna en "columna = :columna" y las une con comas.
        // Ej: ['titulo' => 'x', 'cuerpo' => 'y'] -> "titulo = :titulo, cuerpo = :cuerpo"
        $set = implode(', ', array_map(fn($col) => "$col = :$col", array_keys($data)));

        // Añade el id a los parámetros para rellenar el :id del WHERE
        $data['id'] = $id;

        // PDO sustituye cada :columna por su valor de $data de forma segura
        $this->db->execute(
            "UPDATE {$this->table} SET $set WHERE {$this->primaryKey} = :id",
            $data
        );
        return true;
    }

    public function delete(int $id): bool
    {
        return $this->db->execute(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id",
            ['id' => $id]
        ) > 0;
    }

    // Solo deja pasar columnas de $fillable para evitar inyección en los nombres de columna
    protected function filter(array $data): array
    {
        // array_flip convierte ['nombre', 'email'] en ['nombre' => 0, 'email' => 1]
        // para que array_intersect_key se quede solo con las claves de $data que están en $fillable.
        // Ej: ['nombre' => 'Ana', 'rol' => 'admin'] -> ['nombre' => 'Ana']
        $data = array_intersect_key($data, array_flip($this->fillable));

        // Si no queda ninguna columna válida, la consulta SQL quedaría mal formada
        if (empty($data)) {
            throw new \InvalidArgumentException('No hay columnas válidas para ' . $this->table);
        }
        return $data;
    }
}
