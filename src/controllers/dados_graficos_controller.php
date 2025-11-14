<?php
session_start();
require_once '../../config/database.php';

// Protege o endpoint
if (!isset($_SESSION['usuario_admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado.']);
    exit;
}

// Garante que o setor do admin seja um número
$id_setor_admin = (int)($_SESSION['usuario_admin_setor_id'] ?? 0);
if ($id_setor_admin === 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de setor inválido.']);
    exit;
}

header('Content-Type: application/json');
$conexao = conectar_banco();

try {
    // 1. Contagem por Status
    $query_status = "SELECT status, COUNT(*) as total FROM solicitacoes WHERE setor_responsavel_id = ? GROUP BY status";
    $stmt_status = $conexao->prepare($query_status);
    $stmt_status->bind_param("i", $id_setor_admin);
    $stmt_status->execute();
    $resultado_status = $stmt_status->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_status->close();

    $dados_status = ['labels' => [], 'data' => []];
    foreach ($resultado_status as $row) {
        $dados_status['labels'][] = $row['status'];
        $dados_status['data'][] = (int)$row['total'];
    }

    // 2. Contagem por Prioridade
    $query_prioridade = "SELECT prioridade, COUNT(*) as total FROM solicitacoes WHERE setor_responsavel_id = ? GROUP BY prioridade";
    $stmt_prioridade = $conexao->prepare($query_prioridade);
    $stmt_prioridade->bind_param("i", $id_setor_admin);
    $stmt_prioridade->execute();
    $resultado_prioridade = $stmt_prioridade->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_prioridade->close();

    $dados_prioridade = ['labels' => [], 'data' => []];
    foreach ($resultado_prioridade as $row) {
        $dados_prioridade['labels'][] = $row['prioridade'];
        $dados_prioridade['data'][] = (int)$row['total'];
    }

    // 3. Contagem por Categoria
    $query_categoria = "
        SELECT c.nome_categoria, COUNT(s.id_solicitacao) as total 
        FROM solicitacoes s
        JOIN categorias c ON s.categoria_id = c.id_categoria
        WHERE s.setor_responsavel_id = ?
        GROUP BY c.nome_categoria
        ORDER BY total DESC
    ";
    $stmt_categoria = $conexao->prepare($query_categoria);
    $stmt_categoria->bind_param("i", $id_setor_admin);
    $stmt_categoria->execute();
    $resultado_categoria = $stmt_categoria->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_categoria->close();

    $dados_categoria = ['labels' => [], 'data' => []];
    foreach ($resultado_categoria as $row) {
        $dados_categoria['labels'][] = $row['nome_categoria'];
        $dados_categoria['data'][] = (int)$row['total'];
    }

    echo json_encode([
        'por_status' => $dados_status,
        'por_prioridade' => $dados_prioridade,
        'por_categoria' => $dados_categoria,
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar dados para os gráficos.', 'details' => $e->getMessage()]);
} finally {
    $conexao->close();
}
?>
