<?php
// Funções reutilizadas em todas as páginas do sistema.

// Fuso horário usado nas datas
date_default_timezone_set('America/Sao_Paulo');

session_start();

// Pasta onde ficam os arquivos JSON
define('PASTA_DADOS', __DIR__ . '/../dados/');

// Lê um arquivo JSON e devolve um array. Se não existir, devolve array vazio.
function lerJson($nomeArquivo)
{
    $caminho = PASTA_DADOS . $nomeArquivo;

    if (!file_exists($caminho)) {
        return [];
    }

    $conteudo = file_get_contents($caminho);
    $dados = json_decode($conteudo, true);

    if (!is_array($dados)) {
        return [];
    }

    return $dados;
}

// Grava um array em um arquivo JSON.
function salvarJson($nomeArquivo, $dados)
{
    $caminho = PASTA_DADOS . $nomeArquivo;
    $json = json_encode(array_values($dados), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($caminho, $json, LOCK_EX) !== false;
}

// Gera o próximo id de uma lista (maior id + 1).
function proximoId($lista)
{
    $maior = 0;
    foreach ($lista as $item) {
        if ($item['id'] > $maior) {
            $maior = $item['id'];
        }
    }
    return $maior + 1;
}

// Escapa textos antes de mostrar no HTML (evita que código seja executado).
function e($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

// Redireciona para outra página e encerra o script.
function redirecionar($pagina)
{
    header('Location: ' . $pagina);
    exit;
}

// Verifica se existe usuário conectado.
function usuarioLogado()
{
    return isset($_SESSION['usuario_id']);
}

// Bloqueia páginas internas para quem não fez login.
function exigirLogin()
{
    if (!usuarioLogado()) {
        definirMensagem('erro', 'Faça login para acessar esta página.');
        redirecionar('login.php');
    }
}

// Guarda uma mensagem na sessão para ser mostrada na próxima página.
function definirMensagem($tipo, $texto)
{
    $_SESSION['mensagem'] = ['tipo' => $tipo, 'texto' => $texto];
}

// Mostra a mensagem guardada (se houver) e apaga da sessão.
function mostrarMensagem()
{
    if (isset($_SESSION['mensagem'])) {
        $tipo = $_SESSION['mensagem']['tipo'];
        $texto = $_SESSION['mensagem']['texto'];
        unset($_SESSION['mensagem']);
        echo '<div class="alerta alerta-' . e($tipo) . '" role="alert">' . e($texto) . '</div>';
    }
}

// Procura um evento pelo id. Devolve o evento ou null.
function buscarEvento($id)
{
    $eventos = lerJson('eventos.json');
    foreach ($eventos as $evento) {
        if ($evento['id'] == $id) {
            return $evento;
        }
    }
    return null;
}

// Verifica se o usuário já agendou o evento.
function jaAgendou($usuarioId, $eventoId)
{
    $agendamentos = lerJson('agendamentos.json');
    foreach ($agendamentos as $agendamento) {
        if ($agendamento['usuario_id'] == $usuarioId && $agendamento['evento_id'] == $eventoId) {
            return true;
        }
    }
    return false;
}

// Ordena eventos pela data e horário (do mais próximo para o mais distante).
function ordenarEventos($eventos)
{
    usort($eventos, function ($a, $b) {
        return strcmp($a['data'] . ' ' . $a['horario'], $b['data'] . ' ' . $b['horario']);
    });
    return $eventos;
}

// Converte "2026-12-02" em "02/12/2026".
function formatarData($data)
{
    $partes = explode('-', $data);
    if (count($partes) != 3) {
        return $data;
    }
    return $partes[2] . '/' . $partes[1] . '/' . $partes[0];
}

// Verifica se a data do evento já passou.
function eventoPassou($evento)
{
    return ($evento['data'] . ' ' . $evento['horario']) < date('Y-m-d H:i');
}

// Valida o formato básico de e-mail.
function emailValido($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
