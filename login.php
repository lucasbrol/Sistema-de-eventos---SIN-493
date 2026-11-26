<?php
require_once 'includes/funcoes.php';

// Quem já está logado vai direto para o início
if (usuarioLogado()) {
    redirecionar('inicio.php');
}

$erro = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email == '' || $senha == '') {
        $erro = 'Preencha o e-mail e a senha.';
    } else {
        $usuarios = lerJson('usuarios.json');
        $usuarioEncontrado = null;

        foreach ($usuarios as $usuario) {
            if (strtolower($usuario['email']) == strtolower($email)) {
                $usuarioEncontrado = $usuario;
                break;
            }
        }

        if ($usuarioEncontrado && password_verify($senha, $usuarioEncontrado['senha'])) {
            // Gera um novo id de sessão por segurança
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $usuarioEncontrado['id'];
            $_SESSION['usuario_nome'] = $usuarioEncontrado['nome'];
            definirMensagem('sucesso', 'Login realizado com sucesso!');
            redirecionar('inicio.php');
        } else {
            $erro = 'E-mail ou senha incorretos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | Agenda UFV</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body class="pagina-acesso">
    <main class="cartao-acesso">
        <a href="login.php" class="logo logo-grande">Agenda<span>UFV</span></a>
        <h1>Entrar</h1>
        <p class="subtitulo">Acesse sua conta para ver e agendar eventos.</p>

        <?php mostrarMensagem(); ?>

        <?php if ($erro != ''): ?>
            <div class="alerta alerta-erro" role="alert"><?= e($erro) ?></div>
        <?php endif; ?>

        <form action="login.php" method="post" class="formulario" id="formLogin" novalidate>
            <div class="campo">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?= e($email) ?>" required autocomplete="email">
                <span class="erro-campo"></span>
            </div>

            <div class="campo">
                <label for="senha">Senha</label>
                <div class="grupo-senha">
                    <input type="password" id="senha" name="senha" required autocomplete="current-password">
                    <button type="button" class="mostrar-senha" data-alvo="senha">Mostrar</button>
                </div>
                <span class="erro-campo"></span>
            </div>

            <button type="submit" class="botao botao-primario botao-bloco">Entrar</button>
        </form>

        <p class="link-alternativo">Ainda não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
    </main>

    <script src="js/scripts.js"></script>
</body>
</html>
