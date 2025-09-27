$(document).ready(function(){
    $('#datepicker').datepicker({
        format: "dd/mm/yyyy",
        autoclose: true,
        todayHighlight: true,
        orientation: "bottom auto",
        language: "fr"
    });
});


// Validation côté client pour le mot de passe
const confirmPasswordField = document.getElementById('confirmer_mot_de_passe');
if (confirmPasswordField) {
    confirmPasswordField.addEventListener('input', function() {
        const password = document.getElementById('mot_de_passe').value;
        const confirm = this.value;
        
        if (password !== confirm) {
            this.setCustomValidity('Les mots de passe ne correspondent pas');
        } else {
            this.setCustomValidity('');
        }
    });
}



document.addEventListener('DOMContentLoaded', function() {
    const showPasswordsCheckbox = document.getElementById('show_passwords');
    
    if (showPasswordsCheckbox) {
        showPasswordsCheckbox.addEventListener('change', function() {
            // Pour la page de création de compte
            const motDePasseField = document.getElementById('mot_de_passe');
            const confirmerField = document.getElementById('confirmer_mot_de_passe');
            
            // Pour la page d'édition profil
            const currentPasswordField = document.getElementById('current_password');
            const newPasswordField = document.getElementById('new_password');
            const confirmPasswordField = document.getElementById('confirm_password');
            
            if (this.checked) {
                // Changer en texte si les champs existent
                if (motDePasseField) motDePasseField.type = 'text';
                if (confirmerField) confirmerField.type = 'text';
                if (currentPasswordField) currentPasswordField.type = 'text';
                if (newPasswordField) newPasswordField.type = 'text';
                if (confirmPasswordField) confirmPasswordField.type = 'text';
            } else {
                // Remettre en password si les champs existent
                if (motDePasseField) motDePasseField.type = 'password';
                if (confirmerField) confirmerField.type = 'password';
                if (currentPasswordField) currentPasswordField.type = 'password';
                if (newPasswordField) newPasswordField.type = 'password';
                if (confirmPasswordField) confirmPasswordField.type = 'password';
            }
        });
    }
});


// Validation côté client pour le mot de passe - seulement si les éléments existent
const confirmField = document.getElementById('confirmer_mot_de_passe');
const passwordField = document.getElementById('mot_de_passe');
const showPasswordsCheck = document.getElementById('show_passwords');

if (confirmField && passwordField) {
    confirmField.addEventListener('input', function() {
        const password = passwordField.value;
        const confirm = this.value;
        
        if (password !== confirm) {
            this.setCustomValidity('Les mots de passe ne correspondent pas');
        } else {
            this.setCustomValidity('');
        }
    });
}

if (showPasswordsCheck && passwordField && confirmField) {
    showPasswordsCheck.addEventListener('change', function() {
        if (this.checked) {
            passwordField.type = 'text';
            confirmField.type = 'text';
        } else {
            passwordField.type = 'password';
            confirmField.type = 'password';
        }
    });
}

// Auto-submit des filtres quand ils changent
document.querySelectorAll('.filters input, .filters select').forEach(element => {
    element.addEventListener('change', function() {
        // Récupérer tous les paramètres actuels
        const form = document.querySelector('.search-form');
        const formData = new FormData(form);
        
        // Ajouter les filtres
        const filters = document.querySelectorAll('.filters input:checked, .filters select, .filters input[type="number"]');
        filters.forEach(filter => {
            if (filter.value) {
                formData.set(filter.name, filter.value);
            }
        });
        
        // Construire l'URL avec tous les paramètres
        const params = new URLSearchParams(formData);
        window.location.href = '/covoiturages?' + params.toString();
    });
});

// Gestion du menu burger Bootstrap - fermeture au clic extérieur
document.addEventListener('click', function(event) {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('#navbarNavDropdown');
    
    // Vérifier si les éléments existent
    if (!navbarToggler || !navbarCollapse) return;
    
    // Vérifier si le menu est ouvert (a la classe 'show')
    if (navbarCollapse.classList.contains('show')) {
        // Si le clic n'est ni sur le bouton burger ni dans le menu
        if (!navbarToggler.contains(event.target) && !navbarCollapse.contains(event.target)) {
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