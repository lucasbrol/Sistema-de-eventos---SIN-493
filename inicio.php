<?php
require_once 'includes/funcoes.php';
exigirLogin();

$eventos = ordenarEventos(lerJson('eventos.json'));
$agendamentos = lerJson('agendamentos.json');

// Separa apenas os eventos que ainda vão acontecer
$proximos = [];
foreach ($eventos as $evento) {
    if (!eventoPassou($evento)) {
        $proximos[] = $evento;
    }
}

// Conta quantos agendamentos o usuário tem
$totalMeus = 0;
foreach ($agendamentos as $agendamento) {
    if ($agendamento['usuario_id'] == $_SESSION['usuario_id']) {
        $totalMeus++;
    }
}

$titulo = 'Início';
$paginaAtual = 'inicio.php';
require 'includes/cabecalho.php';
?>

<section class="boas-vindas">
    <h1>Olá, <?= e($_SESSION['usuario_nome']) ?>!</h1>
    <p>Bem-vindo(a) ao sistema de agendamento de eventos. Confira o que vem por aí.</p>
</section>

<section class="resumo">
    <div class="resumo-item">
        <strong><?= count($proximos) ?></strong>
        <span>próximos eventos</span>
    </div>
    <div class="resumo-item">
        <strong><?= $totalMeus ?></strong>
        <span>meus agendamentos</span>
    </div>
    <a href="criar-evento.php" class="resumo-item resumo-acao">
        <strong>+</strong>
        <span>cadastrar evento</span>
    </a>
</section>

<section>
    <div class="titulo-secao">
        <h2>Próximos eventos</h2>
        <a href="eventos.php">Ver todos</a>
    </div>

    <?php if (count($proximos) == 0): ?>
        <p class="vazio">Nenhum evento futuro cadastrado. <a href="criar-evento.php">Que tal criar um?</a></p>
    <?php else: ?>
        <ul class="lista-eventos">
            <?php foreach (array_slice($proximos, 0, 3) as $evento): ?>
                <li class="cartao-evento">
                    <div class="data-destaque">
                        <span class="dia"><?= substr($evento['data'], 8, 2) ?></span>
                        <span class="mes"><?= substr(formatarData($evento['data']), 3) ?></span>
                    </div>
                    <div class="info-evento">
                        <h3><?= e($evento['nome']) ?></h3>
                        <p><?= e($evento['horario']) ?> &middot; <?= e($evento['local']) ?></p>
                    </div>
                    <a href="detalhes.php?id=<?= $evento['id'] ?>" class="botao botao-secundario">Detalhes</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<?php require 'includes/rodape.php'; ?>
