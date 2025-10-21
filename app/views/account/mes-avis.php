<?php
$pageSpecificCss = 'monCompte.css';
require_once 'app/views/includes/head-header.php';
?>

<!-- Page Mes Avis -->
<main>
    <div class="container mt-4 mb-5">
        <!-- Retour -->
        <div class="row mb-4">
            <div class="col-12">
                <a href="/profil" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Retour au profil
                </a>
            </div>
        </div>

        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <h1>
                        <i class="fas fa-star text-warning"></i> 
                        <?= $data['user_statut'] === 'chauffeur' ? 'Avis reçus' : 'Mes avis' ?>
                    </h1>
                    
                    <?php if ($data['stats']['nb_avis'] > 0): ?>
                        <div class="stats-summary">
                            <div class="rating-badge-lg">
                                <i class="fas fa-star text-warning"></i>
                                <strong><?= number_format($data['stats']['note_moyenne'], 1) ?></strong>/5
                                <small>(<?= $data['stats']['nb_avis'] ?> avis)</small>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Filtres (optionnel) -->
        <?php if ($data['stats']['nb_avis'] > 5): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="/mes-avis" class="row g-3">
                            <div class="col-md-4">
                                <label for="filterNote" class="form-label">
                                    <i class="fas fa-filter"></i> Filtrer par note
                                </label>
                                <select name="note" id="filterNote" class="form-select">
                                    <option value="">Toutes les notes</option>
                                    <option value="5" <?= (isset($_GET['note']) && $_GET['note'] == '5') ? 'selected' : '' ?>>5 étoiles</option>
                                    <option value="4" <?= (isset($_GET['note']) && $_GET['note'] == '4') ? 'selected' : '' ?>>4 étoiles</option>
                                    <option value="3" <?= (isset($_GET['note']) && $_GET['note'] == '3') ? 'selected' : '' ?>>3 étoiles</option>
                                    <option value="2" <?= (isset($_GET['note']) && $_GET['note'] == '2') ? 'selected' : '' ?>>2 étoiles</option>
                                    <option value="1" <?= (isset($_GET['note']) && $_GET['note'] == '1') ? 'selected' : '' ?>>1 étoile</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block">
                                    <i class="fas fa-search"></i> Filtrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Liste des avis -->
        <div class="row">
            <div class="col-12">
                <?php if (!empty($data['avis'])): ?>
                    <div class="avis-list">
                        <?php foreach ($data['avis'] as $avis): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="flex-grow-1">
                                            <!-- Route du trajet -->
                                            <h5 class="mb-2">
                                                <i class="fas fa-route text-primary"></i>
                                                <?= htmlspecialchars($avis['ville_depart']) ?> 
                                                <i class="fas fa-arrow-right mx-2"></i> 
                                                <?= htmlspecialchars($avis['ville_arrivee']) ?>
                                            </h5>
                                            
                                            <!-- Informations sur l'auteur/destinataire -->
                                            <div class="text-muted mb-2">
                                                <?php if ($data['user_statut'] === 'chauffeur'): ?>
                                                    <i class="fas fa-user"></i>
                                                    Avis de <strong><?= htmlspecialchars($avis['evaluateur_pseudo']) ?></strong>
                                                <?php else: ?>
                                                    <i class="fas fa-user"></i>
                                                    Avis pour <strong><?= htmlspecialchars($avis['evalue_pseudo']) ?></strong>
                                                <?php endif; ?>
                                                <span class="ms-3">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    <?= date('d/m/Y', strtotime($avis['date_depart'])) ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Note -->
                                        <div class="rating-display">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?= $i <= $avis['note'] ? ' text-warning' : ' text-muted' ?>"></i>
                                            <?php endfor; ?>
                                            <span class="ms-2 fw-bold"><?= $avis['note'] ?>/5</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Commentaire -->
                                    <?php if (!empty($avis['commentaire'])): ?>
                                        <div class="comment-box p-3 bg-light rounded">
                                            <i class="fas fa-quote-left text-muted"></i>
                                            <p class="mb-0 ms-2 d-inline"><?= htmlspecialchars($avis['commentaire']) ?></p>
                                            <i class="fas fa-quote-right text-muted ms-1"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-muted fst-italic">
                                            <i class="fas fa-comment-slash"></i> Aucun commentaire
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Date de création de l'avis -->
                                    <div class="text-muted small mt-2">
                                        <i class="fas fa-clock"></i>
                                        Avis publié le <?= date('d/m/Y à H:i', strtotime($avis['date_creation'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination si nécessaire -->
                    <?php if ($data['stats']['nb_avis'] > 20): ?>
                        <div class="text-center mt-4">
                            <p class="text-muted">Affichage de <?= count($data['avis']) ?> avis sur <?= $data['stats']['nb_avis'] ?></p>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <!-- État vide -->
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-star fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Aucun avis</h4>
                            <p class="text-muted">
                                <?= $data['user_statut'] === 'chauffeur' 
                                    ? 'Vous n\'avez pas encore reçu d\'avis pour vos trajets.' 
                                    : 'Vous n\'avez pas encore donné d\'avis.' ?>
                            </p>
                            <a href="/covoiturages" class="btn btn-primary mt-3">
                                <i class="fas fa-search"></i> Rechercher un trajet
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php
require_once 'app/views/includes/footer.php';
?>