<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

class UserController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function getAdminMenu(): array
    {
        return [
            ['text' => 'Dashboard', 'route' => '/dashboard'],
            ['text' => 'Gerenciar Usuários', 'route' => '/admin/users'],
            ['text' => 'Cadastrar Usuário', 'route' => '/admin/users/register'],
            ['text' => 'Configurações do Sistema', 'route' => '/admin/settings'],
            ['text' => 'Relatórios', 'route' => '/admin/reports'],
        ];
    }

    public function index()
    {
        if (Session::get('perfil') !== 'admin') {
            Session::set('login_erro', 'Acesso negado. Você não tem permissão para acessar esta página.');
            header('Location: /dashboard');
            exit();
        }

        $users = $this->userModel->getAll();

        $data = [
            'users' => $users,
            'userEmail' => Session::get('email'),
            'userProfile' => Session::get('perfil'),
            'menuItems' => $this->getAdminMenu(),
            'sucesso' => Session::get('sucesso'),
            'erro' => Session::get('erro'),
        ];

        Session::remove('sucesso');
        Session::remove('erro');

        $this->view('admin/users/index', $data);
    }

    public function showRegisterForm()
    {
        if (Session::get('perfil') !== 'admin') {
            Session::set('login_erro', 'Acesso negado.');
            header('Location: /dashboard');
            exit();
        }

        $data = [
            'userEmail' => Session::get('email'),
            'userProfile' => Session::get('perfil'),
            'menuItems' => $this->getAdminMenu(),
            'cadastro_erro' => Session::get('cadastro_erro'),
            'cadastro_sucesso' => Session::get('cadastro_sucesso')
        ];

        Session::remove('cadastro_erro');
        Session::remove('cadastro_sucesso');

        $this->view('admin/users/user_register', $data);
    }

    public function processRegister()
    {
        if (Session::get('perfil') !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            Session::set('login_erro', 'Acesso negado.');
            header('Location: /dashboard');
            exit();
        }

        $nome = filter_var($_POST['nome'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $senha = $_POST['senha'] ?? '';
        $confirmar_senha = $_POST['confirmar_senha'] ?? '';
        $perfil = filter_var($_POST['perfil'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (empty($nome) || empty($email) || empty($senha) || empty($confirmar_senha) || empty($perfil)) {
            Session::set('cadastro_erro', 'Todos os campos são obrigatórios.');
            header('Location: /admin/users/register');
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::set('cadastro_erro', 'Formato de e-mail inválido.');
            header('Location: /admin/users/register');
            exit();
        }

        if (strlen($senha) < 6) {
            Session::set('cadastro_erro', 'A senha deve ter pelo menos 6 caracteres.');
            header('Location: /admin/users/register');
            exit();
        }

        if ($senha !== $confirmar_senha) {
            Session::set('cadastro_erro', 'As senhas não coincidem.');
            header('Location: /admin/users/register');
            exit();
        }

        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $existingUser = $this->userModel->findByEmail($email);
            if ($existingUser) {
                Session::set('cadastro_erro', 'Este e-mail já está cadastrado.');
                header('Location: /admin/users/register');
                exit();
            }

            $this->userModel->create([
                'nome' => $nome,
                'email' => $email,
                'senha' => $senha_hash,
                'perfil' => $perfil
            ]);

            Session::set('cadastro_sucesso', 'Usuário criado com sucesso!');
            header('Location: /admin/users/register');
            exit();
        } catch (\PDOException $e) {
            error_log('Erro ao criar usuário: ' . $e->getMessage());
            Session::set('cadastro_erro', 'Ocorreu um erro ao cadastrar o usuário. Detalhes: ' . $e->getMessage());
            header('Location: /admin/users/register');
            exit();
        }
    }

    public function showEditForm(int $id)
    {
        if (Session::get('perfil') !== 'admin') {
            Session::set('login_erro', 'Acesso negado.');
            header('Location: /dashboard');
            exit();
        }

        $user = $this->userModel->findById($id);
        if (!$user) {
            header("HTTP/1.0 404 Not Found");
            $this->view('errors/404');
            exit();
        }

        $data = [
            'user' => $user,
            'userEmail' => Session::get('email'),
            'userProfile' => Session::get('perfil'),
            'menuItems' => $this->getAdminMenu(),
            'edicao_erro' => Session::get('edicao_erro'),
            'edicao_sucesso' => Session::get('edicao_sucesso')
        ];

        Session::remove('edicao_erro');
        Session::remove('edicao_sucesso');

        $this->view('admin/users/edit', $data);
    }

    public function update(int $id)
    {
        if (Session::get('perfil') !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            Session::set('edicao_erro', 'Acesso negado ou método inválido.');
            header('Location: /dashboard');
            exit();
        }

        $nome = filter_var($_POST['nome'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $perfil = filter_var($_POST['perfil'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $senha = $_POST['senha'] ?? '';
        $confirmar_senha = $_POST['confirmar_senha'] ?? '';

        if (empty($nome) || empty($email) || empty($perfil)) {
            Session::set('edicao_erro', 'Nome, email e perfil são obrigatórios.');
            header('Location: /admin/users/edit/' . $id);
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::set('edicao_erro', 'Formato de e-mail inválido.');
            header('Location: /admin/users/edit/' . $id);
            exit();
        }

        $newPasswordHash = null;
        if (!empty($senha)) {
            if (strlen($senha) < 6) {
                Session::set('edicao_erro', 'A nova senha deve ter pelo menos 6 caracteres.');
                header('Location: /admin/users/edit/' . $id);
                exit();
            }
            if ($senha !== $confirmar_senha) {
                Session::set('edicao_erro', 'As novas senhas não coincidem.');
                header('Location: /admin/users/edit/' . $id);
                exit();
            }
            $newPasswordHash = password_hash($senha, PASSWORD_DEFAULT);
        }

        try {
            $existingUser = $this->userModel->findByEmail($email);
            if ($existingUser && $existingUser->cod_usuario != $id) {
                Session::set('edicao_erro', 'Este e-mail já está em uso por outro usuário.');
                header('Location: /admin/users/edit/' . $id);
                exit();
            }  
               $userData = [
                'nome'   => $nome,
                'email'  => $email,
                'perfil' => $perfil,
            ];

            if ($newPasswordHash !== null) {
                $userData['senha'] = $newPasswordHash;
            }

            $this->userModel->updateUser($id, $userData);

            Session::set('edicao_sucesso', 'Usuário atualizado com sucesso!');
            header('Location: /admin/users/edit/' . $id);
            exit();
        } catch (\PDOException $e) {
            error_log('Erro ao atualizar usuário (Admin\\UserController): ' . $e->getMessage());
            Session::set('edicao_erro', 'Ocorreu um erro ao atualizar o usuário. Detalhes: ' . $e->getMessage());
            header('Location: /admin/users/edit/' . $id);
            exit();
        }
    }
    
    public function delete(int $id): void
    {
        if (Session::get('perfil') !== 'admin') {
            Session::set('erro', 'Acesso negado.');
            header('Location: /dashboard');
            exit();
        }

        $success = $this->userModel->deleteUser($id);

        Session::set(
            $success ? 'sucesso' : 'erro',
            $success ? 'Usuário excluído com sucesso!' : 'Erro ao excluir usuário. Tente novamente.'
        );

        header('Location: /admin/users');
        exit();
    }
    public function search(): void
    {
        if (Session::get('perfil') !== 'admin') {
            Session::set('login_erro', 'Acesso negado.');
            header('Location: /dashboard');
            exit();
        }

        $searchTerm = $_GET['nome'] ?? '';
        $usuarios = !empty($searchTerm)
            ? $this->userModel->searchByName($searchTerm) ?? []
            : [];

        $data = [
            'usuarios'    => $usuarios,
            'searchTerm'  => $searchTerm,
            'userEmail'   => Session::get('email'),
            'userProfile' => Session::get('perfil'),
            'menuItems'   => $this->getAdminMenu(),
        ];

        $this->view('admin/users/user_search', $data);
    }
}
