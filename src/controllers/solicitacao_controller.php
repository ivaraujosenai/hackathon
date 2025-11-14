<?php
session_start();
require_once '../../config/database.php';

// Garante que a action sempre estará definida
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'create':
        handle_create_solicitacao();
        break;
    case 'update_status':
        handle_update_status();
        break;
    // Outros casos como 'update', 'delete', 'list' podem ser adicionados aqui
    default:
        // Redireciona para uma página de erro ou para a lista de solicitações
        header('Location: ../views/solicitante/minhas_solicitacoes.php');
        exit;
}

function handle_update_status() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        die("Método não permitido.");
    }

    // Validação
    $id_solicitacao = (int)($_POST['id_solicitacao'] ?? 0);
    $id_usuario_admin = (int)($_POST['id_usuario_admin'] ?? 0);
    $novo_status = $_POST['novo_status'] ?? '';
    $resposta = $_POST['resposta'] ?? '';

    if ($id_solicitacao === 0 || $id_usuario_admin === 0 || empty($novo_status)) {
        header("Location: ../views/admin/visualizar_solicitacao.php?id={$id_solicitacao}&error=Dados inválidos.");
        exit;
    }

    $conexao = conectar_banco();
    $conexao->begin_transaction();

    try {
        // 1. Atualiza o status na tabela de solicitações
        $query_update = "UPDATE solicitacoes SET status = ? WHERE id_solicitacao = ?";
        $stmt_update = $conexao->prepare($query_update);
        $stmt_update->bind_param("si", $novo_status, $id_solicitacao);
        $stmt_update->execute();
        $stmt_update->close();

        // 2. Insere o registro na tabela de movimentações
        $query_mov = "INSERT INTO movimentacoes (solicitacao_id, usuario_admin_id, novo_status, resposta) VALUES (?, ?, ?, ?)";
        $stmt_mov = $conexao->prepare($query_mov);
        $stmt_mov->bind_param("iiss", $id_solicitacao, $id_usuario_admin, $novo_status, $resposta);
        $stmt_mov->execute();
        $stmt_mov->close();

        // Se tudo deu certo, comita a transação
        $conexao->commit();

        header("Location: ../views/admin/visualizar_solicitacao.php?id={$id_solicitacao}&success=Status atualizado.");

    } catch (mysqli_sql_exception $exception) {
        $conexao->rollback();
        header("Location: ../views/admin/visualizar_solicitacao.php?id={$id_solicitacao}&error=" . urlencode($exception->getMessage()));
    } finally {
        $conexao->close();
        exit;
    }
}


function handle_create_solicitacao() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        die("Método não permitido.");
    }

    // Validação básica dos campos obrigatórios
    $campos_obrigatorios = [
        'nome_solicitante', 'matricula_solicitante', 'cargo_solicitante', 
        'local_problema', 'descricao', 'categoria_id', 'prioridade'
    ];
    foreach ($campos_obrigatorios as $campo) {
        if (empty($_POST[$campo])) {
            // Idealmente, redirecionar com uma mensagem de erro
            die("Erro: O campo '{$campo}' é obrigatório.");
        }
    }

    $conexao = conectar_banco();

    // --- Tratamento do Upload da Imagem ---
    $path_imagem = null;
    if (isset($_FILES['path_imagem']) && $_FILES['path_imagem']['error'] == 0) {
        $diretorio_uploads = '../../public/uploads/';
        if (!is_dir($diretorio_uploads)) {
            mkdir($diretorio_uploads, 0777, true);
        }
        
        $nome_arquivo = uniqid() . '_' . basename($_FILES['path_imagem']['name']);
        $caminho_completo = $diretorio_uploads . $nome_arquivo;
        
        // Move o arquivo para o diretório de uploads
        if (move_uploaded_file($_FILES['path_imagem']['tmp_name'], $caminho_completo)) {
            // Armazena o caminho relativo que será acessível publicamente
            $path_imagem = 'public/uploads/' . $nome_arquivo;
        } else {
            // Tratar falha no upload
            die("Erro ao fazer upload da imagem.");
        }
    }

    // --- Define o setor responsável com base na categoria ---
    // Esta é uma regra de negócio que pode ser ajustada
    $categoria_id = (int)$_POST['categoria_id'];
    $setor_responsavel_id = determinar_setor_por_categoria($categoria_id);

    // Prepara a query para evitar SQL Injection
    $query = "INSERT INTO solicitacoes (
                nome_solicitante, matricula_solicitante, cargo_solicitante, email_solicitante,
                local_problema, descricao, categoria_id, prioridade, path_imagem, setor_responsavel_id
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexao->prepare($query);

    if (!$stmt) {
        die("Erro na preparação da query: " . $conexao->error);
    }

    // O email do solicitante não está no form, pode ser adicionado ou deixado em branco
    $email_solicitante = $_POST['email_solicitante'] ?? ''; 

    $stmt->bind_param(
        "ssssssissi",
        $_POST['nome_solicitante'],
        $_POST['matricula_solicitante'],
        $_POST['cargo_solicitante'],
        $email_solicitante,
        $_POST['local_problema'],
        $_POST['descricao'],
        $categoria_id,
        $_POST['prioridade'],
        $path_imagem,
        $setor_responsavel_id
    );

    if ($stmt->execute()) {
        // Redireciona para uma página de sucesso ou de acompanhamento
        $id_solicitacao = $stmt->insert_id;
        header("Location: ../views/solicitante/acompanhar.php?id=" . $id_solicitacao . "&success=true");
    } else {
        // Redireciona com mensagem de erro
        header("Location: ../views/solicitante/nova_solicitacao.php?error=" . urlencode($stmt->error));
    }

    $stmt->close();
    $conexao->close();
}

function determinar_setor_por_categoria($categoria_id) {
    // Mapeamento de Categoria ID para Setor ID
    // 1: TI, 2: Manutenção, 3: Secretaria, 4: Estrutural
    $mapeamento = [
        1 => 1, // Computador -> TI
        2 => 1, // Impressora -> TI
        3 => 1, // Rede/Internet -> TI
        4 => 1, // Software -> TI
        5 => 2, // Elétrica -> Manutenção
        6 => 2, // Hidráulica -> Manutenção
        7 => 4, // Mobiliário -> Estrutural
    ];

    return $mapeamento[$categoria_id] ?? 3; // Padrão para Secretaria se não mapeado
}

?>
