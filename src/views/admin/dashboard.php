<?php
session_start();
require_once '../../../config/database.php';
require_once '../../../templates/header.php';

// Protege a página contra acesso não autenticado
if (!isset($_SESSION['usuario_admin_id'])) {
    header('Location: login.php?error=Acesso negado. Faça login primeiro.');
    exit;
}

$conexao = conectar_banco();
$id_setor_admin = $_SESSION['usuario_admin_setor_id'];

// Busca as solicitações pertencentes ao setor do admin logado
$query = "
    SELECT 
        s.id_solicitacao, s.local_problema, s.prioridade, s.data_abertura, s.status,
        c.nome_categoria
    FROM solicitacoes s
    JOIN categorias c ON s.categoria_id = c.id_categoria
    WHERE s.setor_responsavel_id = ?
    ORDER BY s.data_abertura DESC
";

$stmt = $conexao->prepare($query);
$stmt->bind_param("i", $id_setor_admin);
$stmt->execute();
$resultado = $stmt->get_result();
$solicitacoes = $resultado->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conexao->close();
?>

<div class="max-w-7xl mx-auto">
    <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Painel de Controle</h1>
            <p class="text-gray-600 dark:text-gray-300">Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_admin_nome']) ?>!</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="../../controllers/relatorio_controller.php?action=export_csv" class="text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-sm px-5 py-2.5">
                <i class="fas fa-file-csv mr-2"></i>Exportar para CSV
            </a>
            <a href="../../controllers/auth_controller.php?action=logout" class="text-white bg-red-600 hover:bg-red-700 font-medium rounded-lg text-sm px-5 py-2.5">Sair</a>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Solicitações por Status</h3>
            <canvas id="grafico_status"></canvas>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Solicitações por Prioridade</h3>
            <canvas id="grafico_prioridade"></canvas>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 lg:col-span-2 xl:col-span-1">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Top Categorias</h3>
            <canvas id="grafico_categoria"></canvas>
        </div>
    </div>

    <!-- Tabela de Últimas Solicitações -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Últimas Solicitações</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">#ID</th>
                        <th scope="col" class="px-6 py-3">Abertura</th>
                        <th scope="col" class="px-6 py-3">Categoria</th>
                        <th scope="col" class="px-6 py-3">Local</th>
                        <th scope="col" class="px-6 py-3">Prioridade</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($solicitacoes)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center">Nenhuma solicitação encontrada para seu setor.</td>
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
                                <td class="px-6 py-4">
                                    <?= htmlspecialchars($solicitacao['local_problema']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold
                                        <?php 
                                            switch ($solicitacao['prioridade']) {
                                                case 'Baixa': echo 'text-green-600 dark:text-green-400'; break;
                                                case 'Média': echo 'text-yellow-600 dark:text-yellow-400'; break;
                                                case 'Urgente': echo 'text-red-600 dark:text-red-400'; break;
                                            }
                                        ?>
                                    ">
                                        <?= htmlspecialchars($solicitacao['prioridade']) ?>
                                    </span>
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
                                    <a href="visualizar_solicitacao.php?id=<?= $solicitacao['id_solicitacao'] ?>" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    fetch('../../controllers/dados_graficos_controller.php')
        .then(response => response.json())
        .then(dados => {
            if (dados.error) {
                console.error('Erro ao buscar dados para os gráficos:', dados.error);
                return;
            }

            const options = {
                plugins: {
                    legend: {
                        labels: {
                            color: document.documentElement.classList.contains('dark') ? '#E5E7EB' : '#374151'
                        }
                    }
                }
            };

            // Gráfico de Status
            new Chart(document.getElementById('grafico_status'), {
                type: 'doughnut',
                data: {
                    labels: dados.por_status.labels,
                    datasets: [{
                        label: 'Solicitações por Status',
                        data: dados.por_status.data,
                        backgroundColor: ['#3B82F6', '#F59E0B', '#10B981', '#EF4444'],
                    }]
                },
                options: options
            });

            // Gráfico de Prioridade
            new Chart(document.getElementById('grafico_prioridade'), {
                type: 'pie',
                data: {
                    labels: dados.por_prioridade.labels,
                    datasets: [{
                        label: 'Solicitações por Prioridade',
                        data: dados.por_prioridade.data,
                        backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                    }]
                },
                options: options
            });

            // Gráfico de Categoria
            new Chart(document.getElementById('grafico_categoria'), {
                type: 'bar',
                data: {
                    labels: dados.por_categoria.labels,
                    datasets: [{
                        label: 'Solicitações por Categoria',
                        data: dados.por_categoria.data,
                        backgroundColor: '#6366F1',
                    }]
                },
                options: {
                    ...options,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: document.documentElement.classList.contains('dark') ? '#E5E7EB' : '#374151'
                            }
                        },
                        x: {
                            ticks: {
                                color: document.documentElement.classList.contains('dark') ? '#E5E7EB' : '#374151'
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Erro na requisição Fetch:', error));
});
</script>

<?php
require_once '../../../templates/footer.php';
?>
