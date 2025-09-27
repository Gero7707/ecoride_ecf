<?php
$pageSpecificCss = 'monCompte.css';
require_once 'app/views/includes/head-header.php';
?>
<!-- Page Mon Compte -->
<main>
    <div class="profile-actions d-flex justify-content-around mb-4">
        <a href="/mon-compte/modifier" class="btn btn-primary">
            <i class="fas fa-edit"></i>
            Modifier profil
        </a>
        <?php if ($user['statut'] === 'chauffeur'): ?>
            <a href="/covoiturage/proposer" class="btn btn-success">
                <i class="fas fa-plus"></i>
                Publier trajet
            </a>
        <?php endif; ?>
    </div>
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
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- En-tête profil -->
    <section class="profile-header">
        <div class="container">
            <div class="profile-banner">
                <div class="profile-avatar">
                    <?php if (!empty($user['photo']) && file_exists($user['photo'])): ?>
                        <img src="/<?= htmlspecialchars($user['photo']) ?>" alt="Avatar de <?= htmlspecialchars($user['pseudo']) ?>" class="avatar-img">
                    <?php else: ?>
                        <div class="avatar-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <hr>
                <div class="profile-info">
                    <h1 class="profile-name"><?= htmlspecialchars($user['pseudo']) ?></h1>
                    <div class="profile-status infos d-flex flex-column gap-2">
                        <span class="status-badge status-<?= $user['statut'] ?>">
                            <i class="fas fa-<?= $user['statut'] === 'chauffeur' ? 'car' : 'user' ?>"></i>
                            <?= ucfirst($user['statut']) ?>
                        </span>
                        <?php if ($stats['note_moyenne'] > 0): ?>
                            <span class="rating-badge">
                                <i class="fas fa-star"></i>
                                <?= $stats['note_moyenne'] ?>/5
                                <small>(<?= $stats['nb_avis'] ?> avis)</small>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="profile-meta infos">
                        <span><i class="fas fa-calendar-alt"></i> Membre depuis <?= date('M Y', strtotime($user['date_creation'])) ?></span>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

    <!-- Statistiques -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card d-flex gap-2">
                    <div class="stat-icon stat-credits">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="stat-content d-flex gap-2">
                        <div class="stat-number"><?= $stats['credits'] ?></div>
                        <div class="stat-label">Crédits</div>
                    </div>
                </div>
                <hr>
                
                <div class="stat-card d-flex gap-2">
                    <div class="stat-icon stat-trips">
                        <i class="fas fa-route"></i>
                    </div>
                    <div class="stat-content d-flex gap-2">
                        <div class="stat-number"><?= $stats['trajets_proposes'] ?></div>
                        <div class="stat-label">Trajets proposés</div>
                    </div>
                </div>
                <hr>
                
                <div class="stat-card d-flex gap-2">
                    <div class="stat-icon stat-completed">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content d-flex gap-2">
                        <div class="stat-number"><?= $stats['trajets_termines'] ?></div>
                        <div class="stat-label">Trajets terminés</div>
                    </div>
                </div>
                <hr>
                
                <?php if ($stats['note_moyenne'] > 0): ?>
                <div class="stat-card d-flex gap-2">
                    <div class="stat-icon stat-rating">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-content d-flex gap-2">
                        <div class="stat-number"><?= $stats['note_moyenne'] ?><strong>/5</strong></div>
                        <div class="stat-label">Note moyenne</div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Contenu principal -->
    <section class="profile-content">
        <div class="container">
            <div class="content-grid">
                
                <!-- Section véhicules (pour chauffeurs) -->
                <?php if ($user['statut'] === 'chauffeur'): ?>
                <div class="content-section">
                    <div class="section-header d-flex">
                        <h3><i class="fas fa-car"></i> Mes véhicules</h3>
                        <a href="/vehicule/ajouter" class="btn btn-sm btn-outline">
                            <i class="fas fa-plus"></i> Ajouter
                        </a>
                    </div>
                    <hr>
                    <div class="section-content">
                        <?php if (!empty($vehicules)): ?>
                            <?php foreach ($vehicules as $vehicule): ?>
                                <div class="vehicle-card">
                                    <div class="vehicle-info">
                                        <div class="vehicle-details">
                                            <h4><?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?></h4>
                                            <div class="vehicle-meta d-flex flex-column gap-2">
                                                <span class="infos"><i class="fas fa-palette"></i> <?= htmlspecialchars($vehicule['couleur']) ?></span>
                                                <span class="infos"><i class="fas fa-users"></i> <?= $vehicule['nombre_places'] ?> places</span>
                                                <span class="infos"><i class="fas fa-id-card"></i> <?= htmlspecialchars($vehicule['plaque_immatriculation']) ?></span>
                                            </div>
                                        </div>
                                        <div class="vehicle-energy">
                                            <i class="fa-solid fa-gas-pump"></i>
                                            <span class="energy-badge energy-<?= $vehicule['energie'] ?>">
                                                <?= ucfirst($vehicule['energie']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-car"></i>
                                <p>Aucun véhicule enregistré</p>
                                <a href="/vehicule/ajouter" class="btn btn-primary">Ajouter mon premier véhicule</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <hr>
                </div>
                

                <!-- Mes covoiturages -->
                <div class="content-section">
                    <div class="section-header">
                        <h3><i class="fas fa-map-marked-alt"></i> Mes covoiturages</h3>
                    </div>
                    <hr>
                    <div class="section-content">
                        <?php if (!empty($mes_covoiturages)): ?>
                            <?php foreach (array_slice($mes_covoiturages, 0, 5) as $covoit): ?>
                                <div class="trip-card">
                                    <div class="trip-route">
                                        <div class="route-info">
                                            <h4><?= htmlspecialchars($covoit['ville_depart'] . ' → ' . $covoit['ville_arrivee']) ?></h4>
                                            <div class="trip-meta d-flex flex-column gap-2">
                                                <span class="infos"><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($covoit['date_depart'])) ?></span>
                                                <span class="infos"><i class="fas fa-clock"></i> <?= date('H:i', strtotime($covoit['heure_depart'])) ?></span>
                                                <span class="infos"><i class="fas fa-users"></i> <?= $covoit['nb_reservations'] ?> réservation(s)</span>
                                                <span class="infos"><i class="fas fa-euro-sign"></i> <?= $covoit['prix'] ?>€</span>
                                            </div>
                                        </div>
                                        <div class="trip-status">
                                            <span class="status-badge status-<?= $covoit['statut'] ?>">
                                                <?= ucfirst(str_replace('_', ' ', $covoit['statut'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($mes_covoiturages) > 5): ?>
                                <div class="show-more">
                                    <a href="/mes-covoiturages" class="btn btn-outline">Voir tous mes covoiturages</a>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-map-marked-alt"></i>
                                <p>Aucun covoiturage proposé</p>
                                <a href="/covoiturage/proposer" class="btn btn-primary">Proposer mon premier trajet</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php else: ?>
                <!-- Mes réservations (pour passagers) -->
                <div class="content-section">
                    <div class="section-header">
                        <h3><i class="fas fa-check-circle"></i> Mes réservations</h3>
                    </div>
                    <hr>
                    <div class="section-content">
                        <?php if (!empty($mes_reservations)): ?>
                            <?php foreach (array_slice($mes_reservations, 0, 5) as $reservation): ?>
                                <div class="trip-card">
                                    <div class="trip-route">
                                        <div class="route-info">
                                            <h4><?= htmlspecialchars($reservation['ville_depart'] . ' → ' . $reservation['ville_arrivee']) ?></h4>
                                            <div class="trip-meta">
                                                <span><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($reservation['date_depart'])) ?></span>
                                                <span><i class="fas fa-clock"></i> <?= date('H:i', strtotime($reservation['heure_depart'])) ?></span>
                                                <span><i class="fas fa-user"></i> Avec <?= htmlspecialchars($reservation['chauffeur_pseudo']) ?></span>
                                                <span><i class="fas fa-euro-sign"></i> <?= $reservation['prix'] ?>€</span>
                                            </div>
                                            <?php if (!empty($reservation['marque']) && !empty($reservation['modele'])): ?>
                                                <div class="vehicle-info-small">
                                                    <i class="fas fa-car"></i> <?= htmlspecialchars($reservation['marque'] . ' ' . $reservation['modele']) ?>
                                                    <?php if (!empty($reservation['couleur'])): ?>
                                                        <span class="vehicle-color"><?= htmlspecialchars($reservation['couleur']) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="trip-status">
                                            <span class="status-badge status-<?= $reservation['statut'] ?>">
                                                <?= ucfirst($reservation['statut']) ?>
                                            </span>
                                        </div>
                                        <hr>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($mes_reservations) > 5): ?>
                                <div class="show-more">
                                    <a href="/mes-reservations" class="btn btn-outline">Voir toutes mes réservations</a>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-ticket-alt"></i>
                                <p>Aucune réservation</p>
                                <a href="/covoiturages" class="btn btn-primary">Rechercher un trajet</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Avis -->
                <div class="content-section">
                    <div class="section-header">
                        <h3><i class="fas fa-star"></i> <?= $user['statut'] === 'chauffeur' ? 'Avis reçus' : 'Avis donnés' ?></h3>
                    </div>
                    <hr>
                    <div class="section-content">
                        <?php 
                        $avis_list = $user['statut'] === 'chauffeur' ? $avis_recus : $avis_donnes;
                        if (!empty($avis_list)): ?>
                            <?php foreach (array_slice($avis_list, 0, 3) as $avis): ?>
                                <div class="review-card">
                                    <div class="review-header">
                                        <div class="review-route">
                                            <strong><?= htmlspecialchars($avis['ville_depart'] . ' → ' . $avis['ville_arrivee']) ?></strong>
                                            <span class="review-date"><?= date('d/m/Y', strtotime($avis['date_creation'])) ?></span>
                                        </div>
                                        <div class="review-rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?= $i <= $avis['note'] ? ' star-filled' : ' star-empty' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <div class="review-content">
                                        <div class="review-author">
                                            <i class="fas fa-user"></i>
                                            <?php if ($user['statut'] === 'chauffeur'): ?>
                                                Par <?= htmlspecialchars($avis['evaluateur_pseudo']) ?>
                                            <?php else: ?>
                                                Pour <?= htmlspecialchars($avis['evalue_pseudo']) ?>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($avis['commentaire'])): ?>
                                            <div class="review-comment">
                                                "<?= htmlspecialchars($avis['commentaire']) ?>"
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <hr>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($avis_list) > 3): ?>
                                <div class="show-more">
                                    <a href="/mes-avis" class="btn btn-outline">Voir tous les avis</a>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-star"></i>
                                <p><?= $user['statut'] === 'chauffeur' ? 'Aucun avis reçu' : 'Aucun avis donné' ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Préférences chauffeur -->
                <?php if ($user['statut'] === 'chauffeur' && !empty($preferences)): ?>
                <div class="content-section">
                    <div class="section-header">
                        <h3><i class="fas fa-cog"></i> Mes préférences</h3>
                    </div>
                    <hr>
                    <div class="section-content">
                        <div class="preferences-display">
                            <div class="preference-item">
                                <i class="fas fa-smoking<?= $preferences['accepte_fumeur'] ? '' : '-ban' ?>"></i>
                                <span><?= $preferences['accepte_fumeur'] ? 'Accepte les fumeurs' : 'Interdit de fumer' ?></span>
                            </div>
                            <div class="preference-item">
                                <i class="fas fa-paw"></i>
                                <span><?= $preferences['accepte_animaux'] ? 'Accepte les animaux' : 'Pas d\'animaux' ?></span>
                            </div>
                            <?php if (!empty($preferences['preferences_custom'])): ?>
                                <div class="preference-custom">
                                    <i class="fas fa-info-circle"></i>
                                    <span><?= htmlspecialchars($preferences['preferences_custom']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </section>
</main>

<?php
require_once 'app/views/includes/footer.php';
?>