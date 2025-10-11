$(document).ready(function(){
    $('#datepicker').datepicker({
        format: "dd/mm/yyyy",
        autoclose: true,
        todayHighlight: true,
        orientation: "bottom auto",
        language: "fr"
    });
});

document.addEventListener('DOMContentLoaded', function() {
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

    // Calculateur de prix basé sur la distance estimée
    function calculatePrice() {
        const villeDepart = document.getElementById('ville_depart');
        const villeArrivee = document.getElementById('ville_arrivee');
        const places = document.getElementById('places_disponibles');
        
        // Vérifier que les éléments existent
        if (!villeDepart || !villeArrivee || !places) return;
        
        if (!villeDepart.value || !villeArrivee.value) {
            alert('Veuillez remplir les villes de départ et d\'arrivée');
            return;
        }
    
        const distances = {
            'Paris-Lyon': 470,
            'Paris-Marseille': 775,
            'Lyon-Marseille': 315,
            'Paris-Toulouse': 680,
            'Paris-Bordeaux': 580
        };
    
        const route = villeDepart.value + '-' + villeArrivee.value;
        const reverseRoute = villeArrivee.value + '-' + villeDepart.value;
        let distance = distances[route] || distances[reverseRoute] || 400;
    
        const totalCost = distance * 0.08;
        const pricePerPerson = (totalCost / (parseInt(places.value) + 1)).toFixed(2);
    
        const suggestedPrice = document.getElementById('suggested-price');
        const priceSuggestion = document.getElementById('price-suggestion');
        const prixInput = document.getElementById('prix');
        
        if (suggestedPrice) suggestedPrice.textContent = pricePerPerson + '€';
        if (priceSuggestion) priceSuggestion.style.display = 'block';
        if (prixInput) prixInput.value = pricePerPerson;
    }

    const calculateButton = document.getElementById('calculateButton');
    if (calculateButton) {
        calculateButton.addEventListener('click', calculatePrice);
    }
});