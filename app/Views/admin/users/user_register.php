<?php
// echo "DEBUG: user_register.php está sendo carregado!<br>";
?>

<div class="form-container">
    <h2>Cadastro de Usuário</h2>

    <form action="http://projetoges.local/admin/users/create" method="post">
        <div class="form-group">
            <div class="label">
                <label for="nome">Nome:</label>
            </div>
            <div class="input">
                <input type="text" id="nome" name="nome" required>
            </div>
        </div>

        <div class="form-group">
            <div class="label">
                <label for="email">Email:</label>
            </div>
            <div class="input">
                <input type="email" id="email" name="email" required>
            </div>
        </div>

        <div class="form-group">
            <div class="label">
                <label for="senha">Senha:</label>
            </div>
            <div class="input">
                <input type="password" id="senha" name="senha" required>
                <small>A senha deve ter pelo menos 6 caracteres.</small>
            </div>
        </div>

        <div class="form-group">
            <div class="label">
                <label for="confirmar_senha">Confirmar Senha:</label>
            </div>
            <div class="input">
                <input type="password" id="confirmar_senha" name="confirmar_senha" required>
            </div>
        </div>

        <div class="form-group">
            <div class="label">
                <label for="perfil">Perfil:</label>
            </div>
            <div class="input">
                <select id="perfil" name="perfil" required>
                    <option value="">Selecione o Perfil</option>
                    <option value="admin">Administrador</option>
                    <option value="professor">Professor</option>
                    <option value="secretaria">Secretaria</option>
                    <option value="coordenador">Coordenador</option>
                    <option value="suporte">Suporte</option>
                    <option value="administrativo">Administrativo</option>
                    <option value="aluno">Aluno</option>
                </select>
            </div>
        </div>

        <div>
            <button type="submit">Cadastrar</button>
        </div>

        <p class="mt-2"><a href="/">Já possui uma conta? Faça login.</a></p>
    </form>

    <?php
    if (!empty($data['cadastro_erro'])) {
        echo '<div class="error-message">' . htmlspecialchars($data['cadastro_erro']) . '</div>';
    }

    if (!empty($data['cadastro_sucesso'])) {
        echo '<div class="success-message">' . htmlspecialchars($data['cadastro_sucesso']) . '</div>';
    }
    ?>
</div>