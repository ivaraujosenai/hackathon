<?php
require_once '../../../config/database.php';
require_once '../../../templates/header.php';

// Valida se a matrícula foi passada
if (!isset($_GET['matricula']) || empty($_GET['matricula'])) {
    header('Location: minhas_solicitacoes.php?error=Matrícula não fornecida.');
    exit;
}

$matricula = $_GET['matricula'];
$conexao = conectar_banco();

// Busca as solicitações pela matrícula do solicitante
$query = "
    SELECT 
        s.id_solicitacao, s.descricao, s.data_abertura, s.status,
        c.nome_categoria
    FROM solicitacoes s
    JOIN categorias c ON s.categoria_id = c.id_categoria
    WHERE s.matricula_solicitante = ?
    ORDER BY s.data_abertura DESC
";

$stmt = $conexao->prepare($query);
$stmt->bind_param("s", $matricula);
$stmt->execute();
$resultado = $stmt->get_result();
$solicitacoes = $resultado->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conexao->close();
?>

<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Minhas Solicitações</h1>
            <p class="text-gray-600 dark:text-gray-300">Exibindo solicitações para a matrícula: <strong><?= htmlspecialchars($matricula) ?></strong></p>
        </div>
        <a href="minhas_solicitacoes.php" class="text-blue-600 dark:text-blue-400 hover:underline">&larr; Buscar outra matrícula</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">#ID</th>
                        <th scope="col" class="px-6 py-3">Abertura</th>
                        <th scope="col" class="px-6 py-3">Categoria</th>
                        <th scope="col" class="px-6 py-3">Descrição Resumida</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($solicitacoes)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center">Nenhuma solicitação encontrada para esta matrícula. <a href="nova_solicitacao.php" class="text-blue-500 hover:underline">Criar uma nova?</a></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($solicitacoes as $solicitacao): ?>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-gray-100">
                                    <?= htmlspecialchars($solicitacao['id_solicitacao']) ?>
                                </th>
                                <td class="px-6 py-4">
                                    <?= date('d/m/Y H:i', strtotime($solicitacao['data_abertura'])) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= htmlspecialchars($solicitacao['nome_categoria']) ?>
                                </td>
                                <td class="px-6 py-4 max-w-xs truncate">
                                    <?= htmlspecialchars(substr($solicitacao['descricao'], 0, 50)) ?>...
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
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
                                </td>
                                <td class="px-6 py-4">
                                    <a href="acompanhar.php?id=<?= $solicitacao['id_solicitacao'] ?>" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Ver Detalhes</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once '../../../templates/footer.php';
?>
