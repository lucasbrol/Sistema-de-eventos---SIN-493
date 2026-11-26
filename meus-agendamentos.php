<?php
require_once 'includes/funcoes.php';
exigirLogin();

$agendamentos = lerJson('agendamentos.json');

// Junta cada agendamento do usuário com os dados do evento
$meusEventos = [];
foreach ($agendamentos as $agendamento) {
    if ($agendamento['usuario_id'] == $_SESSION['usuario_id']) {
        $evento = buscarEvento($agendamento['evento_id']);
        if ($evento != null) {
            $meusEventos[] = $evento;
        }
    }
}
$meusEventos = ordenarEventos($meusEventos);

$titulo = 'Meus agendamentos';
$paginaAtual = 'meus-agendamentos.php';
require 'includes/cabecalho.php';
?>

<h1>Meus agendamentos</h1>

<?php if (count($meusEventos) == 0): ?>
    <p class="vazio">Você ainda não agendou nenhum evento. <a href="eventos.php">Ver eventos disponíveis</a></p>
<?php else: ?>
    <ul class="lista-eventos">
        <?php foreach ($meusEventos as $evento): ?>
            <li class="cartao-evento <?= eventoPassou($evento) ? 'encerrado' : '' ?>">
                <div class="data-destaque">
                    <span class="dia"><?= substr($evento['data'], 8, 2) ?></span>
                    <span class="mes"><?= substr(formatarData($evento['data']), 3) ?></span>
                </div>
                <div class="info-evento">
                    <h3><?= e($evento['nome']) ?></h3>
                    <p>
                        <?= formatarData($evento['data']) ?> às <?= e($evento['horario']) ?>
                        &middot; <?= e($evento['local']) ?>
                    </p>
                </div>
                <div class="botoes-item">
                    <a href="detalhes.php?id=<?= $evento['id'] ?>" class="botao botao-secundario">Detalhes</a>
                    <form action="cancelar.php" method="post" class="form-cancelar">
                        <input type="hidden" name="evento_id" value="<?= $evento['id'] ?>">
                        <button type="submit" class="botao botao-perigo">Cancelar</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php require 'includes/rodape.php'; ?>
