<?php
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gerenciador_solicitacoes');

function conectar_banco() {
    $conexao = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conexao->connect_error) {
        die("Falha na conexão: " . $conexao->connect_error);
    }

    $conexao->set_charset("utf8mb4");

    return $conexao;
}
?>