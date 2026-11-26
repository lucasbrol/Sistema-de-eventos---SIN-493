<?php
// Encerra a sessão do usuário.
require_once 'includes/funcoes.php';

$_SESSION = [];
session_destroy();

// Inicia uma sessão nova só para mostrar a mensagem no login
session_start();
definirMensagem('sucesso', 'Você saiu do sistema.');

redirecionar('login.php');
