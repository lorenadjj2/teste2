<?php
// public/index.php
//die("DEBUG: public/index.php foi atingido! URI: " . $_SERVER['REQUEST_URI']);
define('APP_ROOT', dirname(__DIR__));

   // --- ATIVAR EXIBIÇÃO E LOG DE ERROS PARA DEBUG (MUITO IMPORTANTE!) ---
    error_reporting(E_ALL); // Reporta todos os erros PHP
    ini_set('display_errors', 1); // Exibe erros na tela (apenas em ambiente de desenvolvimento!)
    ini_set('log_errors', 1); // Garante que os erros sejam logados
    ini_set('error_log', APP_ROOT . '/app_error.log'); // Define um arquivo de log específico para o seu app
    // --- FIM DA CONFIGURAÇÃO DE ERROS ---

require_once APP_ROOT . '/vendor/autoload.php';
require_once __DIR__ . '/../app/Core/Router.php';

clearstatcache();

\App\Core\Session::init();

// --- ADICIONE ESTAS DUAS LINHAS AQUI ---
error_log("DEBUG: Hora do log: " . date('Y-m-d H:i:s P')); // Adiciona um timestamp para referência
error_log("DEBUG: Conteúdo de _SESSION após Session::init() em index.php: " . print_r($_SESSION, true));
error_log("DEBUG: Perfil do usuário após Session::init() em index.php: " . (isset($_SESSION['perfil']) ? $_SESSION['perfil'] : 'Não Definido'));
// --- FIM DAS LINHAS ---

$router = new App\Core\Router();
// Obtém a URI da requisição para o roteador, mantendo a barra inicial se houver
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// --- DEFINIÇÃO DAS ROTAS ---
// Rotas de Autenticação
$router->addRoute('GET', '/login', 'AuthController@showLoginForm');
$router->addRoute('POST', '/login', 'AuthController@login');
$router->addRoute('GET', '/logout', 'AuthController@logout');

// Rotas do Dashboard
$router->addRoute('GET', '/dashboard', 'DashboardController@index');
$router->addRoute('GET', '/', 'DashboardController@index'); // Rota padrão para a raiz

// Rotas para Administração de Usuários
$router->addRoute('GET', '/admin/users', 'Admin\UserController@index');                   // Lista todos os usuários
$router->addRoute('GET', '/admin/users/register', 'Admin\UserController@showRegisterForm'); // Exibe o formulário de cadastro
$router->addRoute('POST', '/admin/users/create', 'Admin\UserController@processRegister');   // Processa o cadastro (submissão do formulário)
$router->addRoute('GET', '/admin/users/edit/{id}', 'Admin\UserController@showEditForm');   // Exibe o formulário de edição (com ID)
$router->addRoute('POST', '/admin/users/update/{id}', 'Admin\UserController@update'); // Processa a atualização (submissão do formulário de edição)
$router->addRoute('GET', '/admin/users/delete/{id}', 'Admin\UserController@delete');       // Processa a exclusão (com ID)
$router->addRoute('GET', '/admin/users/search', 'Admin\UserController@search');


//$router->dispatch();
$router->dispatch($_SERVER['REQUEST_METHOD'], $requestUri);

