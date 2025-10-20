<?php
$pageSpecificCss = 'monCompte.css';
require_once 'app/views/includes/head-header.php';
?>

<main>
    <div class="container mt-4 mb-5">
        <div class="row">
            <!-- Retour -->
            <div class="col-12 mb-3">
                <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
            
            <!-- Profil -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <!-- Photo -->
                        <?php if (!empty($data['user']['photo'])): ?>
                            <img src="/<?= htmlspecialchars($data['user']['photo']) ?>" 
                                 alt="Photo de <?= htmlspecialchars($data['user']['pseudo']) ?>"
                                 class="rounded-circle mb-3"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center text-white mb-3"
                                 style="width: 150px; height: 150px; font-size: 4rem;">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Pseudo -->
                        <h3><?= htmlspecialchars($data['user']['pseudo']) ?></h3>
                        
                        <!-- Statut -->
                        <p class="text-muted mb-3">
                            <i class="fas fa-<?= $data['user']['statut'] === 'chauffeur' ? 'car' : 'user' ?>"></i>
                            <?= ucfirst($data['user']['statut']) ?>
                        </p>
                        
                        <!-- Note -->
                        <?php if ($data['user']['nombre_avis'] > 0): ?>
                            <div class="mb-3">
                                <div class="text-warning fs-5">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star<?= $i <= round($data['user']['note_moyenne']) ? '' : '-o' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <div class="text-muted small">
                                    <?= number_format($data['user']['note_moyenne'], 1) ?>/5 
                                    (<?= $data['user']['nombre_avis'] ?> avis)
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-muted small">Aucun avis</p>
                        <?php endif; ?>
                        
                        <!-- Statistiques -->
                        <div class="border-top pt-3">
                            <?php if ($data['user']['statut'] === 'chauffeur'): ?>
                                <p class="mb-2">
                                    <i class="fas fa-route"></i>
                                    <strong><?= $data['user']['nb_trajets'] ?></strong> trajet<?= $data['user']['nb_trajets'] > 1 ? 's' : '' ?>
                                </p>
                            <?php else: ?>
                                <p class="mb-2">
                                    <i class="fas fa-check-circle"></i>
                                    <strong><?= $data['user']['nb_reservations'] ?></strong> réservation<?= $data['user']['nb_reservations'] > 1 ? 's' : '' ?>
                                </p>
                            <?php endif; ?>
                            
                            <p class="mb-0 text-muted small">
                                <i class="fas fa-calendar"></i>
                                Membre depuis <?= $data['user']['anciennete'] ?>
                            </p>
                        </div>

                        <!-- Préférences du chauffeur -->
                        <?php if (!empty($preferences)): ?>
                            <div class="section-content mt-3 pt-3 border-top">
                                <h3><i class="fas fa-sliders-h"></i> Préférences du chauffeur</h3>
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
                        <?php endif; ?>
                        
                        <!-- Actions -->
                        <?php if (!$data['isOwnProfile']): ?>
                            <div class="border-top pt-3 mt-3">
                                <?php if ($data['conversationExists']): ?>
                                    <a href="/messagerie/conversation/<?= $data['conversationExists'] ?>" 
                                       class="btn btn-success w-100">
                                        <i class="fas fa-comments"></i> Voir la conversation
                                    </a>
                                <?php else: ?>
                                    <p class="text-muted small mb-0">
                                        <i class="fas fa-info-circle"></i>
                                        Réservez un trajet pour pouvoir discuter
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Avis -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-star"></i> Avis reçus
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (empty($data['avis'])): ?>
                            <p class="text-muted text-center py-5">
                                <i class="fas fa-inbox fs-1 d-block mb-3"></i>
                                Aucun avis pour le moment
                            </p>
                        <?php else: ?>
                            <?php foreach ($data['avis'] as $avis): ?>
                                <div class="mb-4 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <strong><?= htmlspecialchars($avis['evaluateur_pseudo']) ?></strong>
                                            <div class="text-warning small">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star<?= $i <= $avis['note'] ? '' : '-o' ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <?= date('d/m/Y', strtotime($avis['date_creation'])) ?>
                                        </small>
                                    </div>
                                    
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-route"></i>
                                        <?= htmlspecialchars($avis['ville_depart']) ?> 
                                        <i class="fas fa-arrow-right"></i> 
                                        <?= htmlspecialchars($avis['ville_arrivee']) ?>
                                        • <?= date('d/m/Y', strtotime($avis['date_depart'])) ?>
                                    </p>
                                    
                                    <?php if (!empty($avis['commentaire'])): ?>
                                        <p class="mb-0">
                                            "<?= htmlspecialchars($avis['commentaire']) ?>"
                                        </p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once 'app/views/includes/footer.php';
?>