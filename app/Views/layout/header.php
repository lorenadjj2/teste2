<?php use App\Core\Session; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="http://projeto.local/" />
    <title>ETBRAZ - <?php echo ucfirst(Session::getUserProfile() ?? 'Convidado'); ?></title>
    <!--<link rel="stylesheet" href="assets/css/teste_display.css">-->
    <link rel="stylesheet" href="assets/css/globals.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/navigation.css">
    <!--<link rel="stylesheet" href="assets/css/Estilo_Do_Formulario_Tailwind.css">-->
    <link rel="stylesheet" href="assets/css/forms.css">
    <link rel="stylesheet" href="assets/css/tables.css">
    <link rel="stylesheet" href="assets/css/alerts.css">
    <!--<script src="assets/dist/trumbowyg.min.js"></script>-->
</head>
<body>
    <div class="top-bar"> <div class="site-logo-text">ETBRAZ</div> <?php if (Session::isLoggedIn()): ?>
            <div class="user-info">
                Olá, <?php echo htmlspecialchars(Session::get('email')); ?> (<?php echo htmlspecialchars(ucfirst(Session::getUserProfile())); ?>)
                <a href="/logout" style="margin-left: 15px; color: white;">Sair</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="main-banner-image">
        </div>

    <section>
        <nav>
    <ul class="menu"> <?php
        // Verifica se $data['menuItems'] existe e é um array
        if (isset($data['menuItems']) && is_array($data['menuItems'])) {
            foreach ($data['menuItems'] as $item) {
                // Verifica se o item possui sub-itens (é um item de menu principal com dropdown)
                if (!empty($item['sub_items']) && is_array($item['sub_items'])) {
                    echo '<li class="has-submenu">'; // 'has-submenu' é uma classe opcional para estilização específica
                    echo '<a href="' . htmlspecialchars($item['route'] ?? '#') . '">' . htmlspecialchars($item['text']) . '</a>'; // Usa '#' se a rota não estiver definida
                    echo '<ul>'; // Abre a lista do submenu (este <ul> será o dropdown-content)
                    foreach ($item['sub_items'] as $subItem) {
                        echo '<li><a href="' . htmlspecialchars($subItem['route'] ?? '#') . '">' . htmlspecialchars($subItem['text']) . '</a></li>';
                    }
                    echo '</ul>'; // Fecha a lista do submenu
                    echo '</li>';
                } else {
                    // Se não tem sub-itens, renderiza como um item de menu normal (sem dropdown)
                    if (isset($item['text']) && isset($item['route'])) {
                        echo '<li><a href="' . htmlspecialchars($item['route'] ?? '#') . '">' . htmlspecialchars($item['text']) . '</a></li>';
                    }
                }
            }
        }
        ?>
         <?php if (Session::isLoggedIn()): ?>
                    <li><a href="/logout">Sair</a></li>
                <?php endif; ?>
</nav>
    </section>
    <main>