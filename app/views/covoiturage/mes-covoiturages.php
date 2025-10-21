<?php
$pageSpecificCss = 'monCompte.css';
require_once 'app/views/includes/head-header.php';
?>

<main>
    <div class="container mt-4 mb-5">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-column">
                    <div>
                        <h2>
                            <i class="fas fa-car"></i> Mes covoiturages
                            <span class="badge bg-primary"><?= $data['total'] ?></span>
                        </h2>
                        <p class="text-muted text-center mb-0">Gérez tous vos trajets</p>
                    </div>
                    <a href="/covoiturage/creer" class="btn btn-success mt-4">
                        <i class="fas fa-plus"></i> Créer un trajet
                    </a>
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

        <!-- Onglets -->
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#prevu">
                    À venir <span class="badge bg-primary"><?= count($data['groupes']['prevu']) ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#termine">
                    Terminés <span class="badge bg-secondary"><?= count($data['groupes']['termine']) ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#annule">
                    Annulés <span class="badge bg-danger"><?= count($data['groupes']['annule']) ?></span>
                </a>
            </li>
        </ul>

        <!-- Contenu des onglets -->
        <div class="tab-content">
            
            <!-- Trajets à venir -->
            <div class="tab-pane fade show active" id="prevu">
                <?php if (empty($data['groupes']['prevu'])): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times display-1 text-muted mb-3"></i>
                        <p class="text-muted">Aucun trajet prévu</p>
                        <a href="/covoiturage/creer" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Créer un trajet
                        </a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($data['groupes']['prevu'] as $covoit): ?>
                            <?php include 'app/views/includes/card-mes-covoiturages.php'; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Trajets terminés -->
            <div class="tab-pane fade" id="termine">
                <?php if (empty($data['groupes']['termine'])): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle display-1 text-muted mb-3"></i>
                        <p class="text-muted">Aucun trajet terminé</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($data['groupes']['termine'] as $covoit): ?>
                            <?php include 'app/views/includes/card-mes-covoiturages.php'; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Trajets annulés -->
            <div class="tab-pane fade" id="annule">
                <?php if (empty($data['groupes']['annule'])): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-ban display-1 text-muted mb-3"></i>
                        <p class="text-muted">Aucun trajet annulé</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($data['groupes']['annule'] as $covoit): ?>
                            <?php include 'app/views/includes/card-mes-covoiturages.php'; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</main>

<?php
require_once 'app/views/includes/footer.php';
?>