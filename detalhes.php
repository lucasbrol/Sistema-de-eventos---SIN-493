<?php
require_once 'includes/funcoes.php';
exigirLogin();

$id = (int) ($_GET['id'] ?? 0);
$evento = buscarEvento($id);

if ($evento == null) {
    definirMensagem('erro', 'Evento não encontrado.');
    redirecionar('eventos.php');
}

$agendado = jaAgendou($_SESSION['usuario_id'], $evento['id']);
$passou = eventoPassou($evento);

// Conta quantas pessoas agendaram o evento
$totalInscritos = 0;
foreach (lerJson('agendamentos.json') as $agendamento) {
    if ($agendamento['evento_id'] == $evento['id']) {
        $totalInscritos++;
    }
}

$titulo = $evento['nome'];
$paginaAtual = 'eventos.php';
require 'includes/cabecalho.php';
?>

<a href="eventos.php" class="voltar">&larr; Voltar para eventos</a>

<article class="detalhes">
    <h1><?= e($evento['nome']) ?></h1>

    <dl class="dados-evento">
        <div>
            <dt>Data</dt>
            <dd><?= formatarData($evento['data']) ?></dd>
        </div>
        <div>
            <dt>Horário</dt>
            <dd><?= e($evento['horario']) ?></dd>
        </div>
        <div>
            <dt>Local</dt>
            <dd><?= e($evento['local']) ?></dd>
        </div>
        <div>
            <dt>Agendamentos</dt>
            <dd><?= $totalInscritos ?> pessoa(s)</dd>
        </div>
    </dl>

    <h2>Descrição</h2>
    <p class="descricao"><?= nl2br(e($evento['descricao'])) ?></p>

    <div class="acoes">
        <?php if ($passou): ?>
            <p class="etiqueta etiqueta-cinza">Este evento já aconteceu.</p>
        <?php elseif ($agendado): ?>
            <p class="etiqueta">Você já está agendado neste evento.</p>
            <form action="cancelar.php" method="post" class="form-cancelar">
                <input type="hidden" name="evento_id" value="<?= $evento['id'] ?>">
                <input type="hidden" name="voltar" value="detalhes">
                <button type="submit" class="botao botao-perigo">Cancelar agendamento</button>
            </form>
        <?php else: ?>
            <form action="agendar.php" method="post">
                <input type="hidden" name="evento_id" value="<?= $evento['id'] ?>">
                <button type="submit" class="botao botao-primario">Agendar participação</button>
            </form>
        <?php endif; ?>
    </div>
</article>

<?php require 'includes/rodape.php'; ?>
