<?php
require_once '../../../templates/header.php';
?>

<div class="flex items-center justify-center min-h-[60vh]">
    <div class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
        <div class="text-center mb-8">
            <i class="fas fa-search text-5xl text-blue-500 dark:text-blue-400"></i>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white mt-4">Acompanhar Minhas Solicitações</h1>
            <p class="text-gray-600 dark:text-gray-300">Digite sua matrícula para ver o histórico de suas solicitações.</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= htmlspecialchars($_GET['error']) ?></span>
            </div>
        <?php endif; ?>

        <form action="acompanhar_lista.php" method="GET">
            <div class="mb-6">
                <label for="matricula" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sua Matrícula</label>
                <input type="text" id="matricula" name="matricula" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Digite sua matrícula" required>
            </div>

            <div class="mt-8">
                <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none dark:focus:ring-blue-800">Buscar Solicitações</button>
            </div>
        </form>
        
        <div class="mt-6 text-center">
            <a href="nova_solicitacao.php" class="text-green-600 dark:text-green-400 hover:underline">Ou abra uma nova solicitação</a>
        </div>
    </div>
</div>

<?php
require_once '../../../templates/footer.php';
?>
