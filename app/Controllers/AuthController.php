<?php
// ProjetoGES_MVC/app/Controllers/AuthController.php

namespace App\Controllers; // Define o namespace para a classe

use App\Core\Session; // Importa a classe Session
use App\Models\User; // Importa a classe User (o Model)
use App\Core\Controller;

class AuthController extends Controller
{
    private User $userModel; // Declaração da propriedade userModel

    public function __construct()
    {
        parent::__construct(); // Chama o construtor da classe base Controller
        $this->userModel = new User(); // Instancia o Model User
    }

    /**
     * Exibe o formulário de login.
     * Esta é a ação para a rota GET /login
     */
    public function showLoginForm()
    {
        // Pega a mensagem de erro da sessão (se houver) e a limpa
        $data = [
            'login_erro' => Session::get('login_erro'),
            'login_sucesso' => Session::get('login_sucesso') // Se você tiver uma mensagem de sucesso
        ];
        
        // Limpa as mensagens de sessão após serem usadas
        Session::remove('login_erro');
        Session::remove('login_sucesso');
        
        // Passamos 'true' como terceiro argumento para renderizar a view sem o layout (cabeçalho/rodapé).
        $this->view('auth/login', $data, true); 
    }

    /**
     * Processa a submissão do formulário de login.
     * Esta é a ação para a rota POST /login
     */
    public function login()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email_input  = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
            $senha_input  = $_POST['senha'] ?? '';
            $perfil_input = filter_var($_POST['perfil'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (empty($email_input) || empty($senha_input) || empty($perfil_input)) {
                Session::set('login_erro', 'Por favor, preencha todos os campos.');
                header('Location: /login');
                exit();
            }

            try {
                $usuario = $this->userModel->findByEmail($email_input);

                if ($usuario && password_verify($senha_input, $usuario->senha)) {
                    if ($perfil_input === $usuario->perfil) {
                        Session::set('cod_usuario', $usuario->cod_usuario);
                        Session::set('email', $usuario->email);
                        Session::set('perfil', $usuario->perfil);
                        session_regenerate_id(true);
                        header("Location: /dashboard");
                        exit();
                    } else {
                        Session::set('login_erro', 'Perfil incorreto para o usuário fornecido.');
                    }
                } else {
                    Session::set('login_erro', 'E-mail ou senha incorretos.');
                }
            } catch (\PDOException $e) {
                error_log("Erro de banco de dados no login: " . $e->getMessage());
                Session::set('login_erro', 'Ocorreu um erro interno. Por favor, tente novamente mais tarde.');
            }
        } else {
            Session::set('login_erro', 'Método de requisição inválido.');
        }

        header("Location: /login");
        exit();
    }
    public function logout()
    {
        Session::destroy(); // Destrói todas as variáveis de sessão e a própria sessão
        Session::set('login_sucesso', 'Você foi desconectado.'); // Mensagem de sucesso ao deslogar
        header("Location: /login"); // Redireciona para a página de login
        exit();
    }
}
