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
        
                                    <!-- Boutons d'action -->
                                    <div class="vehicle-actions align-items-center">
                                        <button 
                                            class="btn btn-sm btn-danger delete-vehicle-btn" 
                                            data-vehicle-id="<?= $vehicule['id'] ?>"
                                            data-vehicle-name="<?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?>"
                                            title="Supprimer">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </button>
                                    </div>
                                    <hr>
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
                    
                                    <!-- Actions sur le covoiturage -->
                                    <div class="trip-actions justify-content-center">
                                        <a href="/covoiturage/<?= $covoit['id'] ?>" class="btn btn-outline btn-sm">
                                            <i class="fas fa-eye"></i>
                                            Détails
                                        </a>

                                        <?php if ($covoit['statut'] === 'prevu' && strtotime($covoit['date_depart'] . ' ' . $covoit['heure_depart']) > time()): ?>
                                        <button 
                                            type="button"
                                            class="btn btn-danger btn-sm cancel-covoiturage-btn" 
                                            data-covoiturage-id="<?= $covoit['id'] ?>"
                                            data-covoiturage-route="<?= htmlspecialchars($covoit['ville_depart'] . ' → ' . $covoit['ville_arrivee']) ?>"
                                            data-covoiturage-date="<?= date('d/m/Y à H:i', strtotime($covoit['date_depart'] . ' ' . $covoit['heure_depart'])) ?>">
                                            <i class="fas fa-times"></i>
                                            Annuler
                                        </button>
                                        <?php endif; ?>

                                        <?php if ($covoit['statut'] === 'annule'): ?>
                                        <button 
                                            type="button"
                                            class="btn btn-danger btn-sm delete-covoiturage-btn" 
                                            data-covoiturage-id="<?= $covoit['id'] ?>"
                                            data-covoiturage-route="<?= htmlspecialchars($covoit['ville_depart'] . ' → ' . $covoit['ville_arrivee']) ?>">
                                            <i class="fas fa-trash"></i>
                                            Supprimer
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                    <hr>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($mes_covoiturages) > 3): ?>
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
                                                <span><i class="fas fa-user"></i> Avec <?= htmlspecialchars($reservation['chauffeur_pseudo']) ?>
                                                <a href="/messagerie/creer/<?= $reservation['covoiturage_id'] ?>" class="btn btn-success mb-2 messagerie-btn">
                                            <i class="fas fa-comments"></i></a></span>
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
                                    </div>
                                    
                                    <div class="trip-actions">
                                        <a href="/covoiturage/<?= $reservation['covoiturage_id'] ?>" class="btn btn-outline btn-sm">
                                            <i class="fas fa-eye"></i>
                                            Détails
                                        </a>

                                        <?php if ($reservation['statut'] === 'confirmee' && strtotime($reservation['date_depart'] . ' ' . $reservation['heure_depart']) > time() + 2*3600): ?>
                                        <button 
                                            type="button"
                                            class="btn btn-danger btn-sm cancel-reservation-btn" 
                                            data-reservation-id="<?= $reservation['id'] ?>"
                                            data-reservation-route="<?= htmlspecialchars($reservation['ville_depart'] . ' → ' . $reservation['ville_arrivee']) ?>"
                                            data-reservation-date="<?= date('d/m/Y à H:i', strtotime($reservation['date_depart'] . ' ' . $reservation['heure_depart'])) ?>">
                                            <i class="fas fa-times"></i>
                                            Annuler
                                        </button>
                                        <?php endif; ?>

                                        <?php if ($reservation['statut'] === 'annule'): ?>
                                        <button 
                                            type="button"
                                            class="btn btn-danger btn-sm delete-reservation-btn" 
                                            data-reservation-id="<?= $reservation['id'] ?>"
                                            data-reservation-route="<?= htmlspecialchars($reservation['ville_depart'] . ' → ' . $reservation['ville_arrivee']) ?>">
                                            <i class="fas fa-trash"></i>
                                            Supprimer
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <hr>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($mes_reservations) > 5): ?>
                                <div class="show-more">
                                    <a href="/mes-reservations" class="btn btn-outline">Voir toutes mes réservations</a>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="empty-state">
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
                        <h3><i class="fas fa-sliders-h"></i> Mes préférences</h3>
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
<div id="deleteModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Confirmer la suppression</h3>
        </div>
        <div class="modal-body">
            <p>Êtes-vous sûr de vouloir supprimer ce véhicule ?</p>
            <div class="vehicle-info-modal">
                <i class="fas fa-car"></i>
                <strong id="vehicleName"></strong>
            </div>
            <p class="warning-text">
                <i class="fas fa-info-circle"></i>
                Cette action est irréversible.
            </p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="closeModal">
                <i class="fas fa-times"></i> Annuler
            </button>
            <form id="deleteForm" method="POST" style="display: inline;">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal d'annulation de réservation -->
