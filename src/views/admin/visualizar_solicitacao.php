<?php
session_start();
require_once '../../../config/database.php';
require_once '../../../templates/header.php';

// Protege a página e obtém dados do admin
if (!isset($_SESSION['usuario_admin_id'])) {
    header('Location: login.php?error=Acesso negado.');
    exit;
}
$id_usuario_admin = $_SESSION['usuario_admin_id'];

// Valida o ID da solicitação
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='text-red-500 text-center'>ID de solicitação inválido.</div>";
    require_once '../../../templates/footer.php';
    exit;
}
$id_solicitacao = (int)$_GET['id'];

$conexao = conectar_banco();

// Busca os detalhes da solicitação
$query_solicitacao = "
    SELECT s.*, c.nome_categoria, setor.nome_setor
    FROM solicitacoes s
    JOIN categorias c ON s.categoria_id = c.id_categoria
    JOIN setores setor ON s.setor_responsavel_id = setor.id_setor
    WHERE s.id_solicitacao = ?
";
$stmt_solicitacao = $conexao->prepare($query_solicitacao);
$stmt_solicitacao->bind_param("i", $id_solicitacao);
$stmt_solicitacao->execute();
$solicitacao = $stmt_solicitacao->get_result()->fetch_assoc();
$stmt_solicitacao->close();

// Busca o histórico de movimentações
$query_movimentacoes = "
    SELECT m.data_movimentacao, m.novo_status, m.resposta, u.nome_usuario
    FROM movimentacoes m
    LEFT JOIN usuarios_admin u ON m.usuario_admin_id = u.id_usuario
    WHERE m.solicitacao_id = ?
    ORDER BY m.data_movimentacao DESC
";
$stmt_movimentacoes = $conexao->prepare($query_movimentacoes);
$stmt_movimentacoes->bind_param("i", $id_solicitacao);
$stmt_movimentacoes->execute();
$movimentacoes = $stmt_movimentacoes->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_movimentacoes->close();

$conexao->close();

if (!$solicitacao) {
    echo "<div class='text-red-500 text-center'>Solicitação não encontrada.</div>";
    require_once '../../../templates/footer.php';
    exit;
}
?>

<div class="max-w-4xl mx-auto">
    <a href="dashboard.php" class="text-blue-600 dark:text-blue-400 hover:underline mb-6 inline-block">&larr; Voltar ao Painel</a>

    <!-- Detalhes da Solicitação -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-8">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Solicitação #<?= htmlspecialchars($solicitacao['id_solicitacao']) ?></h1>
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-200 text-blue-800"><?= htmlspecialchars($solicitacao['status']) ?></span>
            </div>
            <!-- Conteúdo dos detalhes (similar ao acompanhar.php) -->
        </div>
    </div>

    <!-- Formulário de Interação do Admin -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-8">
        <div class="p-6">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Atualizar Status e Responder</h2>
            <form action="../../controllers/solicitacao_controller.php?action=update_status" method="POST">
                <input type="hidden" name="id_solicitacao" value="<?= $id_solicitacao ?>">
                <input type="hidden" name="id_usuario_admin" value="<?= $id_usuario_admin ?>">

                <div class="mb-4">
                    <label for="novo_status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alterar Status</label>
                    <select id="novo_status" name="novo_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600">
                        <option value="Aberta" <?= $solicitacao['status'] == 'Aberta' ? 'selected' : '' ?>>Aberta</option>
                        <option value="Em Andamento" <?= $solicitacao['status'] == 'Em Andamento' ? 'selected' : '' ?>>Em Andamento</option>
                        <option value="Concluída" <?= $solicitacao['status'] == 'Concluída' ? 'selected' : '' ?>>Concluída</option>
                        <option value="Cancelada" <?= $solicitacao['status'] == 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="resposta" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Adicionar Resposta/Observação</label>
                    <textarea id="resposta" name="resposta" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600"></textarea>
                </div>

                <div class="text-right">
                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5">Atualizar Solicitação</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Histórico de Movimentações -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Histórico de Andamento</h2>
            <div class="space-y-4">
                <?php if (empty($movimentacoes)): ?>
                    <p class="text-gray-500 dark:text-gray-400">Nenhuma movimentação registrada ainda.</p>
                <?php else: ?>
                    <?php foreach ($movimentacoes as $mov): ?>
                        <div class="p-4 border-l-4 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 rounded-r-lg">
                            <p class="text-sm text-gray-500 dark:text-gray-400"><?= date('d/m/Y H:i', strtotime($mov['data_movimentacao'])) ?> por <?= htmlspecialchars($mov['nome_usuario'] ?? 'Sistema') ?></p>
                            <p class="font-semibold">Status alterado para: <?= htmlspecialchars($mov['novo_status']) ?></p>
                            <?php if (!empty($mov['resposta'])): ?>
                                <p class="mt-2 text-gray-700 dark:text-gray-300"><strong>Resposta:</strong> <?= htmlspecialchars($mov['resposta']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                 <div class="p-4 border-l-4 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 rounded-r-lg">
                    <p class="text-sm text-gray-500 dark:text-gray-400"><?= date('d/m/Y H:i', strtotime($solicitacao['data_abertura'])) ?></p>
                    <p class="font-semibold">Solicitação Criada</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../../../templates/footer.php';
?>
