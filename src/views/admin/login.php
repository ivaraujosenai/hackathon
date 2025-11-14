<?php
require_once '../../../templates/header.php';
?>

<div class="flex items-center justify-center min-h-[60vh]">
    <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
        <div class="text-center mb-8">
            <i class="fas fa-user-shield text-5xl text-red-500 dark:text-red-400"></i>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white mt-4">Acesso Restrito</h1>
            <p class="text-gray-600 dark:text-gray-300">Painel do Administrador</p>
        </div>

        <!-- Exibição de mensagens de erro/sucesso -->
        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Erro!</strong>
                <span class="block sm:inline"><?= htmlspecialchars($_GET['error']) ?></span>
            </div>
        <?php endif; ?>

        <form action="../../controllers/auth_controller.php?action=login" method="POST">
            <!-- Email -->
            <div class="mb-6">
                <label for="email_usuario" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                <input type="email" id="email_usuario" name="email_usuario" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="seuemail@exemplo.com" required>
            </div>

            <!-- Senha -->
            <div class="mb-6">
                <label for="senha" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Senha</label>
                <input type="password" id="senha" name="senha" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
            </div>

            <!-- Botão de Login -->
            <div class="mt-8">
                <button type="submit" class="w-full text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-500 dark:hover:bg-red-600 focus:outline-none dark:focus:ring-red-800">Entrar</button>
            </div>
        </form>
    </div>
</div>

<?php
require_once '../../../templates/footer.php';
?>