<div id="cancelReservationModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Annuler la réservation</h3>
        </div>
        <div class="modal-body">
            <p>Êtes-vous sûr de vouloir annuler cette réservation ?</p>
            <div class="reservation-info-modal">
                <i class="fas fa-route"></i>
                <div>
                    <strong id="reservationRoute"></strong><br>
                    <small id="reservationDate"></small>
                </div>
            </div>
            <p class="warning-text">
                <i class="fas fa-info-circle"></i>
                Cette action est irréversible. Le conducteur sera notifié.
            </p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="closeCancelModal">
                <i class="fas fa-arrow-left"></i> Retour
            </button>
            <form id="cancelReservationForm" method="POST" action="/reservation/annuler" style="display: inline;">
                <input type="hidden" name="reservation_id" id="reservationIdToCancel">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-times"></i> Annuler la réservation
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal de suppression de réservation -->
<div id="deleteReservationModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-trash"></i> Supprimer la réservation</h3>
        </div>
        <div class="modal-body">
            <p>Voulez-vous supprimer définitivement cette réservation annulée ?</p>
            <div class="reservation-info-modal">
                <i class="fas fa-route"></i>
                <strong id="deleteReservationRoute"></strong>
            </div>
            <p class="warning-text">
                <i class="fas fa-info-circle"></i>
                Cette action est irréversible.
            </p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="closeDeleteReservationModal">
                <i class="fas fa-times"></i> Annuler
            </button>
            <form id="deleteReservationForm" method="POST" action="/reservation/supprimer" style="display: inline;">
                <input type="hidden" name="reservation_id" id="reservationIdToDelete">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal d'annulation de covoiturage -->
<div id="cancelCovoiturageModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Annuler le covoiturage</h3>
        </div>
        <div class="modal-body">
            <p>Êtes-vous sûr de vouloir annuler ce covoiturage ?</p>
            <div class="reservation-info-modal">
                <i class="fas fa-route"></i>
                <div>
                    <strong id="covoiturageRoute"></strong><br>
                    <small id="covoiturageDate"></small>
                </div>
            </div>
            <p class="warning-text">
                <i class="fas fa-info-circle"></i>
                Cette action annulera toutes les réservations associées. Les passagers seront notifiés.
            </p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="closeCancelCovoiturageModal">
                <i class="fas fa-arrow-left"></i> Retour
            </button>
            <form id="cancelCovoiturageForm" method="POST" action="/covoiturage/annuler" style="display: inline;">
                <input type="hidden" name="covoiturage_id" id="covoiturageIdToCancel">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-times"></i> Annuler le covoiturage
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal de suppression de covoiturage -->
<div id="deleteCovoiturageModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-trash"></i> Supprimer le covoiturage</h3>
        </div>
        <div class="modal-body">
            <p>Voulez-vous supprimer définitivement ce covoiturage annulé ?</p>
            <div class="reservation-info-modal">
                <i class="fas fa-route"></i>
                <strong id="deleteCovoiturageRoute"></strong>
            </div>
            <p class="warning-text">
                <i class="fas fa-info-circle"></i>
                Cette action est irréversible.
            </p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="closeDeleteCovoiturageModal">
                <i class="fas fa-times"></i> Annuler
            </button>
            <form id="deleteCovoiturageForm" method="POST" action="/covoiturage/supprimer" style="display: inline;">
                <input type="hidden" name="covoiturage_id" id="covoiturageIdToDelete">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </form>
        </div>
    </div>
</div>


<?php
$pageSpecificJs = ['vehicle.js', 'profile.js'];
require_once 'app/views/includes/footer.php';
?>