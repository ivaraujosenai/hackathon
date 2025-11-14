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
    // Regra: computador, rede, software, impressora -> TI; o resto -> Manutenção
    $categoria_id = (int)$_POST['categoria_id'];
    $setor_responsavel_id = determinar_setor_por_categoria($conexao, $categoria_id);

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

function determinar_setor_por_categoria($conexao, $categoria_id) {
    // Busca o nome da categoria no banco para aplicar regras baseadas em texto
    $categoria_id = (int)$categoria_id;
    $query = "SELECT nome_categoria FROM categorias WHERE id_categoria = ? LIMIT 1";
    $stmt = $conexao->prepare($query);
    if ($stmt) {
        $stmt->bind_param('i', $categoria_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $nome = strtolower($row['nome_categoria'] ?? '');
    } else {
        $nome = '';
    }

    // Palavras-chave que devem ir para TI
    $keywords_ti = ['computador', 'computadores', 'rede', 'internet', 'software', 'impressora', 'impressoras'];
    $is_ti = false;
    foreach ($keywords_ti as $kw) {
        if (strpos($nome, $kw) !== false) {
            $is_ti = true;
            break;
        }
    }

    // Padrões de busca para localizar o setor no banco (mais robusto que IDs fixos)
    $patterns_ti = ['%ti%', '%tecnolog%', '%inform%'];
    $patterns_manut = ['%manutenc%', '%manutenção%', '%manut%'];

    // Função auxiliar para buscar setor por padrões
    $buscar_setor_por_padroes = function($conexao, $patterns) {
        foreach ($patterns as $pat) {
            $query = "SELECT id_setor FROM setores WHERE LOWER(nome_setor) LIKE ? LIMIT 1";
            $stmt = $conexao->prepare($query);
            if (!$stmt) continue;
            $lower = strtolower($pat);
            $stmt->bind_param('s', $lower);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res && $row = $res->fetch_assoc()) {
                $stmt->close();
                return (int)$row['id_setor'];
            }
            $stmt->close();
        }
        return null;
    };

    if ($is_ti) {
        // Tenta encontrar setor TI por padrões
        $id = $buscar_setor_por_padroes($conexao, $patterns_ti);
        if ($id) return $id;
    }

    // Se não é TI ou não encontrou setor TI, tenta Manutenção
    $id = $buscar_setor_por_padroes($conexao, $patterns_manut);
    if ($id) return $id;

    // Fallbacks: tenta retornar setor padrão por nome exato
    $fallbacks = $is_ti ? ['ti'] : ['manutenção', 'manutencao', 'manut'];
    foreach ($fallbacks as $fb) {
        $q = "SELECT id_setor FROM setores WHERE LOWER(nome_setor) = ? LIMIT 1";
        $s = $conexao->prepare($q);
        if (!$s) continue;
        $lowerfb = strtolower($fb);
        $s->bind_param('s', $lowerfb);
        $s->execute();
        $r = $s->get_result();
        if ($r && $rw = $r->fetch_assoc()) {
            $s->close();
            return (int)$rw['id_setor'];
        }
        $s->close();
    }

    // Último recurso: retornar 1 (TI) se identificado, ou 2 (Manutenção)
    return $is_ti ? 1 : 2;
}

?>
