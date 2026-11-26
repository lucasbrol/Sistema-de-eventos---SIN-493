<?php
// Recebe o formulário de agendamento e grava no JSON.
require_once 'includes/funcoes.php';
exigirLogin();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    redirecionar('eventos.php');
}

$eventoId = (int) ($_POST['evento_id'] ?? 0);
$evento = buscarEvento($eventoId);

if ($evento == null) {
    definirMensagem('erro', 'Evento não encontrado.');
    redirecionar('eventos.php');
}

if (eventoPassou($evento)) {
    definirMensagem('erro', 'Não é possível agendar um evento que já aconteceu.');
    redirecionar('detalhes.php?id=' . $eventoId);
}

// Impede agendar o mesmo evento duas vezes
if (jaAgendou($_SESSION['usuario_id'], $eventoId)) {
    definirMensagem('erro', 'Você já possui agendamento neste evento.');
    redirecionar('detalhes.php?id=' . $eventoId);
}

$agendamentos = lerJson('agendamentos.json');
$agendamentos[] = [
    'id' => proximoId($agendamentos),
    'usuario_id' => $_SESSION['usuario_id'],
    'evento_id' => $eventoId,
    'criado_em' => date('Y-m-d H:i:s'),
];

if (salvarJson('agendamentos.json', $agendamentos)) {
    definirMensagem('sucesso', 'Agendamento realizado com sucesso!');
} else {
    definirMensagem('erro', 'Não foi possível salvar o agendamento.');
}

redirecionar('detalhes.php?id=' . $eventoId);
