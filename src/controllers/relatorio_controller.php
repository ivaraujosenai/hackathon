<?php
session_start();
require_once '../../config/database.php';

// Protege o endpoint
if (!isset($_SESSION['usuario_admin_id'])) {
    http_response_code(403);
    die('Acesso negado.');
}

$action = $_GET['action'] ?? '';

if ($action === 'export_csv') {
    export_to_csv();
} else {
    http_response_code(400);
    die('Ação inválida.');
}

function export_to_csv() {
    $id_setor_admin = (int)($_SESSION['usuario_admin_setor_id'] ?? 0);
    if ($id_setor_admin === 0) {
        die('ID de setor inválido.');
    }

    $conexao = conectar_banco();

    // Query para buscar os dados
    $query = "
        SELECT 
            s.id_solicitacao,
            s.nome_solicitante,
            s.matricula_solicitante,
            s.cargo_solicitante,
            s.local_problema,
            c.nome_categoria,
            s.prioridade,
            s.status,
            s.data_abertura,
            (SELECT MAX(m.data_movimentacao) FROM movimentacoes m WHERE m.solicitacao_id = s.id_solicitacao) as data_ultima_atualizacao
        FROM solicitacoes s
        JOIN categorias c ON s.categoria_id = c.id_categoria
        WHERE s.setor_responsavel_id = ?
        ORDER BY s.data_abertura DESC
    ";

    $stmt = $conexao->prepare($query);
    $stmt->bind_param("i", $id_setor_admin);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Define os headers para o download do arquivo CSV
    $nome_arquivo = "relatorio_solicitacoes_" . date('Y-m-d') . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $nome_arquivo);

    // Abre o stream de saída do PHP
    $output = fopen('php://output', 'w');

    // Adiciona o BOM para UTF-8 para garantir compatibilidade com Excel
    fputs($output, "\xEF\xBB\xBF");

    // Adiciona o cabeçalho do CSV
    fputcsv($output, [
        'ID', 'Solicitante', 'Matricula', 'Cargo', 'Local', 
        'Categoria', 'Prioridade', 'Status', 'Data de Abertura', 'Última Atualização'
    ]);

    // Itera sobre os resultados e escreve no arquivo
    while ($linha = $resultado->fetch_assoc()) {
        fputcsv($output, $linha);
    }

    fclose($output);
    $stmt->close();
    $conexao->close();
    exit;
}
?>
