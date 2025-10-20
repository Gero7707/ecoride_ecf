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
                        <span class="city-label">Départ</span>
                        <h1><?= htmlspecialchars($covoiturage['ville_depart']) ?></h1>
                        <hr>
                    </div>
                    <div class="route-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    <div class="arrival-city">
                        <i class="fas fa-map-marker-alt arrival-dot"></i>
                        <span class="city-label">Arrivée</span>
                        <h1><?= htmlspecialchars($covoiturage['ville_arrivee']) ?></h1>
                        <hr>
                    </div>
                </div>

                <?php if ($covoiturage['energie'] === 'electrique'): ?>
                    <div class="eco-badge"><img src="https://www.gifsgratuits.fr/ecologie/a%20(40).gif" alt="Emoji écologique" class="gif"> Trajet écologique</div>
                    <?php elseif($covoiturage['energie'] === 'hybride'): ?>
                        <div class="eco-badge"><img src="https://www.gifsgratuits.fr/planete/planete%20(2).gif" alt="Emoji écologique" class="gif"> Trajet hybride</div>
                    <?php else: ?>
                        <div class="eco-badge"><img src="https://www.gifsgratuits.fr/ecologie/a%20(15).gif" alt="Emoji écologique" class="gif2"> Trajet non écologique</div>
                <?php endif; ?>
                <hr>
                
                <div class="trip-datetime">
                    <div class="trip-date d-flex ">
                        <i class="fas fa-calendar-alt"></i>
                        <span><?= date('l d F Y', strtotime($covoiturage['date_depart'])) ?></span>
                    </div>
                    <hr>
                    <div class="trip-time d-flex align-items-center">
                        <i class="fas fa-clock"></i>
                        <span>Départ à <?= date('H:i', strtotime($covoiturage['heure_depart'])) ?></span>
                        <?php if (!empty($covoiturage['heure_arrivee'])): ?>
                            <span> Arrivée à <?= date('H:i', strtotime($covoiturage['heure_arrivee'])) ?></span>
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
                                <div class="price-amount d-flex">
                                    <h3>€</h3>
                                    <span class="price-value"><?= number_format($covoiturage['prix'], 2, ',', ' ') ?>€</span>
                                    <span class="price-label">par personne</span>
                                </div>
                                <hr>
                                <div class="availability d-flex">
                                    <i class="fas fa-users"></i>
                                    <span><?= $covoiturage['places_disponibles'] ?> place<?= $covoiturage['places_disponibles'] > 1 ? 's' : '' ?> disponible<?= $covoiturage['places_disponibles'] > 1 ? 's' : '' ?></span>
                                </div>
        
                                <!-- Bouton Voir les passagers (pour le chauffeur) -->
                                <?php if (isset($_SESSION['user_id']) && $covoiturage['chauffeur_id'] === $_SESSION['user_id']): ?>
                                    <a href="/covoiturage/<?= $covoiturage['id'] ?>/passagers" class="btn btn-primary mt-3">
                                        <i class="bi bi-people"></i> Voir les passagers
                                    </a>
                                <?php endif; ?>
                                <hr>
                            </div>
    
                            <!-- Actions selon le statut -->
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <?php if ($_SESSION['user_id'] === $covoiturage['chauffeur_id']): ?>
                                    <!-- C'EST LE CHAUFFEUR -->
                                    <div class="owner-notice">
                                        <i class="fas fa-info-circle"></i>
                                        C'est votre trajet
                                    </div>
            
                                    <?php elseif ($userReservation): ?>
                                    <!-- L'UTILISATEUR A DÉJÀ RÉSERVÉ -->
            
                                    <?php if ($userReservation['statut'] === 'en_attente'): ?>
                                        <!-- Réservation en attente -->
                                        <div class="alert alert-warning mb-3">
                                            <i class="fas fa-hourglass-half"></i>
                                            <strong>Réservation en attente</strong><br>
                                            <small>Votre demande est en attente de confirmation par le conducteur.</small>
                                        </div>
                
                                        <a href="/messagerie/creer/<?= $covoiturage['id'] ?>" class="btn btn-success mb-2">
                                            <i class="fas fa-comments"></i> Contacter le conducteur
                                        </a>
                
                                        <form method="POST" action="/reservation/annuler" onsubmit="return confirm('Voulez-vous vraiment annuler cette réservation ?');">
                                            <input type="hidden" name="reservation_id" value="<?= $userReservation['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="fas fa-times-circle"></i> Annuler ma réservation
                                            </button>
                                        </form>
                
                                    <?php elseif ($userReservation['statut'] === 'confirmee'): ?>
                                        <!-- Réservation confirmée -->
                                        <div class="alert alert-success mb-3">
                                            <i class="fas fa-check-circle"></i>
                                            <strong>Réservation confirmée !</strong><br>
                                            <small>Votre place est réservée pour ce trajet.</small>
                                        </div>
                
                                        <a href="/messagerie/creer/<?= $covoiturage['id'] ?>" class="btn btn-success mb-2">
                                            <i class="fas fa-comments"></i> Contacter le conducteur
                                        </a>
                
                                        <form method="POST" action="/reservation/annuler" onsubmit="return confirm('Voulez-vous vraiment annuler cette réservation ?');">
                                            <input type="hidden" name="reservation_id" value="<?= $userReservation['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="fas fa-times-circle"></i> Annuler ma réservation
                                            </button>
                                        </form>
                
                                    <?php elseif ($userReservation['statut'] === 'annulee' || $userReservation['statut'] === 'annule'): ?>
                                        <!-- Réservation annulée -->
                                        <div class="alert alert-secondary mb-3">
                                            <i class="fas fa-ban"></i>
                                            <strong>Réservation annulée</strong><br>
                                            <small>Cette réservation a été annulée.</small>
                                        </div>
                
                                        <?php if ($covoiturage['places_disponibles'] > 0 && $covoiturage['statut'] === 'prevu'): ?>
                                            <form method="POST" action="/reservation/creer">
                                                <input type="hidden" name="covoiturage_id" value="<?= $covoiturage['id'] ?>">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-redo"></i> Réserver à nouveau
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
            
                                <?php else: ?>
                                    <!-- L'UTILISATEUR N'A PAS ENCORE RÉSERVÉ -->

                                    <?php if ($covoiturage['places_disponibles'] > 0 && $covoiturage['statut'] === 'prevu'): ?>
                                        <form method="POST" action="/reservation/creer" class="reservation-form">
                                            <input type="hidden" name="covoiturage_id" value="<?= $covoiturage['id'] ?>">
                                            <button type="submit" class="btn btn-primary btn-reserve">
                                                Réserver ce trajet
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-disabled" disabled>
                                            <i class="fas fa-times"></i>
                                            <?= $covoiturage['statut'] !== 'prevu' ? 'Trajet non disponible' : 'Complet' ?>
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
        
                            <?php else: ?>
                                <!-- NON CONNECTÉ -->
                                <a href="/connexion" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt"></i>
                                    Se connecter pour réserver
                                </a>
                            <?php endif; ?>
                        </div>
                    <hr>
                    <!-- Informations du véhicule -->
                    <?php if (!empty($covoiturage['marque'])): ?>
                    <div class="vehicle-info-card">
                        <div class="vehicle-details">
                            <div class="vehicle-main">
                                <div class="d-flex align-items-center car">
                                    <i class="fas fa-car"></i>
                                    <h4><?= htmlspecialchars($covoiturage['marque'] . ' ' . $covoiturage['modele']) ?></h4>
                                </div>
                                <div class="vehicle-specs d-flex flex-column align-items-start ">
                                    <?php if (!empty($covoiturage['couleur'])): ?>
                                        <span class="spec"><i class="fas fa-palette multicolor"></i> <?= htmlspecialchars($covoiturage['couleur']) ?></span>
                                    <?php endif; ?>
                                    <span class="spec"><i class="fas fa-users"></i> <?= $covoiturage['places_disponibles'] ?> places</span>
                                    <?php if (!empty($covoiturage['energie'])): ?>
                                        <span class="energy-badge energy-<?= $covoiturage['energie'] ?>">
                                            <i class="fa-solid fa-gas-pump"></i>  <?= ucfirst($covoiturage['energie']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <?php endif; ?>

                    <!-- Statut du trajet -->
                    <div class="status-card">
                        <div class="status-info d-flex align-items-center">
                            <i class="fas fa-info-circle"></i>
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
                        <hr>
                    </div>
                </div>

                <!-- Sidebar droite -->
                <div class="sidebar">
                    <!-- Profil du chauffeur -->
                    <div class=" d-flex justify-content-center">
                        <div class="driver-card">
                            <h3 class="d-flex justify-content-center"><i class="fas fa-user"></i> Chauffeur :</h3>
                            <div class="driver-profile d-flex justify-content-evenly mt-4">
                                <div class="driver-avatar">
                                    <?php if (!empty($covoiturage['photo_chauffeur'])): ?>
                                        <img src="/<?= htmlspecialchars($covoiturage['photo_chauffeur']) ?>" 
                                                alt="Photo de <?= htmlspecialchars($covoiturage['pseudo'] ?? 'Chauffeur') ?>" 
                                                class="avatar-img">
                                    <?php else: ?>
                                        <div class="avatar-placeholder">
                                            <?= strtoupper(substr($covoiturage['pseudo'] ?? 'U', 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="driver-info ">
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
                                <a href="/utilisateur/<?= $covoiturage['chauffeur_id'] ?>" class="btn btn-sm btn-outline-primary mt-2 ">
                                    Voir profil
                                </a>
                            </div>
                        </div>
                    </div>
                    <hr>
                    
                    <div class="section-content">
                        <h3><i class="fas fa-sliders-h"></i>Préférences du chauffeur</h3>
                        <div class="preferences-display mt-4">
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
                    <hr>
                    
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
                                    <hr>
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
$pageSpecificJs = 'covoiturage.js';
require_once 'app/views/includes/footer.php';
?>