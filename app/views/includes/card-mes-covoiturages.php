<div class="col-md-6 col-lg-4 mb-4">
    <div class="card h-100 <?= $covoit['statut'] === 'annule' ? 'border-danger' : '' ?>">
        <!-- Badge statut -->
        <div class="position-absolute top-0 end-0 m-2">
            <?php
            $badges = [
                'prevu' => 'bg-success',
                'en_cours' => 'bg-warning',
                'termine' => 'bg-secondary',
                'annule' => 'bg-danger'
            ];
            $labels = [
                'prevu' => 'Prévu',
                'en_cours' => 'En cours',
                'termine' => 'Terminé',
                'annule' => 'Annulé'
            ];
            ?>
            <span class="badge <?= $badges[$covoit['statut']] ?>">
                <?= $labels[$covoit['statut']] ?>
            </span>
        </div>

        <div class="card-body">
            <!-- Route -->
            <h5 class="card-title">
                <i class="fas fa-map-marker-alt text-success"></i>
                <?= htmlspecialchars($covoit['ville_depart']) ?>
                <i class="fas fa-arrow-right mx-2"></i>
                <i class="fas fa-map-marker-alt text-danger"></i>
                <?= htmlspecialchars($covoit['ville_arrivee']) ?>
            </h5>

            <!-- Date et heure -->
            <p class="text-muted mb-2">
                <i class="fas fa-calendar"></i>
                <?= date('d/m/Y', strtotime($covoit['date_depart'])) ?>
                à <?= date('H:i', strtotime($covoit['heure_depart'])) ?>
            </p>

            <!-- Véhicule -->
            <?php if (!empty($covoit['marque'])): ?>
                <p class="text-muted small mb-2">
                    <i class="fas fa-car"></i>
                    <?= htmlspecialchars($covoit['marque'] . ' ' . $covoit['modele']) ?>
                </p>
            <?php endif; ?>

            <!-- Prix et places -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fs-4 text-success fw-bold">
                    <?= number_format($covoit['prix'], 2) ?>€
                </span>
                <span class="badge bg-primary">
                    <?= $covoit['places_disponibles'] ?> place<?= $covoit['places_disponibles'] > 1 ? 's' : '' ?>
                </span>
            </div>

            <!-- Statistiques réservations -->
            <?php if ($covoit['nb_reservations'] > 0): ?>
                <div class="alert alert-info small mb-3">
                    <i class="fas fa-users"></i>
                    <strong><?= $covoit['nb_reservations'] ?></strong> réservation<?= $covoit['nb_reservations'] > 1 ? 's' : '' ?>
                    
                    <?php if ($covoit['nb_en_attente'] > 0): ?>
                        <br><i class="fas fa-hourglass-half"></i>
                        <?= $covoit['nb_en_attente'] ?> en attente
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Type de réservation -->
            <?php if ($covoit['confirmation_requise'] == 1): ?>
                <p class="text-muted small mb-3">
                    <i class="fas fa-check-circle"></i> Confirmation manuelle
                </p>
            <?php else: ?>
                <p class="text-muted small mb-3">
                    <i class="fas fa-bolt"></i> Réservation instantanée
                </p>
            <?php endif; ?>
        </div>

        <!-- Actions -->
        <div class="card-footer bg-white">
            <div class="d-grid gap-2">
                <a href="/covoiturage/<?= $covoit['id'] ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye"></i> Voir le détail
                </a>
                
                <?php if ($covoit['statut'] === 'prevu'): ?>
                    <a href="/covoiturage/<?= $covoit['id'] ?>/passagers" class="btn btn-primary btn-sm">
                        <i class="fas fa-users"></i> Voir les passagers
                        <?php if ($covoit['nb_en_attente'] > 0): ?>
                            <span class="badge bg-danger"><?= $covoit['nb_en_attente'] ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>