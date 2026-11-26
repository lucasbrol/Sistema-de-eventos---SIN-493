<?php
// Remove um agendamento do usuário conectado.
require_once 'includes/funcoes.php';
exigirLogin();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    redirecionar('meus-agendamentos.php');
}

$eventoId = (int) ($_POST['evento_id'] ?? 0);
$destino = ($_POST['voltar'] ?? '') == 'detalhes' ? 'detalhes.php?id=' . $eventoId : 'meus-agendamentos.php';

$agendamentos = lerJson('agendamentos.json');
$restantes = [];
$removeu = false;

// Mantém todos, menos o agendamento deste usuário neste evento
foreach ($agendamentos as $agendamento) {
    if ($agendamento['usuario_id'] == $_SESSION['usuario_id'] && $agendamento['evento_id'] == $eventoId) {
        $removeu = true;
    } else {
        $restantes[] = $agendamento;
    }
}

if (!$removeu) {
    definirMensagem('erro', 'Agendamento não encontrado.');
} elseif (salvarJson('agendamentos.json', $restantes)) {
    definirMensagem('sucesso', 'Agendamento cancelado.');
} else {
    definirMensagem('erro', 'Não foi possível cancelar o agendamento.');
}

redirecionar($destino);
