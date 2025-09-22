<?php

namespace App\Core;

class Router
{
    protected array $routes = [];

    /**
     * Adiciona uma nova rota ao roteador.
     * Normaliza a URI definida para remover barras finais e ajusta o padrão regex.
     *
     * @param string $method O método HTTP (GET, POST, etc.).
     * @param string $uri A URI da rota.
     * @param string $action A string "Controller@method" ou um callable.
     */
    public function addRoute(string $method, string $uri, string $action): void
    {
        $uri = rtrim($uri, '/');
        if ($uri === '') {
            $uri = '/';
        }

        // Primeiro, escape os caracteres especiais da URI LITERAL
        // (ex: /admin/users/edit/{id} -> /admin/users/edit/\{id\})
        $uriPattern = preg_quote($uri, '#');

        // Agora, substitua os placeholders {id} pelo padrão de captura regex
        // (ex: /admin/users/edit/\{id\} -> /admin/users/edit/(?P<id>[^/]+))
        $uriPattern = preg_replace('/\\\{([a-zA-Z0-9_]+)\\\}/', '(?P<$1>[^/]+)', $uriPattern);
        
        // Ajusta a regex para permitir ou não uma barra final opcional no URI da requisição
        // Ex: "/admin" irá corresponder a "/admin" E "/admin/"
        $uriPattern = '#^' . $uriPattern . '/?$#'; 

        $this->routes[$method][$uriPattern] = $action;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        $uri = rtrim($uri, '/');
        if ($uri === '') {
            $uri = '/';
        }

        error_log(date('[d-M-Y H:i:s e]') . " DEBUG (Router::dispatch): Método da requisição: " . $method);
        error_log(date('[d-M-Y H:i:s e]') . " DEBUG (Router::dispatch): URI da requisição normalizada: " . $uri);
        error_log(date('[d-M-Y H:i:s e]') . " DEBUG (Router::dispatch): Rotas registradas para o método " . $method . ": " . print_r($this->routes[$method] ?? [], true));

        if (!isset($this->routes[$method])) {
            error_log(date('[d-M-Y H:i:s e]') . " DEBUG (Router::dispatch): Nenhuma rota definida para o método " . $method);
            $this->handleNotFound();
            return;
        }

        foreach ($this->routes[$method] as $routePattern => $action) {
            // Reativando o log de tentativa de casamento de rota
            error_log(date('[d-M-Y H:i:s e]') . " DEBUG (Router::dispatch): Tentando casar URI '" . $uri . "' com padrão '" . $routePattern . "'");
            
           if (preg_match($routePattern, $uri, $matches)) {
                // Filtra apenas os parâmetros nomeados da URI
                $paramsAssociative = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Converte para array indexado para compatibilidade com call_user_func_array
                $params = array_values($paramsAssociative);

                error_log(date('[d-M-Y H:i:s e]') . " DEBUG (Router::dispatch): Rota encontrada! Chamando " . $action);
                $this->callAction($action, $params);
                return;
            }

        }

        error_log(date('[d-M-Y H:i:s e]') . " DEBUG (Router::dispatch): Nenhuma rota correspondente encontrada para URI: " . $uri . " e Método: " . $method);
        $this->handleNotFound();
    }

    /**
     * Chama o método do controlador.
     *
     * @param string $action A string "Controller@method".
     * @param array $params Parâmetros capturados da URI.
     */
    protected function callAction(string $action, array $params): void
    {
        list($controllerName, $methodName) = explode('@', $action);

        // Adiciona o namespace completo para o controller
        $controllerClass = 'App\\Controllers\\' . $controllerName;

        if (!class_exists($controllerClass)) {
            error_log(date('[d-M-Y H:i:s e]') . " DEBUG (Router::callAction): Classe do controller não encontrada: $controllerClass");
            $this->handleNotFound(); // Redireciona para 404
            return;
        }

        $controllerInstance = new $controllerClass();

        if (!method_exists($controllerInstance, $methodName)) {
            error_log(date('[d-M-Y H:i:s e]') . " DEBUG (Router::callAction): Método '$methodName' não encontrado no controller '$controllerClass'.");
            $this->handleNotFound(); // Redireciona para 404
            return;
        }        
        // Adiciona um log de debug para saber qual método foi chamado
        error_log(date('[d-M-Y H:i:s e]') . " DEBUG: $action foi chamado.", 4);

        call_user_func_array([$controllerInstance, $methodName], $params);
    }

    /**
     * Lida com rotas não encontradas (404).
     */
    protected function handleNotFound(): void
    {
        header("HTTP/1.0 404 Not Found");
        // Carrega a view de erro 404
        if (defined('APP_ROOT') && file_exists(APP_ROOT . '/app/Views/errors/404.php')) {
            require_once APP_ROOT . '/app/Views/errors/404.php';
        } else {
            // Último recurso se nem a view 404 for encontrada
            echo "404 - Página Não Encontrada (Arquivo 404.php ausente)";
        }
    }
}
