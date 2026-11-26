<?php
require_once 'includes/funcoes.php';
exigirLogin();

$eventos = ordenarEventos(lerJson('eventos.json'));

$titulo = 'Eventos';
$paginaAtual = 'eventos.php';
require 'includes/cabecalho.php';
?>

<div class="titulo-secao">
    <h1>Eventos</h1>
    <a href="criar-evento.php" class="botao botao-primario">+ Cadastrar evento</a>
</div>

<?php if (count($eventos) == 0): ?>
    <p class="vazio">Nenhum evento cadastrado ainda.</p>
<?php else: ?>
    <div class="campo campo-busca">
        <label for="busca">Buscar evento</label>
        <input type="search" id="busca" placeholder="Digite o nome ou o local">
    </div>

    <ul class="lista-eventos" id="listaEventos">
        <?php foreach ($eventos as $evento): ?>
            <li class="cartao-evento <?= eventoPassou($evento) ? 'encerrado' : '' ?>"
                data-busca="<?= e($evento["nome"] . " " . $evento["local"]) ?>">
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
                    <?php if (eventoPassou($evento)): ?>
                        <span class="etiqueta etiqueta-cinza">Encerrado</span>
                    <?php elseif (jaAgendou($_SESSION['usuario_id'], $evento['id'])): ?>
                        <span class="etiqueta">Agendado</span>
                    <?php endif; ?>
                </div>
                <a href="detalhes.php?id=<?= $evento['id'] ?>" class="botao botao-secundario">Ver detalhes</a>
            </li>
        <?php endforeach; ?>
    </ul>
    <p class="vazio oculto" id="semResultados">Nenhum evento encontrado para essa busca.</p>
<?php endif; ?>

<?php require 'includes/rodape.php'; ?>
