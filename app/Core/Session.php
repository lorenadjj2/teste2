<?php
// ProjetoGES_MVC/app/Core/Session.php

namespace App\Core; // Define o namespace para a classe

class Session
{
    /**
     * Inicializa a sessão com configurações de segurança.
     * Deve ser chamado uma única vez no início da aplicação (no Front Controller).
     */
    public static function init()
    {
        session_set_cookie_params([
            'httponly' => true,
            'samesite' => 'Lax', // Ou 'Strict' para maior segurança
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' // Apenas se usar HTTPS
        ]);

        if (session_status() === PHP_SESSION_NONE) { // Verifica se a sessão ainda não está ativa
            session_start();
        }
        //session_regenerate_id(true); // Regenera o ID da sessão para prevenir Session Fixation
    }

    /**
     * Define um valor na sessão.
     * @param string $key A chave da sessão.
     * @param mixed $value O valor a ser armazenado.
     */
    public static function set($key, $value)
    {
        // Não é necessário chamar self::init() aqui, pois espera-se que init() seja chamado uma vez no Front Controller.
        $_SESSION[$key] = $value;
    }

    /**
     * Obtém um valor da sessão.
     * @param string $key A chave da sessão.
     * @param mixed $default O valor padrão a ser retornado se a chave não existir.
     * @return mixed O valor da sessão ou o padrão.
     */
    public static function get($key, $default = null)
    {
        // Não é necessário chamar self::init() aqui.
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Verifica se uma chave existe na sessão.
     * @param string $key A chave da sessão a ser verificada.
     * @return bool True se a chave existe, false caso contrário.
     */
    public static function has(string $key): bool
    {
        // Não é necessário chamar self::init() aqui.
        return isset($_SESSION[$key]);
    }

    /**
     * Remove uma chave da sessão.
     * @param string $key A chave a ser removida.
     */
    public static function remove(string $key)
    {
        // Não é necessário chamar self::init() aqui.
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Verifica se o usuário está logado.
     * @return bool True se o usuário estiver logado, false caso contrário.
     */
    public static function isLoggedIn()
    {
        return self::get('cod_usuario') !== null && self::get('perfil') !== null;
    }

    /**
     * Obtém o perfil do usuário logado.
     * @return string|null O perfil do usuário ou null se não estiver logado.
     */
    public static function getUserProfile()
    {
        return self::get('perfil');
    }

    /**
     * Requer que o usuário esteja logado. Redireciona se não estiver.
     */
    public static function requireLogin()
    {
        if (!self::isLoggedIn()) {
            self::destroy(); // Opcional: destruir a sessão para garantir limpeza
            header('Location: /login?erro=sessao_expirada'); // Redireciona para a rota de login
            exit();
        }
    }

    /**
     * Requer que o usuário tenha um dos perfis permitidos. Redireciona se não tiver.
     * @param array $allowedProfiles Um array de perfis permitidos.
     */
    public static function requirePermission(array $allowedProfiles = [])
    {
        self::requireLogin(); // Primeiro, verifica se está logado

        $userProfile = self::getUserProfile();
        if (!empty($allowedProfiles) && !in_array($userProfile, $allowedProfiles)) {
            header('Location: /dashboard?erro=acesso_negado'); // Redireciona para o dashboard ou página de acesso negado
            exit();
        }
    }

    /**
     * Destrói a sessão atual e limpa todas as variáveis de sessão.
     */
    public static function destroy()
    {
        session_unset();
        session_destroy();
    }

    // --- NOVOS MÉTODOS PARA MENSAGENS FLASH ---

    /**
     * Define uma mensagem flash (temporária) na sessão.
     * Será exibida apenas na próxima requisição e depois removida.
     *
     * @param string $type O tipo da mensagem (ex: 'success', 'error', 'warning', 'info').
     * @param string $message O conteúdo da mensagem.
     */
    public static function setFlash(string $type, string $message)
    {
        // Não é necessário chamar self::init() aqui.
        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }
        $_SESSION['flash_messages'][$type] = $message;
    }

    /**
     * Obtém uma mensagem flash da sessão e a remove.
     *
     * @param string $type O tipo da mensagem a ser obtida.
     * @param mixed $default O valor padrão a ser retornado se a mensagem não existir.
     * @return string|null A mensagem flash ou null se não existir.
     */
    public static function getFlash(string $type, $default = null)
    {
        // Não é necessário chamar self::init() aqui.
        $message = $_SESSION['flash_messages'][$type] ?? $default;
        if (isset($_SESSION['flash_messages'][$type])) {
            unset($_SESSION['flash_messages'][$type]); // Remove a mensagem após ser lida
        }
        return $message;
    }

    /**
     * Verifica se há alguma mensagem flash de um tipo específico.
     *
     * @param string $type O tipo da mensagem a ser verificada.
     * @return bool True se houver mensagem flash, false caso contrário.
     */
    public static function hasFlash(string $type): bool
    {
        // Não é necessário chamar self::init() aqui.
        return isset($_SESSION['flash_messages'][$type]);
    }
}
