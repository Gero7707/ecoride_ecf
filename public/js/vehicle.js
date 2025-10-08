// vehicle.js - Gestion des véhicules

document.addEventListener('DOMContentLoaded', function() {
    
    // ========== AUTO-FORMATAGE PLAQUE D'IMMATRICULATION ==========
    const plaqueInput = document.getElementById('plaque_immatriculation');
    if (plaqueInput) {
        plaqueInput.addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            
            // Format nouveau: AA-123-BB
            if (value.length >= 2 && /^[A-Z]{2}/.test(value)) {
                if (value.length > 2) value = value.slice(0, 2) + '-' + value.slice(2);
                if (value.length > 6) value = value.slice(0, 6) + '-' + value.slice(6);
                if (value.length > 9) value = value.slice(0, 9);
            }
            // Format ancien: 1234-AB-12
            else if (/^[0-9]/.test(value)) {
                if (value.length > 4) value = value.slice(0, 4) + '-' + value.slice(4);
                if (value.length > 7) value = value.slice(0, 7) + '-' + value.slice(7);
                if (value.length > 10) value = value.slice(0, 10);
            }
            
            e.target.value = value;
        });
    }

    // ========== VALIDATION FORMULAIRE AJOUT VÉHICULE ==========
    const addVehicleForm = document.getElementById('addVehicleForm');
    if (addVehicleForm) {
        addVehicleForm.addEventListener('submit', function(e) {
            const plaque = document.getElementById('plaque_immatriculation').value;
            const dateImmat = document.getElementById('date_premiere_immatriculation').value;
            
            // Vérifier format plaque
            const formatNouveau = /^[A-Z]{2}-[0-9]{3}-[A-Z]{2}$/;
            const formatAncien = /^[0-9]{1,4}-[A-Z]{2,3}-[0-9]{2}$/;
            
            if (!formatNouveau.test(plaque) && !formatAncien.test(plaque)) {
                e.preventDefault();
                alert('Format de plaque invalide. Utilisez AA-123-BB ou 1234-AB-12');
                return false;
            }
            
            // Vérifier que la date n'est pas dans le futur
            if (new Date(dateImmat) > new Date()) {
                e.preventDefault();
                alert('La date d\'immatriculation ne peut pas être dans le futur.');
                return false;
            }
        });
    }

    // ========== SUPPRESSION DE VÉHICULE ==========
    let vehicleIdToDelete = null;

    function confirmDelete(vehicleId, vehicleName) {
        vehicleIdToDelete = vehicleId;
        const modal = document.getElementById('deleteModal');
        const nameElement = document.getElementById('vehicleName');

        if (modal && nameElement) {
            nameElement.textContent = vehicleName;
            modal.style.display = 'flex';
        }
    }

    function closeModal() {
        vehicleIdToDelete = null;
        const modal = document.getElementById('deleteModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    // Attacher les écouteurs sur TOUS les boutons de suppression
    const deleteButtons = document.querySelectorAll('.delete-vehicle-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const vehicleId = this.dataset.vehicleId;
            const vehicleName = this.dataset.vehicleName;
            confirmDelete(vehicleId, vehicleName);
        });
    });

    // Bouton Annuler dans le modal
    const closeModalBtn = document.getElementById('closeModal');
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }
    
    // Fermer en cliquant sur l'overlay
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('click', function(e) {
            if (e.target.id === 'deleteModal') {
                closeModal();
            }
        });
    }
    
    // Fermer avec Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    // Soumettre le formulaire de suppression
    const deleteForm = document.getElementById('deleteForm');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!vehicleIdToDelete) return;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/vehicule/supprimer/' + vehicleIdToDelete;
            document.body.appendChild(form);
            form.submit();
        });
    }

});