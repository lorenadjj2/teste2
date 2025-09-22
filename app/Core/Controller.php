<?php

namespace App\Core;

// Certifique-se de que a classe View esteja sendo usada, se não estiver no mesmo namespace.
use App\Core\View;

abstract class Controller
{
    // Propriedade para armazenar a instância da View
    protected View $viewInstance;

    public function __construct()
    {
        // Inicializa a instância da classe View no construtor do Controller
        $this->viewInstance = new View();
    }

    /**
     * Carrega um modelo (Model).
     * @param string $model Nome do modelo a ser carregado.
     * @return object Retorna uma instância do modelo.
     */
    public function model(string $model)
    {
        // Converte o nome do modelo para o formato de caminho de arquivo.
        // Ex: "User" se torna "User.php"
        $modelPath = APP_ROOT . '/app/Models/' . $model . '.php'; // Use APP_ROOT para consistência

        // Verifica se o arquivo do modelo existe.
        if (file_exists($modelPath)) {
            // Inclui o arquivo do modelo.
            require_once $modelPath;
            // Cria uma instância do modelo e a retorna.
            // Ex: "User" se torna new \App\Models\User();
            $class = '\\App\\Models\\' . basename($modelPath, '.php'); // Obtém o nome da classe sem a extensão
            return new $class();
        } else {
            // Se o modelo não for encontrado, exibe uma mensagem de erro e encerra a execução.
            error_log("Model not found: " . $modelPath);
            die('Model not found: ' . $modelPath);
        }
    }

    /**
     * Carrega uma visualização (View) usando a classe App\Core\View.
     *
     * Este método agora delega a renderização da view para a classe App\Core\View,
     * e o mais importante, repassa o argumento $isStandalone.
     *
     * @param string $view Caminho da view a ser carregada (ex: 'admin/dashboard').
     * @param array $data Dados a serem passados para a view.
     * @param bool $isStandalone Se true, renderiza apenas a view sem o cabeçalho/rodapé. Padrão é false.
     * @return void
     */
    protected function view(string $view, array $data = [], bool $isStandalone = false)
    {
        // Converte o caminho da view para o formato de arquivo.
        // Ex: "admin/dashboard" se torna "APP_ROOT/app/Views/admin/dashboard.php"
        $viewPath = APP_ROOT . '/app/Views/' . $view . '.php'; // Use APP_ROOT para consistência

        // Verifica se o arquivo da view existe.
        if (!file_exists($viewPath)) { // Use !file_exists para verificar se NÃO existe
            // Se a view não for encontrada, exibe uma mensagem de erro.
            error_log('View not found: ' . $viewPath);
            die('View not found: ' . $viewPath);
        }

        // Chama o método render() da instância de View, passando todos os argumentos,
        // incluindo o novo $isStandalone.
        $this->viewInstance->render($viewPath, $data, $isStandalone);
    }
    /**
     * Redireciona para uma URL com parâmetros de query string.
     * @param string $url
     * @param array $params
     */
    protected function redirect($url, $params = [])
    {
        if (!empty($params)) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($params);
        }
        header('Location: ' . $url);
        exit;
    }
}