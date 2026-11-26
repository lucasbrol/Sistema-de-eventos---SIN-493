<?php
require_once 'includes/funcoes.php';

if (usuarioLogado()) {
    redirecionar('inicio.php');
}

$erros = [];
$nome = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmacao = $_POST['confirmacao'] ?? '';

    // Validações no servidor (repetem as do JavaScript)
    if ($nome == '') {
        $erros[] = 'Informe o nome.';
    }
    if ($email == '') {
        $erros[] = 'Informe o e-mail.';
    } elseif (!emailValido($email)) {
        $erros[] = 'Informe um e-mail válido.';
    }
    if (strlen($senha) < 6) {
        $erros[] = 'A senha deve ter pelo menos 6 caracteres.';
    }
    if ($senha != $confirmacao) {
        $erros[] = 'As senhas não são iguais.';
    }

    $usuarios = lerJson('usuarios.json');

    // Impede dois usuários com o mesmo e-mail
    foreach ($usuarios as $usuario) {
        if (strtolower($usuario['email']) == strtolower($email)) {
            $erros[] = 'Já existe um usuário cadastrado com este e-mail.';
            break;
        }
    }

    if (count($erros) == 0) {
        $usuarios[] = [
            'id' => proximoId($usuarios),
            'nome' => $nome,
            'email' => strtolower($email),
            'senha' => password_hash($senha, PASSWORD_DEFAULT),
            'criado_em' => date('Y-m-d H:i:s'),
        ];

        if (salvarJson('usuarios.json', $usuarios)) {
            definirMensagem('sucesso', 'Cadastro realizado! Agora é só entrar com seu e-mail e senha.');
            redirecionar('login.php');
        } else {
            $erros[] = 'Não foi possível salvar o cadastro. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | Agenda UFV</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body class="pagina-acesso">
    <main class="cartao-acesso">
        <a href="login.php" class="logo logo-grande">Agenda<span>UFV</span></a>
        <h1>Criar conta</h1>
        <p class="subtitulo">Preencha os dados abaixo para se cadastrar.</p>

        <?php if (count($erros) > 0): ?>
            <div class="alerta alerta-erro" role="alert">
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <li><?= e($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="cadastro.php" method="post" class="formulario" id="formCadastro" novalidate>
            <div class="campo">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" value="<?= e($nome) ?>" required autocomplete="name">
                <span class="erro-campo"></span>
            </div>

            <div class="campo">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?= e($email) ?>" required autocomplete="email">
                <span class="erro-campo"></span>
            </div>

            <div class="campo">
                <label for="senha">Senha <small>(mínimo 6 caracteres)</small></label>
                <div class="grupo-senha">
                    <input type="password" id="senha" name="senha" required minlength="6" autocomplete="new-password">
                    <button type="button" class="mostrar-senha" data-alvo="senha">Mostrar</button>
                </div>
                <span class="erro-campo"></span>
            </div>

            <div class="campo">
                <label for="confirmacao">Confirmar senha</label>
                <div class="grupo-senha">
                    <input type="password" id="confirmacao" name="confirmacao" required autocomplete="new-password">
                    <button type="button" class="mostrar-senha" data-alvo="confirmacao">Mostrar</button>
                </div>
                <span class="erro-campo"></span>
            </div>

            <button type="submit" class="botao botao-primario botao-bloco">Cadastrar</button>
        </form>

        <p class="link-alternativo">Já tem conta? <a href="login.php">Entrar</a></p>
    </main>

    <script src="js/scripts.js"></script>
</body>
</html>
