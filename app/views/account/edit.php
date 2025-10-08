<?php 
$pageSpecificCss = 'edit.css';
require_once 'app/views/includes/head-header.php';
?>

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
    <section class="edit-header">
        <div class="container">
            <div class="header-content">
                <div class="back-navigation mb-3">
                    <a href="/profil" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Retour au profil
                    </a>
                </div>
                <div class="page-title">
                    <h1><i class="fas fa-user-edit"></i> Modifier mon profil</h1>
                    <p>Mettez à jour vos Infos personnelles</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Formulaire de modification -->
    <section class="edit-form-section">
        <div class="container">
            <form action="/mon-compte/modifier" method="POST" enctype="multipart/form-data" id="editProfileForm" class="edit-form">
                
                <!-- Section photo de profil -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-camera"></i>
                        Photo de profil
                    </h3>
                    <hr>
                    <div class="photo-upload-area ">
                        <div class="current-photo mt-3">
                            <?php if (!empty($user['photo']) && file_exists($user['photo'])): ?>
                                <img src="/<?= htmlspecialchars($user['photo']) ?>" alt="Photo de profil" id="photoPreview" class="profile-photo">
                            <?php else: ?>
                                <div class="photo-placeholder" id="photoPreview">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <hr>
                        <div class="photo-actions mt-3">
                            <label for="photo" class="btn btn-outline">
                                <i class="fas fa-camera"></i>
                                Changer la photo
                            </label>
                            <input type="file" id="photo" name="photo" accept="image/*" class="file-input mt-3">
                            <?php if (!empty($user['photo'])): ?>
                                <a href="/mon-compte/supprimer-photo" class="btn btn-outline btn-danger mt-3" onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre photo ?')">
                                    <i class="fas fa-trash"></i>
                                    Supprimer
                                </a>
                            <?php endif; ?>
                        </div>
                        <small class="form-help mt-3">
                            Formats acceptés : JPG, PNG, GIF (max 5MB)
                        </small>
                    </div>
                    <hr>
                </div>

                <!-- Section informations personnelles -->
                <div class="form-section">
                    <h3 class="section-title mb-3">
                        <i class="fas fa-user"></i>
                        Infos personnelles
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="pseudo">Pseudo <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="text" 
                                        id="pseudo" 
                                        name="pseudo" 
                                        value="<?= htmlspecialchars($user['pseudo']) ?>" 
                                        required 
                                        maxlength="50"
                                        class="form-control">
                            </div>
                            <small class="form-help">Votre pseudo public visible par les autres utilisateurs</small>
                        </div>

                        <div class="form-group">
                            <i class="fas fa-envelope input-icon"></i> <label for="email">Email <span class="required">*</span></label> 
                            <div class="input-group">
                                <input type="email" 
                                        id="email" 
                                        name="email" 
                                        value="<?= htmlspecialchars($user['email']) ?>" 
                                        required 
                                        maxlength="100"
                                        class="form-control">
                            </div>
                            <small class="form-help">Votre adresse email de connexion</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <i class="fas fa-phone input-icon"></i><label for="telephone">Téléphone</label>
                            <div class="input-group">
                                <input type="tel" 
                                        id="telephone" 
                                        name="telephone" 
                                        value="<?= htmlspecialchars($user['telephone'] ?? '') ?>" 
                                        maxlength="20"
                                        pattern="[0-9+\-\.\s\(\)]+"
                                        class="form-control">
                            </div>
                            <small class="form-help">Numéro visible par vos co-voyageurs</small>
                        </div>

                        <div class="form-group">
                            <i class="fas fa-id-badge input-icon"></i><label for="statut">Statut</label>
                            <div class="input-group">
                                <select id="statut" name="statut" class="form-control">
                                    <option value="passager" <?= $user['statut'] === 'passager' ? 'selected' : '' ?>>Passager</option>
                                    <option value="chauffeur" <?= $user['statut'] === 'chauffeur' ? 'selected' : '' ?>>Chauffeur</option>
                                </select>
                            </div>
                            <small class="form-help">Changez votre statut selon vos besoins</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <i class="fas fa-map-marker-alt input-icon"></i><label for="adresse">Adresse</label>
                        <div class="input-group">
                            <input type="text" 
                                    id="adresse" 
                                    name="adresse" 
                                    value="<?= htmlspecialchars($user['adresse'] ?? '') ?>" 
                                    maxlength="255"
                                    placeholder="Votre adresse complète"
                                    class="form-control">
                        </div>
                        <small class="form-help">Adresse utilisée pour calculer les trajets</small>
                    </div>
                </div>

                <!-- Section préférences chauffeur (si applicable) -->
                <div class="form-section" id="chauffeur-preferences" style="<?= $user['statut'] !== 'chauffeur' ? 'display: none;' : '' ?>">
                <hr>
                    <h3 class="section-title"><i class="fas fa-cog"></i>Préférences</h3>
                    <div class="preferences-grid">
                        <div class="preference-item">
                            <div class="checkbox-group">
                                <input type="checkbox" 
                                        id="accepte_fumeur" 
                                        name="accepte_fumeur" 
                                        <?= (!empty($preferences) && $preferences['accepte_fumeur']) ? 'checked' : '' ?>>
                                <label for="accepte_fumeur">
                                    <i class="fas fa-smoking"></i>
                                    J'accepte les fumeurs
                                </label>
                            </div>
                        </div>

                        <div class="preference-item">
                            <div class="checkbox-group">
                                <input type="checkbox" 
                                        id="accepte_animaux" 
                                        name="accepte_animaux" 
                                        <?= (!empty($preferences) && $preferences['accepte_animaux']) ? 'checked' : '' ?>>
                                <label for="accepte_animaux">
                                    <i class="fas fa-paw"></i>
                                    J'accepte les animaux
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="preferences_custom">Préférences personnalisées</label>
                        <textarea id="preferences_custom" 
                                    name="preferences_custom" 
                                    rows="3" 
                                    maxlength="500"
                                    placeholder="Décrivez vos préférences spécifiques (musique, discussions, etc.)"
                                    class="form-control"><?= htmlspecialchars($preferences['preferences_custom'] ?? '') ?></textarea>
                        <small class="form-help">Informations supplémentaires pour vos passagers</small>
                    </div>
                    <hr>
                </div>

                <!-- Section changement de mot de passe -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-lock"></i>
                        Changer le mot de passe
                        <small>(Optionnel)</small>
                    </h3>
                    
                    <div class="form-group">
                        <i class="fas fa-lock input-icon"></i>
                        <label for="current_password">Mot de passe actuel</label>
                        <div class="input-group">
                            <input type="password" 
                                    id="current_password" 
                                    name="current_password"
                                    placeholder="Mot de passe actuel"
                                    class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <i class="fas fa-key input-icon"></i>
                            <label for="new_password">Nouveau mot de passe</label>
                            <div class="input-group">
                                <input type="password" 
                                        id="new_password" 
                                        name="new_password"
                                        placeholder="Nouveau mot de passe"
                                        class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <i class="fas fa-key input-icon"></i>
                            <label for="confirm_password">Confirmer mot de passe</label>
                            <div class="input-group">
                                <input type="password" 
                                        id="confirm_password" 
                                        name="confirm_password"
                                        placeholder="Confirmer nouveau mot de passe"
                                        class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="password-requirements">
                        <small class="form-help">
                            <i class="fas fa-info-circle"></i>
                            Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.
                        </small>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="show_passwords">
                            <label for="show_passwords">
                                <i class="fas fa-eye"></i>
                                Afficher les mots de passe
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions d-flex justify-content-around">
                    <button type="submit" class="btn btn-primary btn-lg">
                        Sauvegarder
                    </button>
                    <a href="/profil" class="btn btn-secondary btn-lg">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </section>
</main>


<?php
$pageSpecificJs = 'auth.js';
require_once 'app/views/includes/footer.php';
?>