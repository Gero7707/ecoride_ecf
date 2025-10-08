<?php 
$pageSpecificCss = 'create.css';
require_once 'app/views/includes/head-header.php'
?>

<!-- Page de création de covoiturage -->
<main>
    <!-- Messages flash -->
    <?php if (isset($_SESSION['success'])): ?>
        <section class="flash-messages">
            <div class="container">
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <section class="flash-messages">
            <div class="container">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= $_SESSION['error'] ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- En-tête -->
    <section class="create-header">
        <div class="container">
            <div class="header-content">
                <a href="/profil" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </a>
                <div class="page-title text-center">
                    <h1><i class="fas fa-plus-circle"></i> Publier un trajet</h1>
                    <p>Partagez votre trajet et faites des économies</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Formulaire -->
    <section class="create-form-section">
        <div class="container">
            <form action="/covoiturage/creer" method="POST" id="createTripForm" class="trip-form">
                
                <!-- Informations du trajet -->
                <div class="form-section">
                    <h3 class="section-title mb-3">
                        <i class="fas fa-route"></i>
                        Itinéraire
                    </h3>
                    
                    <div class="form-row mb-3">
                        <div class="form-group mb-3">
                            <i class="fas fa-map-marker-alt input-icon"></i>
                            <label for="ville_depart">Ville de départ <span class="required">*</span></label>
                            <div class="input-group mb-3">
                                <input type="text" 
                                        id="ville_depart" 
                                        name="ville_depart" 
                                        class="form-control" 
                                        required 
                                        placeholder="Ex: Paris"
                                        list="villes-list">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <i class="fas fa-map-marker-alt input-icon"></i>
                            <label for="ville_arrivee">Ville d'arrivée <span class="required">*</span></label>
                            <div class="input-group mb-3">
                                <input type="text" 
                                        id="ville_arrivee" 
                                        name="ville_arrivee" 
                                        class="form-control" 
                                        required 
                                        placeholder="Ex: Lyon"
                                        list="villes-list">
                            </div>
                        </div>
                    </div>

                    <!-- Liste de villes courantes -->
                    <datalist id="villes-list">
                        <option value="Paris">
                        <option value="Lyon">
                        <option value="Marseille">
                        <option value="Toulouse">
                        <option value="Nice">
                        <option value="Nantes">
                        <option value="Bordeaux">
                        <option value="Lille">
                        <option value="Strasbourg">
                        <option value="Rennes">
                        <option value="Montpellier">
                    </datalist>
                </div>

                <!-- Date et heure -->
                <div class="form-section">
                    <h3 class="section-title mb-3">
                        <i class="fas fa-calendar-alt"></i>
                        Date et horaires
                    </h3>
                    
                    <div class="form-row mb-3">
                        <div class="form-group mb-3">
                            <i class="fas fa-calendar input-icon"></i>
                            <label for="date_depart">Date de départ <span class="required">*</span></label>
                            <div class="input-group mb-3">
                                <input type="date" 
                                        id="date_depart" 
                                        name="date_depart" 
                                        class="form-control" 
                                        required
                                        min="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <i class="fas fa-clock input-icon"></i>
                            <label for="heure_depart">Heure de départ <span class="required">*</span></label>
                            <div class="input-group mb-3">
                                <input type="time" 
                                        id="heure_depart" 
                                        name="heure_depart" 
                                        class="form-control" 
                                        required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <i class="fas fa-clock input-icon"></i>
                        <label for="heure_arrivee">Heure d'arrivée estimée</label>
                        <div class="input-group mb-3">
                            <input type="time" 
                                    id="heure_arrivee" 
                                    name="heure_arrivee" 
                                    class="form-control">
                        </div>
                        <small class="form-help">Optionnel - aide les passagers à planifier</small>
                    </div>
                </div>

                <!-- Véhicule -->
                <div class="form-section">
                    <h3 class="section-title mb-3">
                        <i class="fas fa-car"></i>
                        Véhicule
                    </h3>
                    
                    <?php if (!empty($vehicules)): ?>
                        <div class="form-group mb-3">
                            <label for="vehicule_id">Sélectionnez votre véhicule <span class="required">*</span></label>
                            <select id="vehicule_id" name="vehicule_id" class="form-control mb-3" required>
                                <option value="">-- Choisir un véhicule --</option>
                                <?php foreach ($vehicules as $vehicule): ?>
                                    <option value="<?= $vehicule['id'] ?>">
                                        <?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele'] . ' - ' . $vehicule['couleur']) ?>
                                        (<?= $vehicule['nombre_places'] ?> places)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Vous devez d'abord ajouter un véhicule pour proposer un covoiturage.
                            <a href="/vehicule/ajouter" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i>
                                Ajouter un véhicule
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="form-group mb-3">
                        <i class="fas fa-users input-icon"></i>
                        <label for="places_disponibles">Nombre de places disponibles <span class="required">*</span></label>
                        <div class="input-group mb-3">
                            <input type="number" 
                                    id="places_disponibles" 
                                    name="places_disponibles" 
                                    class="form-control" 
                                    min="1" 
                                    max="8" 
                                    value="3"
                                    required>
                        </div>
                        <small class="form-help">Maximum 8 places par trajet</small>
                    </div>
                </div>

                <!-- Prix et options -->
                <div class="form-section">
                    <h3 class="section-title mb-3">
                        <i class="fas fa-euro-sign"></i>
                        Prix et options
                    </h3>
                    
                    <div class="form-group mb-3">
                        <i class="fas fa-euro-sign input-icon"></i>
                        <label for="prix">Prix par personne (€) <span class="required">*</span></label>
                        <div class="input-group mb-3">
                            <input type="number" 
                                    id="prix" 
                                    name="prix" 
                                    class="form-control" 
                                    min="1" 
                                    max="999" 
                                    step="0.01"
                                    required
                                    placeholder="Ex: 25.00">
                        </div>
                        <small class="form-help">Partagez équitablement les frais d'essence et de péage</small>
                    </div>

                    <div class="price-calculator mb-3">
                        <button type="button" class="btn btn-outline btn-sm mb-3"  id="calculateButton">
                            <i class="fas fa-calculator"></i>
                            Calculer le prix recommandé
                        </button>
                        <div id="price-suggestion" style="display: none;" class="price-info mb-3">
                            Prix suggéré : <strong id="suggested-price"></strong>
                        </div>
                    </div>
                </div>

                <!-- Options de réservation -->
                <div class="form-section">
                    <h3 class="section-title mb-3">
                        <i class="fas fa-cog"></i>
                        Options de réservation
                    </h3>
                    
                    <div class="reservation-options mb-3">
                        <div class="option-card d-flex gap-3 mb-3">
                            <input type="radio" id="instant" name="reservation_type" value="instant" checked>
                            <label for="instant" class="d-flex gap-1">
                                <div class="option-icon">
                                    <i class="fas fa-bolt"></i>
                                </div>
                                <div class="option-content">
                                    <strong>Réservation instantanée</strong>
                                    <p>Les passagers peuvent réserver immédiatement</p>
                                </div>
                            </label>
                        </div>

                        <div class="option-card d-flex gap-3">
                            <input type="radio" id="confirmation" name="reservation_type" value="confirmation">
                            <label for="confirmation" class="d-flex gap-1">
                                <div class="option-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="option-content">
                                    <strong>Avec confirmation</strong>
                                    <p>Vous devrez valider chaque demande de réservation</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <?php if (!empty($vehicules)): ?>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-check"></i>
                            Publier
                        </button>
                    <?php endif; ?>
                    <a href="/profil" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i>
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </section>
</main>

<?php
$pageSpecificJs = 'covoiturage.js';
require_once 'app/views/includes/footer.php';
?>