<?php
// ProjetoGES_MVC/app/Config/Database.php

namespace App\Config; // Define o namespace para a classe

use PDO;
use PDOException;

class Database
{
    private static $instance = null; // Para implementar o padrão Singleton (garante apenas uma instância da conexão)
    private $connection; // Para armazenar a conexão PDO

    // O construtor é privado para forçar o uso do método estático getConnection()
    private function __construct()
    {
        $host = 'localhost';
        $dbname = 'gesbd'; // <-- ATUALIZE com o nome do SEU banco de dados
        $user = 'root';         // <-- ATUALIZE com o seu usuário do banco de dados
        $password = ''; // <-- ATUALIZE com a sua senha do banco de dados

        try {
            $this->connection = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Busca resultados como array associativo por padrão
        } catch (PDOException $e) {
            // Loga o erro de conexão. Em produção, você não quer que isso apareça para o usuário.
            error_log("Database connection error: " . $e->getMessage());
            die("Um erro técnico ocorreu. Por favor, tente novamente mais tarde.");
        }
    }
    /**
     * Este é o método `getInstance()`.
     * Ele verifica se já existe uma instância de Database.
     * Se não existir, ele cria uma nova instância (e chama o construtor privado).
     * Se já existir, ele retorna a instância existente.
     */
    public static function getInstance() {
        if (self::$instance === null) { // Verifica se a instância já foi criada
            self::$instance = new Database(); // Se não, cria uma nova instância
        }
        return self::$instance; // Retorna a instância única
    }
    /**
     * Retorna a instância da conexão PDO.
     * @return PDO A instância da conexão PDO.
     */
     public function getConnection() {
        return $this->connection;
    }
}