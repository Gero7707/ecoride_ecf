<?php
/**
 * Vue : Liste des conversations
 * Affiche toutes les conversations de l'utilisateur
 * 
 * Variables disponibles (passées par le Controller) :
 * - $data['conversations'] : Array des conversations enrichies
 * - $data['totalUnread'] : Nombre total de messages non lus
 */

// Inclure le header (adapte le chemin selon ton projet)
$pageSpecificCss = 'messagerie.css';
require_once 'app/views/includes/head-header.php';
?>

<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="bi bi-chat-dots"></i> Messagerie
                    <?php if ($data['totalUnread'] > 0): ?>
                        <span class="badge bg-danger"><?= $data['totalUnread'] ?></span>
                    <?php endif; ?>
                </h2>
                <a href="/covoiturages" class="btn btn-outline-primary">
                    <i class="bi bi-search"></i> Trouver un trajet
                </a>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Liste des conversations -->
            <?php if (empty($data['conversations'])): ?>
                <!-- Aucune conversation -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h4 class="mt-3">Aucune conversation</h4>
                        <p class="text-muted">
                            Vous n'avez pas encore de conversations.<br>
                            Réservez un trajet pour commencer à échanger avec le conducteur !
                        </p>
                        <a href="/covoiturages" class="btn btn-primary mt-3">
                            <i class="bi bi-search"></i> Rechercher un trajet
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Liste des conversations -->
                <div class="list-group">
                    <?php foreach ($data['conversations'] as $conv): ?>
                        <?php
                        $conversation = $conv['conversation'];
                        $otherUser = $conv['other_user'];
                        $trajet = $conv['trajet'];
                        $isDriver = $conv['is_driver'];
                        $unreadCount = $conv['unread_count'];
                        ?>
                        
                        <a href="/messagerie/conversation/<?= $conversation['id'] ?>" 
                           class="list-group-item list-group-item-action <?= $unreadCount > 0 ? 'bg-light' : '' ?>">
                            <div class="d-flex w-100">
                                <!-- Photo de profil -->
                                <div class="flex-shrink-0 me-3">
                                    <?php if ($otherUser['photo']): ?>
                                        <img src="/<?= htmlspecialchars($otherUser['photo']) ?>" 
                                             alt="<?= htmlspecialchars($otherUser['pseudo']) ?>"
                                             class="rounded-circle"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white"
                                             style="width: 50px; height: 50px;">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Contenu -->
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <!-- Nom de l'autre utilisateur -->
                                            <h6 class="mb-1 <?= $unreadCount > 0 ? 'fw-bold' : '' ?>">
                                                <?= htmlspecialchars($otherUser['pseudo']) ?>
                                                <small class="text-muted">
                                                    (<?= $isDriver ? 'Passager' : 'Conducteur' ?>)
                                                </small>
                                            </h6>
                                            
                                            <!-- Infos du trajet -->
                                            <p class="mb-1 text-muted small">
                                                <i class="bi bi-geo-alt"></i>
                                                <?= htmlspecialchars($trajet['ville_depart']) ?> 
                                                <i class="bi bi-arrow-right"></i> 
                                                <?= htmlspecialchars($trajet['ville_arrivee']) ?>
                                            </p>
                                            
                                            <p class="mb-0 text-muted small">
                                                <i class="bi bi-calendar"></i>
                                                <?= date('d/m/Y', strtotime($trajet['date_depart'])) ?> à 
                                                <?= date('H:i', strtotime($trajet['heure_depart'])) ?>
                                            </p>
                                        </div>
                                        
                                        <!-- Badge non lus + date -->
                                        <div class="text-end">
                                            <small class="text-muted d-block">
                                                <?= $conv['last_message_date'] ?>
                                            </small>
                                            <?php if ($unreadCount > 0): ?>
                                                <span class="badge bg-danger mt-1">
                                                    <?= $unreadCount ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<?php
// Inclure le footer (adapte le chemin selon ton projet)
require_once 'app/views/includes/footer.php';
?>