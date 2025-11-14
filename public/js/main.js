document.addEventListener('DOMContentLoaded', () => {
    const themeToggleButton = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-toggle-icon');

    // Função para definir o tema
    const setTheme = (isDark) => {
        if (isDark) {
            document.documentElement.classList.add('dark');
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
            localStorage.setItem('color-theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
            localStorage.setItem('color-theme', 'light');
        }
    };

    // Verifica o tema inicial
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('color-theme');
    const isInitiallyDark = savedTheme === 'dark' || (savedTheme === null && prefersDark);
    
    setTheme(isInitiallyDark);

    // Listener para o botão de troca de tema
    themeToggleButton.addEventListener('click', () => {
        const isCurrentlyDark = document.documentElement.classList.contains('dark');
        setTheme(!isCurrentlyDark);
    });
});
