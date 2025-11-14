<?php
require_once 'templates/header.php';
?>

<div class="flex flex-col items-center justify-center min-h-[60vh]">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-100 mb-4">Bem-vindo ao SysSolicita</h1>
        <p class="text-lg text-gray-600 dark:text-gray-300">Seu sistema de gerenciamento de solicitações de manutenção e suporte.</p>
    </div>

    <div class="w-full max-w-4xl flex flex-col md:flex-row justify-center gap-8">
        
        <a href="src/views/solicitante/nova_solicitacao.php" class="group w-full md:w-1/2 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 text-center hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300">
            <div class="mb-4">
                <i class="fas fa-user-edit text-5xl text-blue-500 dark:text-blue-400"></i>
            </div>
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Sou Solicitante</h2>
            <p class="text-gray-600 dark:text-gray-400">Abrir uma nova solicitação ou acompanhar o andamento das minhas solicitações.</p>
        </a>
        
        <a href="src/views/admin/login.php" class="group w-full md:w-1/2 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 text-center hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300">
            <div class="mb-4">
                <i class="fas fa-user-shield text-5xl text-red-500 dark:text-red-400"></i>
            </div>
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Sou Administrador</h2>
            <p class="text-gray-600 dark:text-gray-400">Acessar o painel de controle para gerenciar as solicitações dos setores.</p>
        </a>

    </div>
</div>

<?php
require_once 'templates/footer.php';
?>
