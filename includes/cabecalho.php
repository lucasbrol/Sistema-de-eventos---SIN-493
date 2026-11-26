<?php
// Cabeçalho comum das páginas internas.
// Antes de incluir, a página define $titulo e $paginaAtual.
$itensMenu = [
    'inicio.php' => 'Início',
    'eventos.php' => 'Eventos',
    'criar-evento.php' => 'Cadastrar evento',
    'meus-agendamentos.php' => 'Meus agendamentos',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> | Agenda UFV</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <header class="topo">
        <div class="topo-conteudo">
            <a href="inicio.php" class="logo">Agenda<span>UFV</span></a>

            <button type="button" class="botao-menu" id="botaoMenu" aria-expanded="false" aria-controls="menuPrincipal">
                Menu
            </button>

            <nav class="menu" id="menuPrincipal">
                <ul>
                    <?php foreach ($itensMenu as $link => $nome): ?>
                        <li>
                            <a href="<?= $link ?>" class="<?= $paginaAtual == $link ? 'ativo' : '' ?>"><?= $nome ?></a>
                        </li>
                    <?php endforeach; ?>
                    <li><a href="logout.php" class="sair">Sair</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="conteudo">
        <?php mostrarMensagem(); ?>
