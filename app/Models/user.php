<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class User extends Model
{
    protected string $table = 'tbl_usuarios';
    protected string $primaryKey = 'cod_usuario';

    public function __construct()
    {
        parent::__construct();
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO {$this->table} (nome, email, senha, perfil) 
                VALUES (:nome, :email, :senha, :perfil";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome' => $data['nome'],
            ':email' => $data['email'],
            ':senha' => $data['senha'],
            ':perfil' => $data['perfil']
        ]);
    }

    public function findByEmail(string $email): object|false
    {
        $sql = "SELECT cod_usuario, nome, email, senha, perfil 
                FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getAll(): array
    {
        try {
            $stmt = $this->db->query("SELECT cod_usuario, nome, email, perfil FROM {$this->table}");
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Erro ao obter todos os usuários: " . $e->getMessage());
            return [];
        }
    }

    public function findById(int $id): object|false
    {
        $stmt = $this->db->prepare("SELECT cod_usuario, nome, email, perfil
                                    FROM {$this->table} WHERE cod_usuario = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function updateUser(int $id, array $data): bool
    {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            if ($key !== $this->primaryKey) {
                $fields[] = "`{$key}` = :{$key}";
                $values[":{$key}"] = $value;
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE `{$this->table}` SET " . implode(', ', $fields) . " WHERE `{$this->primaryKey}` = :id";
        $values[':id'] = $id;

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log("Erro ao atualizar usuário: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteUser(int $id): bool
    {//unselect usuarios ativos
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao excluir usuário: " . $e->getMessage());
            return false;
        }
    }

    public function searchByName(string $nome): array
    {
        $sql = "SELECT cod_usuario, nome, email, perfil FROM {$this->table} WHERE nome LIKE :nome";
        $stmt = $this->db->prepare($sql);
        $nomeLike = '%' . $nome . '%';
        $stmt->bindParam(':nome', $nomeLike, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}