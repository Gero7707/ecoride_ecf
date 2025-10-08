<?php 
$pageSpecificCss = 'add-vehicule.css';
require_once 'app/views/includes/head-header.php';
?>

<!-- Page d'ajout de véhicule -->
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
    <section class="vehicle-header">
        <div class="container">
            <div class="header-content">
                <a href="/profil" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </a>
                <div class="page-title">
                    <h1><i class="fas fa-car"></i> Ajouter un véhicule</h1>
                    <p>Enregistrez votre véhicule pour proposer des covoiturages</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Formulaire -->
    <section class="vehicle-form-section">
        <div class="container">
            <form action="/vehicule/ajouter" method="POST" id="addVehicleForm" class="vehicle-form">
                
                <!-- Informations du véhicule -->
                <div class="form-section">
                    
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Informations du véhicule
                    </h3>
                    
                    <div class="form-row mt-4">
                        <div class="form-group">
                            <i class="fas fa-car input-icon"></i>
                            <label for="marque">Marque <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="text" 
                                        id="marque" 
                                        name="marque" 
                                        class="form-control" 
                                        required 
                                        placeholder="Ex: Renault"
                                        list="marques-list">
                            </div>
                        </div>

                        <div class="form-group mt-2">
                            <i class="fas fa-car-side input-icon"></i>
                            <label for="modele">Modèle <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="text" 
                                        id="modele" 
                                        name="modele" 
                                        class="form-control" 
                                        required 
                                        placeholder="Ex: Clio">
                            </div>
                        </div>
                    </div>

                    <!-- Liste des marques courantes -->
                    <datalist id="marques-list">
                        <option value="Renault">
                        <option value="Peugeot">
                        <option value="Citroën">
                        <option value="Volkswagen">
                        <option value="Toyota">
                        <option value="BMW">
                        <option value="Mercedes">
                        <option value="Audi">
                        <option value="Ford">
                        <option value="Fiat">
                        <option value="Opel">
                        <option value="Nissan">
                        <option value="Honda">
                        <option value="Hyundai">
                        <option value="Kia">
                    </datalist>

                    <div class="form-row mt-2">
                        <div class="form-group">
                            <i class="fas fa-palette input-icon"></i>
                            <label for="couleur">Couleur</label>
                            <div class="input-group">
                                <input type="text" 
                                        id="couleur" 
                                        name="couleur" 
                                        class="form-control" 
                                        placeholder="Ex: Blanche"
                                        list="couleurs-list">
                            </div>
                        </div>

                        <div class="form-group mt-2">
                            <i class="fas fa-users input-icon"></i>
                            <label for="nombre_places">Nombre de places <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="number" 
                                        id="nombre_places" 
                                        name="nombre_places" 
                                        class="form-control" 
                                        min="1" 
                                        max="8" 
                                        value="5"
                                        required>
                            </div>
                            <small class="form-help">Nombre total de places du véhicule</small>
                        </div>
                    </div>

                    <datalist id="couleurs-list">
                        <option value="Blanche">
                        <option value="Noire">
                        <option value="Grise">
                        <option value="Bleue">
                        <option value="Rouge">
                        <option value="Verte">
                        <option value="Jaune">
                        <option value="Orange">
                        <option value="Marron">
                        <option value="Beige">
                    </datalist>
                </div>

                <!-- Immatriculation -->
                <div class="form-section mt-4">
                    <h3 class="section-title">
                        <i class="fas fa-id-card"></i>
                        Immatriculation
                    </h3>
                    
                    <div class="form-row mt-2">
                        <div class="form-group">
                            <i class="fas fa-credit-card input-icon"></i>
                            <label for="plaque_immatriculation">Plaque d'immatriculation <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="text" 
                                        id="plaque_immatriculation" 
                                        name="plaque_immatriculation" 
                                        class="form-control plaque-input" 
                                        required 
                                        placeholder="AA-123-BB"
                                        pattern="[A-Z]{2}-[0-9]{3}-[A-Z]{2}|[0-9]{1,4}-[A-Z]{2,3}-[0-9]{2}"
                                        maxlength="12">
                            </div>
                            <small class="form-help">Format: AA-123-BB (nouveau) ou 1234-AB-12 (ancien)</small>
                        </div>

                        <div class="form-group mt-4">
                            <i class="fas fa-calendar input-icon"></i>
                            <label for="date_premiere_immatriculation">Date de 1ère immatriculation <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="date" 
                                        id="date_premiere_immatriculation" 
                                        name="date_premiere_immatriculation" 
                                        class="form-control" 
                                        required
                                        max="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Type d'énergie -->
                <div class="form-section mt-4">
                    <h3 class="section-title">
                        <i class="fas fa-gas-pump"></i>
                        Type d'énergie
                    </h3>
                    
                    <div class="energy-options mt-2">
                        <div class="energy-card d-flex gap-2">
                            <input type="radio" id="essence" name="energie" value="essence" required>
                            <label for="essence" class="d-flex gap-2">
                                <div class="energy-icon" >
                                    <i class="fas fa-gas-pump"></i>
                                </div>
                                <span>Essence</span>
                            </label>
                        </div>

                        <div class="energy-card d-flex gap-2">
                            <input type="radio" id="diesel" name="energie" value="diesel" required>
                            <label for="diesel" class="d-flex gap-2">
                                <div class="energy-icon" >
                                    <i class="fas fa-oil-can"></i>
                                </div>
                                <span>Diesel</span>
                            </label>
                        </div>

                        <div class="energy-card d-flex gap-2">
                            <input type="radio" id="electrique" name="energie" value="electrique" required>
                            <label for="electrique" class="d-flex gap-2">
                                <div class="energy-icon" >
                                    <i class="fas fa-plug"></i>
                                </div>
                                <span>Électrique</span>
                            </label>
                        </div>

                        <div class="energy-card d-flex gap-2">
                            <input type="radio" id="hybride" name="energie" value="hybride" required>
                            <label for="hybride" class="d-flex gap-2">
                                <div class="energy-icon" >
                                    <i class="fas fa-leaf"></i>
                                </div>
                                <span>Hybride</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-check"></i>
                        Ajouter 
                    </button>
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
$pageSpecificJs = 'vehicle.js';
require_once 'app/views/includes/footer.php';
?>