// auth.js - Gestion de l'authentification et des formulaires connexion/inscription

document.addEventListener('DOMContentLoaded', function() {
    
    // ========== VALIDATION DES MOTS DE PASSE ==========
    
    // Pour la page d'inscription
    const confirmPasswordField = document.getElementById('confirmer_mot_de_passe');
    const passwordField = document.getElementById('mot_de_passe');
    
    if (confirmPasswordField && passwordField) {
        confirmPasswordField.addEventListener('input', function() {
            const password = passwordField.value;
            const confirm = this.value;
            
            if (password !== confirm) {
                this.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                this.setCustomValidity('');
            }
        });
    }
    
    // ========== AFFICHER/MASQUER LES MOTS DE PASSE ==========
    
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
            
            const type = this.checked ? 'text' : 'password';
            
            // Changer le type si les champs existent
            if (motDePasseField) motDePasseField.type = type;
            if (confirmerField) confirmerField.type = type;
            if (currentPasswordField) currentPasswordField.type = type;
            if (newPasswordField) newPasswordField.type = type;
            if (confirmPasswordField) confirmPasswordField.type = type;
        });
    }

    // ========== SUPPRESSION DE PHOTO DE PROFIL ==========
    
    function openDeletePhotoModal() {
        document.getElementById('deletePhotoModal').style.display = 'flex';
    }
    
    function closeDeletePhotoModal() {
        document.getElementById('deletePhotoModal').style.display = 'none';
    }
    
    // Bouton de suppression de photo
    const deletePhotoBtn = document.querySelector('.delete-photo-btn');
    if (deletePhotoBtn) {
        deletePhotoBtn.addEventListener('click', openDeletePhotoModal);
    }
    
    // Bouton fermer modal
    const closePhotoBtn = document.getElementById('closeDeletePhotoModal');
    if (closePhotoBtn) {
        closePhotoBtn.addEventListener('click', closeDeletePhotoModal);
    }
    
    // Fermer en cliquant sur l'overlay
    const deletePhotoModal = document.getElementById('deletePhotoModal');
    if (deletePhotoModal) {
        deletePhotoModal.addEventListener('click', function(e) {
            if (e.target.id === 'deletePhotoModal') {
                closeDeletePhotoModal();
            }
        });
    }
    
    // Fermer avec Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeletePhotoModal();
        }
    });
});