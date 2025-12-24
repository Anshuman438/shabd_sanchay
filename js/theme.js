document.addEventListener('DOMContentLoaded', function() {
    // Initialize theme
    const currentTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', currentTheme);

    // Theme toggle functionality
    document.querySelector('.theme-toggle')?.addEventListener('click', function() {
        const theme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
    });

    // Mobile menu toggle
    const menuToggle = document.querySelector(".menu-toggle");
    const mainNav = document.querySelector(".main-nav");
    menuToggle?.addEventListener("click", () => {
        mainNav?.classList.toggle("active");
    });

    // Basic loader functionality
    window.addEventListener('load', function() {
        document.body.classList.add('loaded');
    });

    // Fallback in case load event doesn't fire
    setTimeout(() => {
        document.body.classList.add('loaded');
    }, 3000);
});

// Basic page transition handling
document.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', function(e) {
        if (e.ctrlKey || e.metaKey || e.shiftKey || link.target === '_blank') return;
        if (link.href.startsWith('javascript:') || link.href.includes('#')) return;
        document.body.classList.remove('loaded');
    });
});