<?php
include_once __DIR__ . '/../../layout/header.php';

$usuarios = $usuarios ?? [];
$searchTerm = $searchTerm ?? '';
?>

<div class="form-container">
    <h2>Consultar Usuários</h2>

    <form action="/admin/users/search" method="GET">
        <div class="form-group">
            <div class="label">
                <label for="userName">Consultar pelo nome:</label>
            </div>
            <div class="input">
                <input type="text" id="userName" name="nome" placeholder="Digite o nome do usuário..." value="<?php echo htmlspecialchars($searchTerm); ?>">
            </div>
        </div>
        <button type="submit">Consultar</button>
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Perfil</th>
                </tr>
            </thead>
            <tbody id="usuariosTableBody">
                <?php if (!empty($usuarios)): ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario->cod_usuario); ?></td>
                            <td><?php echo htmlspecialchars($usuario->nome); ?></td>
                            <td><?php echo htmlspecialchars($usuario->email); ?></td>
                            <td><?php echo htmlspecialchars($usuario->perfil); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">
                            <?php echo $searchTerm ? 'Nenhum usuário encontrado com esse nome.' : 'Digite um nome e clique em Consultar.'; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include_once __DIR__ . '/../../layout/footer.php';
?>