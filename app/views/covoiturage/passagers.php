<?php
/**
 * Vue : Liste des passagers d'un covoiturage
 * Pour le chauffeur uniquement
 */
$pageSpecificCss = 'details.css';
require_once 'app/views/includes/head-header.php';
?>

<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="/covoiturage/<?= $data['covoiturage']['id'] ?>" class="btn btn-outline-secondary btn-sm mb-2">
                        <i class="bi bi-arrow-left"></i> Retour au trajet
                    </a>
                    <h2>
                        <i class="bi bi-people"></i> Passagers
                    </h2>
                    <p class="text-muted mb-0">
                        <i class="bi bi-geo-alt"></i>
                        <?= htmlspecialchars($data['covoiturage']['ville_depart']) ?> 
                        <i class="bi bi-arrow-right"></i> 
                        <?= htmlspecialchars($data['covoiturage']['ville_arrivee']) ?>
                        <span class="ms-2">
                            <i class="bi bi-calendar"></i>
                            <?= date('d/m/Y', strtotime($data['covoiturage']['date_depart'])) ?> à 
                            <?= date('H:i', strtotime($data['covoiturage']['heure_depart'])) ?>
                        </span>
                    </p>
                </div>
                
                <div class="text-end">
                    <div class="badge bg-primary fs-6">
                        <?= count($data['passagers']) ?> passager<?= count($data['passagers']) > 1 ? 's' : '' ?>
                    </div>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Liste des passagers -->
            <?php if (empty($data['passagers'])): ?>
                <!-- Aucun passager -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-person-x display-1 text-muted"></i>
                        <h4 class="mt-3">Aucun passager pour le moment</h4>
                        <p class="text-muted">
                            Les passagers qui réserveront ce trajet apparaîtront ici.
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <!-- Cartes des passagers -->
                <div class="row">
                    <?php foreach ($data['passagers'] as $passager): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 <?= $passager['statut'] === 'en_attente' ? 'border-warning' : '' ?>">
                                <!-- Badge statut -->
                                <?php if ($passager['statut'] === 'en_attente'): ?>
                                    <div class="position-absolute top-0 start-50 translate-middle-x mt-2">
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-hourglass-split"></i> En attente de confirmation
                                        </span>
                                    </div>
                                <?php elseif ($passager['statut'] === 'confirmee'): ?>
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Confirmé
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <div class="card-body">
                                    <!-- Photo et infos -->
                                    <div class="text-center mb-3 mt-3">
                                        <?php if ($passager['photo']): ?>
                                            <img src="/<?= htmlspecialchars($passager['photo']) ?>" 
                                                    alt="<?= htmlspecialchars($passager['pseudo']) ?>"
                                                    class="rounded-circle mb-2"
                                                    style="width: 80px; height: 80px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center text-white mb-2"
                                                    style="width: 80px; height: 80px; font-size: 2rem;">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <h5 class="mb-1"><?= htmlspecialchars($passager['pseudo']) ?></h5>
                                        
                                        <!-- Note moyenne -->
                                        <?php if ($passager['nombre_avis'] > 0): ?>
                                            <div class="text-warning mb-2">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <?php if ($i <= round($passager['note_moyenne'])): ?>
                                                        <i class="bi bi-star-fill"></i>
                                                    <?php else: ?>
                                                        <i class="bi bi-star"></i>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                                <span class="text-muted small">
                                                    (<?= round($passager['note_moyenne'], 1) ?>/5 - <?= $passager['nombre_avis'] ?> avis)
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-muted small">Aucun avis</p>
                                        <?php endif; ?>
                                        
                                        <p class="text-muted small mb-2">
                                            <i class="bi bi-calendar-check"></i>
                                            Réservé le <?= date('d/m/Y à H:i', strtotime($passager['date_reservation'])) ?>
                                        </p>
                                    </div>

                                    <!-- Actions -->
                                    <div class="d-grid gap-2">
                                        <!-- Voir profil -->
                                        <a href="/utilisateur/<?= $passager['user_id'] ?>" 
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-person"></i> Voir le profil
                                        </a>
                                        
                                        <!-- Envoyer un message -->
                                        <a href="/messagerie/creer/<?= $data['covoiturage']['id'] ?>?passager=<?= $passager['user_id'] ?>" 
                                            class="btn btn-outline-success btn-sm">
                                            <i class="bi bi-chat-dots"></i> Envoyer un message
                                        </a>
                                        
                                        <!-- Confirmer/Refuser si en attente -->
                                        <?php if ($passager['statut'] === 'en_attente'): ?>
                                            <div class="btn-group" role="group">
                                                <form method="POST" 
                                                        action="/reservation/confirmer" 
                                                        class="d-inline w-50"
                                                        onsubmit="return confirm('Confirmer cette réservation ?');">
                                                    <input type="hidden" name="reservation_id" value="<?= $passager['id'] ?>">
                                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                                        <i class="bi bi-check-lg"></i> Accepter
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" 
                                                        action="/reservation/refuser" 
                                                        class="d-inline w-50"
                                                        onsubmit="return confirm('Refuser cette réservation ?');">
                                                    <input type="hidden" name="reservation_id" value="<?= $passager['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                                        <i class="bi bi-x-lg"></i> Refuser
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Téléphone (si confirmé) -->
                                        <?php if ($passager['statut'] === 'confirmee' && $passager['telephone']): ?>
                                            <a href="tel:<?= htmlspecialchars($passager['telephone']) ?>" 
                                                class="btn btn-outline-secondary btn-sm">
                                                <i class="bi bi-telephone"></i> 
                                                <?= htmlspecialchars($passager['telephone']) ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 

require_once 'app/views/includes/footer.php';
?>