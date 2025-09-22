<?php
// echo "DEBUG: user_register.php está sendo carregado!<br>"; // Pode remover esta linha de debug se quiser
?>

<div class="form-container">
    <h2>Editar Usuário: <?php echo htmlspecialchars($data['user']->nome ?? ''); ?></h2>

    <?php if (!empty($data['edicao_sucesso'])): ?>
        <div class="success-message">
            <?php echo htmlspecialchars($data['edicao_sucesso']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($data['edicao_erro'])): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($data['edicao_erro']); ?>
        </div>
    <?php endif; ?>

    <form action="/admin/users/update/<?php echo htmlspecialchars($data['user']->cod_usuario ?? ''); ?>" method="POST">
        <div class="form-group">
            <div class="label">
                <label for="nome">Nome:</label>
            </div>
            <div class="input">
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($data['user']->nome ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <div class="label">
                <label for="email">Email:</label>
            </div>
            <div class="input">
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($data['user']->email ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <div class="label">
                <label for="perfil">Perfil:</label>
            </div>
            <div class="input">
                <select id="perfil" name="perfil" required>
                    <option value="admin" <?php echo ($data['user']->perfil ?? '') == 'admin' ? 'selected' : ''; ?>>Administrador</option>
                    <option value="secretaria" <?php echo ($data['user']->perfil ?? '') == 'secretaria' ? 'selected' : ''; ?>>Secretaria</option>
                    <option value="coordenador" <?php echo ($data['user']->perfil ?? '') == 'coordenador' ? 'selected' : ''; ?>>Coordenador</option>
                    <option value="suporte" <?php echo ($data['user']->perfil ?? '') == 'suporte' ? 'selected' : ''; ?>>Suporte</option>
                    <option value="administrativo" <?php echo ($data['user']->perfil ?? '') == 'administrativo' ? 'selected' : ''; ?>>Administrativo</option>
                    <option value="professor" <?php echo ($data['user']->perfil ?? '') == 'professor' ? 'selected' : ''; ?>>Professor</option>
                    <option value="aluno" <?php echo ($data['user']->perfil ?? '') == 'aluno' ? 'selected' : ''; ?>>Aluno</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="label">
                <label for="senha">Nova Senha (opcional):</label>
            </div>
            <div class="input">
                <input type="password" id="senha" name="senha" placeholder="Deixe em branco para não alterar">
                <small>Preencha apenas se quiser alterar a senha.</small>
            </div>
        </div>

        <div class="form-group">
            <div class="label">
                <label for="confirmar_senha">Confirmar Nova Senha:</label>
            </div>
            <div class="input">
                <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Repita a nova senha">
            </div>
        </div>

        <div>
            <button type="submit">Atualizar</button>
            <a href="/admin/users" class="btn-secondary-custom">Voltar para a Lista</a>
        </div>
    </form>
</div>