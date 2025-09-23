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
// document.getElementById('confirmer_mot_de_passe').addEventListener('input', function() {
//     const password = document.getElementById('mot_de_passe').value;
//     const confirm = this.value;
    
//     if (password !== confirm) {
//         this.setCustomValidity('Les mots de passe ne correspondent pas');
//     } else {
//         this.setCustomValidity('');
//     }
// });

// Afficher/masquer les mots de passe
// document.getElementById('show_passwords').addEventListener('change', function() {
//     const passwordField = document.getElementById('mot_de_passe');
//     const confirmField = document.getElementById('confirmer_mot_de_passe');
    
//     if (this.checked) {
//         passwordField.type = 'text';
//         confirmField.type = 'text';
//     } else {
//         passwordField.type = 'password';
//         confirmField.type = 'password';
//     }
// });

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
