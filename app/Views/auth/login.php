<?php
// Não precisa mais de session_start() aqui. A sessão será iniciada pelo Core/Session.php
// e o erro será passado via $data para a View.

// A variável $error_message virá do Controller para esta View.
// Se o Controller não passar, ela será nula.
$error_message = $data['error_message'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
     <base href="http://projeto.local/" /> 
     <link rel="stylesheet" type="text/css" href="assets/css/stylelogin.css">
</head>
<body>
    <form method="post" action="http://projeto.local/login">
        <h2>Acesso ao Sistema</h2>

        <?php if (!empty($error_message)) { ?>
            <p style="color: red; padding: 10px; border: 1px solid red; background-color: #ffebeb; margin-bottom: 15px;">
                <?php echo htmlspecialchars($error_message); ?>
            </p>
        <?php } ?>

        <div>
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>
        </div>
        <div>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
        </div>
        <div>
            <label for="perfil">Perfil:</label>
            <select id="perfil" name="perfil" required>
                <option value="">Selecione o Perfil</option>
                <option value="admin">Administrador</option>
                <option value="professor">Professor</option>
                <option value="coordenador">Coordenador</option>
                <option value="secretaria">Secretaria</option>
                <option value="administrativo">Administrativo</option>
                <option value="aluno">Aluno</option>
            </select>
        </div>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>