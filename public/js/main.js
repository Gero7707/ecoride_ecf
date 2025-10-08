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


// Calculateur de prix basé sur la distance estimée
function calculatePrice() {
    const villeDepart = document.getElementById('ville_depart').value;
    const villeArrivee = document.getElementById('ville_arrivee').value;
    const places = document.getElementById('places_disponibles').value;
    
    if (!villeDepart || !villeArrivee) {
        alert('Veuillez remplir les villes de départ et d\'arrivée');
        return;
    }
    
    // Estimation simple basée sur des distances moyennes
    const distances = {
        'Paris-Lyon': 470,
        'Paris-Marseille': 775,
        'Lyon-Marseille': 315,
        'Paris-Toulouse': 680,
        'Paris-Bordeaux': 580
    };
    
    const route = villeDepart + '-' + villeArrivee;
    const reverseRoute = villeArrivee + '-' + villeDepart;
    
    let distance = distances[route] || distances[reverseRoute] || 400; // Par défaut 400km
    
    // Calcul : environ 0.08€/km, divisé par le nombre de places
    const totalCost = distance * 0.08;
    const pricePerPerson = (totalCost / (parseInt(places) + 1)).toFixed(2); // +1 pour le chauffeur
    
    document.getElementById('suggested-price').textContent = pricePerPerson + '€';
    document.getElementById('price-suggestion').style.display = 'block';
    document.getElementById('prix').value = pricePerPerson;
}

// Gestion du formulaire d'ajout de véhicule
document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-formatage de la plaque d'immatriculation
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

    // Validation du formulaire d'ajout de véhicule
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

    // Gestion de la suppression de véhicule
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

    // ✅ Attacher les écouteurs sur TOUS les boutons de suppression
    const deleteButtons = document.querySelectorAll('.delete-vehicle-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const vehicleId = this.dataset.vehicleId;
            const vehicleName = this.dataset.vehicleName;
            confirmDelete(vehicleId, vehicleName);
        });
    });


    // Écouteur sur le bouton Annuler
    const closeModalBtn = document.getElementById('closeModal');
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }
    
    // Vous pouvez aussi fermer en cliquant sur l'overlay
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
});





// Attendre que le DOM soit chargé pour attacher les événements
document.addEventListener('DOMContentLoaded', function() {
    // Gérer la suppression
    const deleteForm = document.getElementById('deleteForm');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!vehicleIdToDelete) return;
            
            // Créer un formulaire caché pour envoyer la requête POST
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/vehicule/supprimer/' + vehicleIdToDelete;
            document.body.appendChild(form);
            form.submit();
        });
    }
    
    // Fermer en cliquant à l'extérieur
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
});