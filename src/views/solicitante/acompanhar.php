<?php
require_once '../../../config/database.php';
require_once '../../../templates/header.php';

// Valida se o ID da solicitação foi passado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='text-red-500 text-center'>ID de solicitação inválido.</div>";
    require_once '../../../templates/footer.php';
    exit;
}

$id_solicitacao = (int)$_GET['id'];
$conexao = conectar_banco();

// Busca os detalhes da solicitação
$query_solicitacao = "
    SELECT 
        s.id_solicitacao, s.nome_solicitante, s.matricula_solicitante, s.local_problema,
        s.descricao, s.prioridade, s.path_imagem, s.data_abertura, s.status,
        c.nome_categoria,
        setor.nome_setor
    FROM solicitacoes s
    JOIN categorias c ON s.categoria_id = c.id_categoria
    JOIN setores setor ON s.setor_responsavel_id = setor.id_setor
    WHERE s.id_solicitacao = ?
";
$stmt_solicitacao = $conexao->prepare($query_solicitacao);
$stmt_solicitacao->bind_param("i", $id_solicitacao);
$stmt_solicitacao->execute();
$resultado = $stmt_solicitacao->get_result();

if ($resultado->num_rows === 0) {
    echo "<div class='text-red-500 text-center'>Solicitação não encontrada.</div>";
    require_once '../../../templates/footer.php';
    exit;
}
$solicitacao = $resultado->fetch_assoc();
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
?>

<div class="max-w-4xl mx-auto">
    
    <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
            <p class="font-bold">Sucesso!</p>
            <p>Sua solicitação foi enviada e registrada com o número <strong>#<?= htmlspecialchars($solicitacao['id_solicitacao']) ?></strong>. Guarde este número para futuras consultas.</p>
        </div>
    <?php endif; ?>

    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-6">Detalhes da Solicitação</h1>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-8">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Solicitação #<?= htmlspecialchars($solicitacao['id_solicitacao']) ?></h2>
                <span class="px-3 py-1 text-sm font-semibold rounded-full 
                    <?php 
                        switch ($solicitacao['status']) {
                            case 'Aberta': echo 'bg-blue-200 text-blue-800 dark:bg-blue-800 dark:text-blue-100'; break;
                            case 'Em Andamento': echo 'bg-yellow-200 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100'; break;
                            case 'Concluída': echo 'bg-green-200 text-green-800 dark:bg-green-800 dark:text-green-100'; break;
                            case 'Cancelada': echo 'bg-red-200 text-red-800 dark:bg-red-800 dark:text-red-100'; break;
                        }
                    ?>
                ">
                    <?= htmlspecialchars($solicitacao['status']) ?>
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 mt-6 text-gray-700 dark:text-gray-300">
                <p><strong>Solicitante:</strong> <?= htmlspecialchars($solicitacao['nome_solicitante']) ?></p>
                <p><strong>Matrícula:</strong> <?= htmlspecialchars($solicitacao['matricula_solicitante']) ?></p>
                <p><strong>Data de Abertura:</strong> <?= date('d/m/Y H:i', strtotime($solicitacao['data_abertura'])) ?></p>
                <p><strong>Prioridade:</strong> <?= htmlspecialchars($solicitacao['prioridade']) ?></p>
                <p><strong>Categoria:</strong> <?= htmlspecialchars($solicitacao['nome_categoria']) ?></p>
                <p><strong>Setor Responsável:</strong> <?= htmlspecialchars($solicitacao['nome_setor']) ?></p>
                <p class="md:col-span-2"><strong>Local do Problema:</strong> <?= htmlspecialchars($solicitacao['local_problema']) ?></p>
            </div>

            <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Descrição do Problema</h3>
                <p class="mt-2 text-gray-600 dark:text-gray-400 whitespace-pre-wrap"><?= htmlspecialchars($solicitacao['descricao']) ?></p>
            </div>

            <?php if ($solicitacao['path_imagem']): ?>
            <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Imagem Anexada</h3>
                <div class="mt-2">
                    <img src="/hackathon/<?= htmlspecialchars($solicitacao['path_imagem']) ?>" alt="Imagem da solicitação" class="rounded-lg max-w-lg shadow-md">
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Histórico de Movimentações -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Histórico de Andamento</h2>
            <div class="space-y-4">
                <?php if (empty($movimentacoes)): ?>
                    <p class="text-gray-500 dark:text-gray-400">Nenhuma atualização de status registrada ainda.</p>
                <?php else: ?>
                    <?php foreach ($movimentacoes as $mov): ?>
                        <div class="p-4 border-l-4 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 rounded-r-lg">
                            <p class="text-sm text-gray-500 dark:text-gray-400"><?= date('d/m/Y H:i', strtotime($mov['data_movimentacao'])) ?> por <strong><?= htmlspecialchars($mov['nome_usuario'] ?? 'Equipe Responsável') ?></strong></p>
                            <p class="font-semibold">Status alterado para: <?= htmlspecialchars($mov['novo_status']) ?></p>
                            <?php if (!empty($mov['resposta'])): ?>
                                <p class="mt-2 text-gray-700 dark:text-gray-300"><strong>Observação:</strong> <?= htmlspecialchars($mov['resposta']) ?></p>
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

    <div class="mt-8 text-center">
        <a href="minhas_solicitacoes.php" class="text-blue-600 dark:text-blue-400 hover:underline">Consultar outra solicitação</a>
    </div>
</div>

<?php
require_once '../../../templates/footer.php';
?>
