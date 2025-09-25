<?php
$pageSpecificCss = 'details.css';
require_once 'app/views/includes/head-header.php';
?>


<!-- Page des détails du covoiturage -->
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
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- En-tête du trajet -->
    <section class="trip-header">
        <div class="container">
            <div class="trip-route-main">
                <div class="route-cities">
                    <div class="departure-city">
                        <i class="fas fa-circle departure-dot"></i>
                        <h1><?= htmlspecialchars($covoiturage['ville_depart']) ?></h1>
                        <span class="city-label">Départ</span>
                    </div>
                    <div class="route-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    <div class="arrival-city">
                        <i class="fas fa-map-marker-alt arrival-dot"></i>
                        <h1><?= htmlspecialchars($covoiturage['ville_arrivee']) ?></h1>
                        <span class="city-label">Arrivée</span>
                    </div>
                </div>
                
                <div class="trip-datetime">
                    <div class="trip-date">
                        <i class="fas fa-calendar-alt"></i>
                        <span><?= date('l d F Y', strtotime($covoiturage['date_depart'])) ?></span>
                    </div>
                    <div class="trip-time">
                        <i class="fas fa-clock"></i>
                        <span>Départ à <?= date('H:i', strtotime($covoiturage['heure_depart'])) ?></span>
                        <?php if (!empty($covoiturage['heure_arrivee'])): ?>
                            <span>• Arrivée à <?= date('H:i', strtotime($covoiturage['heure_arrivee'])) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenu principal -->
    <section class="trip-details-content">
        <div class="container">
            <div class="details-grid">
                
                <!-- Informations principales -->
                <div class="main-info">
                    
                    <!-- Prix et disponibilité -->
                    <div class="price-availability-card">
                        <div class="price-section">
                            <div class="price-amount">
                                <span class="price-value"><?= number_format($covoiturage['prix'], 2, ',', ' ') ?>€</span>
                                <span class="price-label">par personne</span>
                            </div>
                            <div class="availability">
                                <i class="fas fa-users"></i>
                                <span><?= $covoiturage['places_disponibles'] ?> place<?= $covoiturage['places_disponibles'] > 1 ? 's' : '' ?> disponible<?= $covoiturage['places_disponibles'] > 1 ? 's' : '' ?></span>
                            </div>
                        </div>
                        
                        <!-- Bouton de réservation -->
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $covoiturage['chauffeur_id']): ?>
                            <?php if ($covoiturage['places_disponibles'] > 0 && $covoiturage['statut'] === 'prevu'): ?>
                                <form method="POST" action="/reservation/creer" class="reservation-form">
                                    <input type="hidden" name="covoiturage_id" value="<?= $covoiturage['id'] ?>">
                                    <button type="submit" class="btn btn-primary btn-reserve">
                                        <i class="fas fa-ticket-alt"></i>
                                        Réserver ce trajet
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-disabled" disabled>
                                    <i class="fas fa-times"></i>
                                    <?= $covoiturage['statut'] !== 'prevu' ? 'Trajet non disponible' : 'Complet' ?>
                                </button>
                            <?php endif; ?>
                        <?php elseif (!isset($_SESSION['user_id'])): ?>
                            <a href="/connexion" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i>
                                Se connecter pour réserver
                            </a>
                        <?php else: ?>
                            <div class="owner-notice">
                                <i class="fas fa-info-circle"></i>
                                C'est votre trajet
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Informations du véhicule -->
                    <?php if (!empty($covoiturage['marque'])): ?>
                    <div class="vehicle-info-card">
                        <h3><i class="fas fa-car"></i> Véhicule</h3>
                        <div class="vehicle-details">
                            <div class="vehicle-main">
                                <h4><?= htmlspecialchars($covoiturage['marque'] . ' ' . $covoiturage['modele']) ?></h4>
                                <div class="vehicle-specs">
                                    <?php if (!empty($covoiturage['couleur'])): ?>
                                        <span class="spec"><i class="fas fa-palette"></i> <?= htmlspecialchars($covoiturage['couleur']) ?></span>
                                    <?php endif; ?>
                                    <span class="spec"><i class="fas fa-users"></i> <?= $covoiturage['places_disponibles'] ?> places</span>
                                    <?php if (!empty($covoiturage['energie'])): ?>
                                        <span class="energy-badge energy-<?= $covoiturage['energie'] ?>">
                                            <?= ucfirst($covoiturage['energie']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Statut du trajet -->
                    <div class="status-card">
                        <h3><i class="fas fa-info-circle"></i> Statut du trajet</h3>
                        <div class="status-info">
                            <span class="status-badge status-<?= $covoiturage['statut'] ?>">
                                <?php
                                $status_labels = [
                                    'prevu' => 'Prévu',
                                    'en_cours' => 'En cours',
                                    'termine' => 'Terminé',
                                    'annule' => 'Annulé'
                                ];
                                echo $status_labels[$covoiturage['statut']] ?? ucfirst($covoiturage['statut']);
                                ?>
                            </span>
                            <span class="creation-date">
                                Publié le <?= date('d/m/Y à H:i', strtotime($covoiturage['date_creation'])) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Sidebar droite -->
                <div class="sidebar">
                    
                    <!-- Profil du chauffeur -->
                    <div class="driver-card">
                        <h3><i class="fas fa-user"></i> Votre chauffeur</h3>
                        <div class="driver-profile">
                            <div class="driver-avatar">
                                <?php if (!empty($covoiturage['photo_chauffeur']) && file_exists($covoiturage['photo_chauffeur'])): ?>
                                    <img src="<?= htmlspecialchars($covoiturage['photo_chauffeur']) ?>" alt="Photo de <?= htmlspecialchars($covoiturage['pseudo_chauffeur']) ?>" class="avatar-img">
                                <?php else: ?>
                                    <div class="avatar-placeholder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="driver-info">
                                <h4><?= htmlspecialchars($covoiturage['pseudo']) ?></h4>
                                <?php if (isset($covoiturage['note_moyenne']) && $covoiturage['note_moyenne'] > 0): ?>
                                    <div class="driver-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star<?= $i <= round($covoiturage['note_moyenne']) ? ' star-filled' : ' star-empty' ?>"></i>
                                        <?php endfor; ?>
                                        <span class="rating-text"><?= number_format($covoiturage['note_moyenne'], 1) ?>/5</span>
                                    </div>
                                <?php endif; ?>
                                <div class="driver-stats">
                                    <span><i class="fas fa-route"></i> <?= $covoiturage['nb_trajets_chauffeur'] ?? 'N/A' ?> trajets</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Avis sur le chauffeur -->
                    <?php if (!empty($avis_chauffeur)): ?>
                    <div class="reviews-card">
                        <h3><i class="fas fa-star"></i> Avis sur le chauffeur</h3>
                        <div class="reviews-list">
                            <?php foreach (array_slice($avis_chauffeur, 0, 3) as $avis): ?>
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="reviewer-name"><?= htmlspecialchars($avis['evaluateur_pseudo']) ?></div>
                                        <div class="review-rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?= $i <= $avis['note'] ? ' star-filled' : ' star-empty' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($avis['commentaire'])): ?>
                                        <div class="review-comment">
                                            "<?= htmlspecialchars($avis['commentaire']) ?>"
                                        </div>
                                    <?php endif; ?>
                                    <div class="review-date">
                                        <?= date('d/m/Y', strtotime($avis['date_creation'])) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($avis_chauffeur) > 3): ?>
                            <div class="show-more-reviews">
                                <a href="#" class="btn btn-outline btn-sm">Voir tous les avis (<?= count($avis_chauffeur) ?>)</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Actions supplémentaires -->
                    <div class="actions-card">
                        <div class="action-buttons">
                            <a href="/covoiturages?depart=<?= urlencode($covoiturage['ville_depart']) ?>&arrivee=<?= urlencode($covoiturage['ville_arrivee']) ?>" class="btn btn-outline">
                                <i class="fas fa-search"></i>
                                Autres trajets similaires
                            </a>
                            <a href="mailto:contact@ecoride.fr?subject=Signalement trajet <?= $covoiturage['id'] ?>" class="btn btn-outline btn-sm">
                                <i class="fas fa-flag"></i>
                                Signaler ce trajet
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
require_once 'app/views/includes/footer.php';
?>