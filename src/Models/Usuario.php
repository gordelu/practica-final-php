<?php
namespace App\Models;

// Modelo genérico de la tabla usuarios: el CRUD de BaseModel
class Usuario extends BaseModel
{
    protected string $table = 'usuarios';
    protected array $fillable = ['nombre', 'email', 'password'];

    // Guarda la contraseña siempre hasheada, nunca en texto plano
    public function insert(array $data): int
    {
        return parent::insert($this->hashPassword($data));
    }

    public function update(int $id, array $data): bool
    {
        return parent::update($id, $this->hashPassword($data));
    }

    private function hashPassword(array $data): array
    {
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    // Devuelve el id del usuario si el email existe y la contraseña coincide, o null si no
    public function findByEmail(string $email, string $password): ?int
    {
        $user = $this->db->fetch(
            "SELECT id, password FROM {$this->table} WHERE email = :email",
            ['email' => $email]
        );

        // password_verify compara la contraseña en texto plano con el hash guardado
        if ($user === null || !password_verify($password, $user['password'])) {
            return null;
        }

        return (int) $user['id'];
    }
}
