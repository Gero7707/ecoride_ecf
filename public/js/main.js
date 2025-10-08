
// Gestion du menu burger Bootstrap - fermeture au clic extérieur
document.addEventListener('click', function(event) {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('#navbarNavDropdown');
    
    // Vérifier si les éléments existent
    if (!navbarToggler || !navbarCollapse) return;
    
    // Vérifier si le menu est ouvert (a la classe 'show')
    if (navbarCollapse.classList.contains('show')) {
        // Récupérer les liens et éléments cliquables du menu
        const menuLinks = navbarCollapse.querySelectorAll('a, button, .nav-link');
        const clickedInsideMenu = Array.from(menuLinks).some(link => link.contains(event.target));
        // Si le clic n'est ni sur le bouton burger ni dans le menu
        if (!navbarToggler.contains(event.target) && !clickedInsideMenu) {
            // Utiliser l'API Bootstrap pour fermer le menu
            const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                toggle: false
            });
            bsCollapse.hide();
        }
    }
});

// Fermer le menu avec la touche Échap
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const navbarCollapse = document.querySelector('#navbarNavDropdown');
        if (navbarCollapse && navbarCollapse.classList.contains('show')) {
            const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                toggle: false
            });
            bsCollapse.hide();
        }
    }
});
