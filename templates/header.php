<!DOCTYPE html>
<html lang="pt-BR" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Solicitações</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/hackathon/public/css/estilos.css">
    
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300 flex flex-col min-h-screen">
    <nav class="bg-white dark:bg-gray-800 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex-shrink-0">
                    <a href="/hackathon/index.php" class="text-2xl font-bold text-blue-600 dark:text-blue-400">SysSolicita</a>
                </div>
                <div class="flex items-center">
                    <a href="/hackathon/src/views/solicitante/minhas_solicitacoes.php">Minhas Solicitações</a>
                    <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                        <i id="theme-toggle-icon" class="fas fa-sun"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>
    <main class="container mx-auto px-4 py-8 flex-grow">
