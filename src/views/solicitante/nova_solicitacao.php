<?php
// Inclui o arquivo de configuração do banco de dados e o cabeçalho
require_once '../../../config/database.php';
require_once '../../../templates/header.php';

// Conecta ao banco de dados
$conexao = conectar_banco();

// Busca os cargos para o campo de seleção
$cargos_resultado = $conexao->query("SELECT nome_cargo FROM cargos ORDER BY nome_cargo");
$cargos = $cargos_resultado->fetch_all(MYSQLI_ASSOC);

// Busca as categorias para o campo de seleção
$categorias_resultado = $conexao->query("SELECT id_categoria, nome_categoria FROM categorias ORDER BY nome_categoria");
$categorias = $categorias_resultado->fetch_all(MYSQLI_ASSOC);

$conexao->close();
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Nova Solicitação</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
        <form action="../../controllers/solicitacao_controller.php?action=create" method="POST" enctype="multipart/form-data">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome do Solicitante -->
                <div>
                    <label for="nome_solicitante" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nome Completo</label>
                    <input type="text" id="nome_solicitante" name="nome_solicitante" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>

                <!-- Matrícula -->
                <div>
                    <label for="matricula_solicitante" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Matrícula</label>
                    <input type="text" id="matricula_solicitante" name="matricula_solicitante" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>

                <!-- Cargo -->
                <div>
                    <label for="cargo_solicitante" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cargo</label>
                    <select id="cargo_solicitante" name="cargo_solicitante" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                        <option value="" disabled selected>Selecione seu cargo</option>
                        <?php foreach ($cargos as $cargo): ?>
                            <option value="<?= htmlspecialchars($cargo['nome_cargo']) ?>"><?= htmlspecialchars($cargo['nome_cargo']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Local do Problema -->
                <div>
                    <label for="local_problema" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Local do Problema (Sala, Setor, etc.)</label>
                    <input type="text" id="local_problema" name="local_problema" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>

                <!-- Categoria -->
                <div>
                    <label for="categoria_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Categoria</label>
                    <select id="categoria_id" name="categoria_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                        <option value="" disabled selected>Selecione a categoria</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= $categoria['id_categoria'] ?>"><?= htmlspecialchars($categoria['nome_categoria']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Prioridade -->
                <div>
                    <label for="prioridade" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Prioridade</label>
                    <select id="prioridade" name="prioridade" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                        <option value="" disabled selected>Selecione a prioridade</option>
                        <option value="Baixa">Baixa</option>
                        <option value="Média">Média</option>
                        <option value="Urgente">Urgente</option>
                    </select>
                </div>
            </div>

            <!-- Descrição Detalhada -->
            <div class="mt-6">
                <label for="descricao" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Descrição Detalhada da Solicitação</label>
                <textarea id="descricao" name="descricao" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required></textarea>
            </div>

            <!-- Upload de Imagem -->
            <div class="mt-6">
                <label for="path_imagem" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Anexar Imagem (Opcional)</label>
                <input type="file" id="path_imagem" name="path_imagem" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">Formatos permitidos: JPG, PNG, GIF.</p>
            </div>

            <!-- Botão de Envio -->
            <div class="mt-8 text-right">
                <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none dark:focus:ring-blue-800">Enviar Solicitação</button>
            </div>
        </form>
    </div>
</div>

<?php
require_once '../../../templates/footer.php';
?>
