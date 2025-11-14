<?php
session_start();
require_once '../../config/database.php';

$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':
        handle_login();
        break;
    case 'logout':
        handle_logout();
        break;
    default:
        header('Location: ../views/admin/login.php');
        exit;
}

function handle_login() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../views/admin/login.php');
        exit;
    }

    $email = $_POST['email_usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        header('Location: ../views/admin/login.php?error=Email e senha são obrigatórios.');
        exit;
    }

    $conexao = conectar_banco();

    $query = "SELECT id_usuario, nome_usuario, senha_hash, setor_id FROM usuarios_admin WHERE email_usuario = ?";
    $stmt = $conexao->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Verifica a senha
        if (password_verify($senha, $usuario['senha_hash'])) {
            // Login bem-sucedido, armazena dados na sessão
            $_SESSION['usuario_admin_id'] = $usuario['id_usuario'];
            $_SESSION['usuario_admin_nome'] = $usuario['nome_usuario'];
            $_SESSION['usuario_admin_setor_id'] = $usuario['setor_id'];
            
            // Redireciona para o painel de controle do administrador
            header('Location: ../views/admin/dashboard.php');
            exit;
        }
    }

    // Se chegou até aqui, o login falhou
    header('Location: ../views/admin/login.php?error=Credenciais inválidas.');
    $stmt->close();
    $conexao->close();
    exit;
}

function handle_logout() {
    session_unset();
    session_destroy();
    header('Location: ../../index.php');
    exit;
}
?>
