<?php
// ProjetoGES_MVC/app/Views/dashboard/index.php

// A variável $data virá do Controller
$userEmail = $data['userEmail'] ?? 'Usuário';
$userProfile = $data['userProfile'] ?? 'Convidado';
?>

<div class="content">
    <h1>Bem-vindo ao Sistema de Gestão Acadêmica!, <?php echo htmlspecialchars(ucfirst($userProfile)); ?>!</h1>
    <p>Você está logado como: <?php echo htmlspecialchars($userEmail); ?></p>
    <p>Esta é a sua página inicial. As funcionalidades disponíveis dependerão do seu perfil.</p>

    <?php if ($userProfile === 'admin'): ?>
        <p>Acesso total como Administrador.</p>
    <?php elseif ($userProfile === 'professor'): ?>
        <p>Acesso às ferramentas do Professor.</p>
    <?php endif; ?>
</div>