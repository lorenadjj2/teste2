<center>
    <h1>Lista de Usuários</h1>
    <p>Estes são os usuários cadastrados no sistema!</p>
</center>

<div class="table-wrapper">
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Nome do Usuário</th>
                <th>E-mail do Usuário</th>
                <th>Perfil</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (empty($data['users'])) {
        ?>
            <tr>
                <td colspan="4" style="text-align: center;">Nenhum usuário foi cadastrado ainda.</td>
            </tr>
        <?php
        } else {
            foreach ($data['users'] as $usuario) {
        ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario->nome ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($usuario->email ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($usuario->perfil ?? 'N/A'); ?></td>
                    <td>
                        <a href="http://projetoges.local/admin/users/edit/<?php echo htmlspecialchars($usuario->cod_usuario ?? ''); ?>">Editar</a> |
                        <a href="/admin/users/delete/<?php echo htmlspecialchars($usuario->cod_usuario ?? ''); ?>" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                    </td>
                </tr>
        <?php
            }
        }
        ?>
        </tbody>
    </table>
</div>

<br><br>