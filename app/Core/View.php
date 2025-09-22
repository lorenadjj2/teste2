<?php

namespace App\Core;

class View
{
    /**
     * Renderiza uma view, com a opção de incluir ou não o cabeçalho e o rodapé do layout.
     *
     * @param string $viewPath O caminho completo do arquivo da view a ser renderizada.
     * @param array $data Um array associativo de dados a serem passados para a view.
     * @param bool $isStandalone Se true, renderiza apenas a view sem o cabeçalho/rodapé. Padrão é false.
     * @return void
     */
    public function render(string $viewPath, array $data = [], bool $isStandalone = false)
    {
        // Inicia o buffer de saída. Isso captura todo o output HTML.
        ob_start();

        // Extrai o array $data para variáveis individuais, tornando-as acessíveis na view.
        // Por exemplo, $data['userEmail'] se torna $userEmail na view.
        extract($data);

        // Se a view NÃO for standalone (ou seja, precisa do layout completo), inclui o cabeçalho.
        if (!$isStandalone) {
            // Inclui o arquivo do cabeçalho do layout.
            // O caminho é relativo ao diretório raiz do projeto (APP_ROOT).
            require_once APP_ROOT . '/app/Views/layout/header.php';
        }

        // Inclui o arquivo da view principal (o conteúdo específico da página).
        require_once $viewPath;

        // Se a view NÃO for standalone (ou seja, precisa do layout completo), inclui o rodapé.
        if (!$isStandalone) {
            // Inclui o arquivo do rodapé do layout.
            require_once APP_ROOT . '/app/Views/layout/footer.php';
        }

        // Envia o conteúdo do buffer de saída para o navegador e limpa o buffer.
        echo ob_get_clean();
    }
}
