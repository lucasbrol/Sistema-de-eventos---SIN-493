<?php
require_once 'includes/funcoes.php';
exigirLogin();

$erros = [];
$dados = ['nome' => '', 'descricao' => '', 'data' => '', 'horario' => '', 'local' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($dados as $campo => $valor) {
        $dados[$campo] = trim($_POST[$campo] ?? '');
    }

    if ($dados['nome'] == '') {
        $erros[] = 'Informe o nome do evento.';
    }
    if ($dados['descricao'] == '') {
        $erros[] = 'Informe a descrição.';
    }

    // Confere se a data existe (formato AAAA-MM-DD)
    $partes = explode('-', $dados['data']);
    if (count($partes) != 3 || !checkdate((int) $partes[1], (int) $partes[2], (int) $partes[0])) {
        $erros[] = 'Informe uma data válida.';
    } elseif ($dados['data'] < date('Y-m-d')) {
        $erros[] = 'A data do evento não pode estar no passado.';
    }

    // Confere o horário (formato HH:MM)
    if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $dados['horario'])) {
        $erros[] = 'Informe um horário válido.';
    }

    if ($dados['local'] == '') {
        $erros[] = 'Informe o local.';
    }

    if (count($erros) == 0) {
        $eventos = lerJson('eventos.json');
        $novoId = proximoId($eventos);

        $eventos[] = [
            'id' => $novoId,
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'],
            'data' => $dados['data'],
            'horario' => $dados['horario'],
            'local' => $dados['local'],
            'criado_por' => $_SESSION['usuario_id'],
            'criado_em' => date('Y-m-d H:i:s'),
        ];

        if (salvarJson('eventos.json', $eventos)) {
            definirMensagem('sucesso', 'Evento cadastrado com sucesso!');
            redirecionar('detalhes.php?id=' . $novoId);
        } else {
            $erros[] = 'Não foi possível salvar o evento. Tente novamente.';
        }
    }
}

$titulo = 'Cadastrar evento';
$paginaAtual = 'criar-evento.php';
require 'includes/cabecalho.php';
?>

<h1>Cadastrar evento</h1>
<p class="subtitulo">Qualquer usuário conectado pode cadastrar um evento.</p>

<?php if (count($erros) > 0): ?>
    <div class="alerta alerta-erro" role="alert">
        <ul>
            <?php foreach ($erros as $erro): ?>
                <li><?= e($erro) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="criar-evento.php" method="post" class="formulario formulario-largo" id="formEvento" novalidate>
    <div class="campo">
        <label for="nome">Nome do evento</label>
        <input type="text" id="nome" name="nome" value="<?= e($dados['nome']) ?>" required maxlength="120">
        <span class="erro-campo"></span>
    </div>

    <div class="campo">
        <label for="descricao">Descrição</label>
        <textarea id="descricao" name="descricao" rows="5" required><?= e($dados['descricao']) ?></textarea>
        <span class="erro-campo"></span>
    </div>

    <div class="linha-campos">
        <div class="campo">
            <label for="data">Data</label>
            <input type="date" id="data" name="data" value="<?= e($dados['data']) ?>" min="<?= date('Y-m-d') ?>" required>
            <span class="erro-campo"></span>
        </div>

        <div class="campo">
            <label for="horario">Horário</label>
            <input type="time" id="horario" name="horario" value="<?= e($dados['horario']) ?>" required>
            <span class="erro-campo"></span>
        </div>
    </div>

    <div class="campo">
        <label for="local">Local</label>
        <input type="text" id="local" name="local" value="<?= e($dados['local']) ?>" required maxlength="120">
        <span class="erro-campo"></span>
    </div>

    <button type="submit" class="botao botao-primario">Salvar evento</button>
</form>

<?php require 'includes/rodape.php'; ?>
