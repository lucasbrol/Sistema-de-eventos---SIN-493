<?php
// Página de entrada: manda para o início se estiver logado, senão para o login.
require_once 'includes/funcoes.php';

if (usuarioLogado()) {
    redirecionar('inicio.php');
}

redirecionar('login.php');
