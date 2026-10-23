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
        // implode(separador, array): une los elementos del array en un string con el separador entre ellos
        // array_keys(array): devuelve un array solo con las claves del array
        $columns = implode(', ', array_keys($data));
        // implode(separador, array): une los elementos del array en un string con el separador entre ellos
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
        // implode(separador, array): une los elementos del array en un string con el separador entre ellos
        // array_map(función, array): aplica la función a cada elemento del array y devuelve un array nuevo con los resultados
        // array_keys(array): devuelve un array solo con las claves del array
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
        $filtrado = [];

        // Recorre los datos y se queda solo con las columnas que están en $fillable
        // Ej: ['nombre' => 'Ana', 'rol' => 'admin'] -> ['nombre' => 'Ana']
        foreach ($data as $columna => $valor) {
            if (in_array($columna, $this->fillable)) {
                $filtrado[$columna] = $valor;
            }
        }

        // Si no queda ninguna columna válida, la consulta SQL quedaría mal formada
        if (empty($filtrado)) {
            throw new \InvalidArgumentException('No hay columnas válidas para ' . $this->table);
        }
        return $filtrado;
    }
}
