<?php

namespace App\Core;

use PDO; // Importa PDO para dicas de tipo (Melhora o autocompletar do editor)
use PDOException; // Importa PDOException
use App\Config\Database; // Importa sua classe Database

abstract class Model
{
    protected PDO $db;
    protected string $table = ''; // Propriedade para definir o nome da tabela no modelo
    protected string $primaryKey = 'id'; // Propriedade para a chave primária padrão

    public function __construct()
    {
        try {
            // **CORREÇÃO CRUCIAL AQUI:**
            // 1. Obtenha a ÚNICA instância da classe Database (chamando o método estático getInstance()).
            $dbInstance = Database::getInstance();
            
            // 2. Chame o método getConnection() NESTA instância (método não estático) para obter a conexão PDO.
            /** @var PDO $this->db */ // Ajuda o editor a entender o tipo de $this->db
            $this->db = $dbInstance->getConnection(); // <-- ESTA É A FORMA CORRETA!
        } catch (PDOException $e) {
            // Em ambiente de produção, é melhor logar o erro e não exibi-lo diretamente ao usuário
            error_log("Erro na conexão com o banco de dados no Model: " . $e->getMessage());
            // Para o desenvolvimento, você pode exibir, mas remova em produção.
            die("Erro crítico: Falha na conexão com o banco de dados. Por favor, tente novamente mais tarde.");
        }
    }

    // Método para ler todos os registros da tabela definida
    protected function readAll(): array
    {
        if (!isset($this->table) || empty($this->table)) {
            error_log("Erro: Propriedade \$table não definida ou vazia no Model " . get_class($this));
            return [];
        }

        try {
            $stmt = $this->db->query("SELECT * FROM `{$this->table}`");
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Erro ao ler todos os registros da tabela '{$this->table}': " . $e->getMessage());
            return [];
        }
    }

    // Método público para obter todos os registros (pode ser usado por modelos filhos)
    public function getAll(): array
    {
        return $this->readAll(); 
    }

    // Você pode adicionar outros métodos comuns de CRUD aqui, como findById, save, update, delete, etc.
}
