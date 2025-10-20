<?php
/**
 * Vue : Affichage d'une conversation
 * Interface de chat avec messages et formulaire d'envoi
 * 
 * Variables disponibles :
 * - $data['conversation'] : La conversation
 * - $data['conversation_id'] : ID MongoDB
 * - $data['messages'] : Array des messages
 * - $data['other_user'] : L'autre utilisateur
 * - $data['trajet'] : Infos du trajet
 * - $data['is_driver'] : Boolean (true si conducteur)
 * - $data['user_role'] : 'chauffeur' ou 'passager'
 * - $data['current_user_id'] : ID de l'utilisateur connecté
 */
$pageSpecificCss = 'messagerie.css';
require_once 'app/views/includes/head-header.php';
?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- En-tête de la conversation -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <a href="/messagerie" class="btn btn-outline-secondary btn-sm me-3">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                        
                        <!-- Photo de l'autre utilisateur -->
                        <div class="flex-shrink-0 me-3">
                            <?php if ($data['other_user']['photo']): ?>
                                <img src="/<?= htmlspecialchars($data['other_user']['photo']) ?>" 
                                        alt="<?= htmlspecialchars($data['other_user']['pseudo']) ?>"
                                        class="rounded-circle"
                                        style="width: 50px; height: 50px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white"
                                    style="width: 50px; height: 50px;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Infos -->
                        <div class="flex-grow-1">
                            <h5 class="mb-1">
                                <?= htmlspecialchars($data['other_user']['pseudo']) ?>
                                <a href="/utilisateur/<?= $data['other_user']['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-user"></i> Voir le profil
                                </a>
                                <small class="text-muted">
                                    (<?= $data['is_driver'] ? 'Passager' : 'Conducteur' ?>)
                                </small>
                            </h5>
                            <p class="mb-0 text-muted small">
                                <i class="bi bi-geo-alt"></i>
                                <?= htmlspecialchars($data['trajet']['ville_depart']) ?> 
                                <i class="bi bi-arrow-right"></i> 
                                <?= htmlspecialchars($data['trajet']['ville_arrivee']) ?>
                                <span class="ms-2">
                                    <i class="bi bi-calendar"></i>
                                    <?= date('d/m/Y', strtotime($data['trajet']['date_depart'])) ?>
                                </span>
                            </p>
                        </div>
                        
                        <!-- Lien vers le trajet -->
                        <a href="/covoiturage/<?= $data['trajet']['id'] ?>" 
                            class="btn btn-outline-primary btn-sm"
                            >
                            <i class="bi bi-eye"></i> Voir le trajet
                        </a>
                    </div>
                </div>
            </div>

            <!-- Zone des messages -->
            <div class="card mb-3">
                <div class="card-body" style="height: 500px; overflow-y: auto;" id="messages-container">
                    <?php if (empty($data['messages'])): ?>
                        <!-- Aucun message -->
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-chat-text display-4"></i>
                            <p class="mt-3">Aucun message pour le moment.<br>Envoyez le premier message !</p>
                        </div>
                    <?php else: ?>
                        <!-- Liste des messages -->
                        <div id="messages-list">
                            <?php foreach ($data['messages'] as $message): ?>
                                <?php 
                                $isOwnMessage = ($message['sender_id'] === $data['current_user_id']);
                                $alignClass = $isOwnMessage ? 'text-end' : 'text-start';
                                $bgClass = $isOwnMessage ? 'bg-primary text-white' : 'bg-light';
                                ?>
                                
                                <div class="mb-3 <?= $alignClass ?>" data-message-id="<?= $message['id'] ?>">
                                    <div class="d-inline-block" style="max-width: 70%;">
                                        <?php if (!$isOwnMessage): ?>
                                            <!-- Nom de l'expéditeur (si pas nous) -->
                                            <small class="text-muted d-block mb-1">
                                                <?= htmlspecialchars($data['other_user']['pseudo']) ?>
                                            </small>
                                        <?php endif; ?>
                                        
                                        <!-- Bulle du message -->
                                        <div class="p-3 rounded <?= $bgClass ?>" style="word-wrap: break-word;">
                                            <?= nl2br(htmlspecialchars($message['content'])) ?>
                                        </div>
                                        
                                        <!-- Heure -->
                                        <small class="text-muted d-block mt-1">
                                            <?= date('H:i', $message['created_at']) ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Indicateur de chargement -->
                    <div id="loading-indicator" class="text-center text-muted py-3" style="display: none;">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        Chargement des nouveaux messages...
                    </div>
                </div>

                <!-- Formulaire d'envoi -->
                <div class="card-footer">
                    <form id="message-form" class="d-flex gap-2">
                        <input type="hidden" 
                                id="conversation-id" 
                                value="<?= htmlspecialchars($data['conversation_id']) ?>">
                        
                        <textarea class="form-control" 
                                    id="message-input" 
                                    rows="1" 
                                    placeholder="Écrivez votre message..."
                                    maxlength="1000"
                                    required></textarea>
                        
                        <button type="submit" 
                                class="btn btn-primary px-4"
                                id="send-button">
                            <i class="fa-solid fa-share"></i>
                        </button>
                    </form>
                    <small class="text-muted">
                        <span id="char-count">0</span>/1000 caractères
                    </small>
                </div>
            </div>

            <!-- Info expiration -->
            <?php 
            $daysUntilExpiration = ceil(($data['conversation']['expires_at'] - time()) / 86400);
            ?>
            <?php if ($daysUntilExpiration > 0 && $daysUntilExpiration <= 7): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Cette conversation expirera dans <?= $daysUntilExpiration ?> jour<?= $daysUntilExpiration > 1 ? 's' : '' ?>.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>





<!-- Script inline pour initialiser -->
<script>
// Données de la conversation à passer au JavaScript
window.conversationData = {
    conversationId: '<?= $data['conversation_id'] ?>',
    currentUserId: <?= $data['current_user_id'] ?>,
    otherUserPseudo: '<?= htmlspecialchars($data['other_user']['pseudo'], ENT_QUOTES) ?>',
    otherUserPhoto: '<?= $data['other_user']['photo'] ? '/' . htmlspecialchars($data['other_user']['photo'], ENT_QUOTES) : '' ?>'
};

// Auto-scroll vers le bas au chargement
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('messages-container');
    container.scrollTop = container.scrollHeight;
});
</script>

<?php
$pageSpecificJs = 'messagerie.js';
require_once 'app/views/includes/footer.php';
?>