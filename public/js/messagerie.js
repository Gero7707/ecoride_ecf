/**
 * JavaScript pour la messagerie en temps réel
 * Gère l'envoi et la réception automatique des messages
 */

// Variables globales
let pollingInterval = null;
let lastMessageTimestamp = null;
let isPolling = false;

/**
 * Initialisation au chargement de la page
 */
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier qu'on est bien sur la page de conversation
    if (!window.conversationData) {
        return;
    }

    console.log('💬 Messagerie initialisée');

    // Récupérer les éléments DOM
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const charCount = document.getElementById('char-count');
    const messagesContainer = document.getElementById('messages-container');

    // Récupérer le timestamp du dernier message
    initLastMessageTimestamp();

    // Gérer le formulaire d'envoi
    if (messageForm) {
        messageForm.addEventListener('submit', handleSendMessage);
    }

    // Gérer le compteur de caractères
    if (messageInput && charCount) {
        messageInput.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });

        // Auto-resize du textarea
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Envoyer avec Ctrl+Enter
        messageInput.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                messageForm.dispatchEvent(new Event('submit'));
            }
        });
    }

    // Démarrer le polling (vérification des nouveaux messages)
    startPolling();

    // Marquer les messages comme lus
    markMessagesAsRead();

    // Scroll automatique vers le bas
    scrollToBottom();
});

/**
 * Initialiser le timestamp du dernier message
 */
function initLastMessageTimestamp() {
    const messages = document.querySelectorAll('[data-message-id]');
    if (messages.length > 0) {
        // Le dernier message est le plus récent
        const lastMessage = messages[messages.length - 1];
        const timestamp = lastMessage.dataset.timestamp;
        if (timestamp) {
            lastMessageTimestamp = parseInt(timestamp);
        } else {
            // Si pas de timestamp, utiliser l'heure actuelle
            lastMessageTimestamp = Math.floor(Date.now() / 1000);
        }
    } else {
        // Aucun message, utiliser l'heure actuelle
        lastMessageTimestamp = Math.floor(Date.now() / 1000);
    }
}

/**
 * Gérer l'envoi d'un message
 */
async function handleSendMessage(e) {
    e.preventDefault();

    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const content = messageInput.value.trim();

    // Vérifier que le message n'est pas vide
    if (!content) {
        return;
    }

    // Désactiver le formulaire pendant l'envoi
    messageInput.disabled = true;
    sendButton.disabled = true;
    sendButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    try {
        // Envoyer le message à l'API
        const response = await fetch('/api/messages/send.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                conversation_id: window.conversationData.conversationId,
                content: content
            })
        });

        const data = await response.json();

        if (data.success) {
            // Afficher le message immédiatement
            appendMessage(data.message, true);

            // Vider le champ
            messageInput.value = '';
            messageInput.style.height = 'auto';
            document.getElementById('char-count').textContent = '0';

            // Scroll vers le bas
            scrollToBottom();

            // Mettre à jour le timestamp
            lastMessageTimestamp = data.message.created_at;

        } else {
            // Afficher l'erreur
            alert('Erreur : ' + (data.error || 'Impossible d\'envoyer le message'));
        }

    } catch (error) {
        console.error('Erreur lors de l\'envoi :', error);
        alert('Erreur de connexion. Veuillez réessayer.');
    } finally {
        // Réactiver le formulaire
        messageInput.disabled = false;
        sendButton.disabled = false;
        sendButton.innerHTML = '<i class="fa-solid fa-share"></i>';
        messageInput.focus();
    }
}

/**
 * Ajouter un message à la liste
 */
function appendMessage(message, isOwnMessage) {
    const messagesList = document.getElementById('messages-list');
    const emptyMessage = document.querySelector('#messages-container .text-center');

    // Si c'est le premier message, supprimer le message "Aucun message"
    if (emptyMessage) {
        emptyMessage.remove();
    }

    // Créer le container du message s'il n'existe pas
    if (!messagesList) {
        const messagesContainer = document.getElementById('messages-container');
        const newList = document.createElement('div');
        newList.id = 'messages-list';
        messagesContainer.appendChild(newList);
    }

    const alignClass = isOwnMessage ? 'text-end' : 'text-start';
    const bgClass = isOwnMessage ? 'bg-primary text-white' : 'bg-light';

    const messageHtml = `
        <div class="mb-3 ${alignClass}" data-message-id="${message.id}" data-timestamp="${message.created_at}">
            <div class="d-inline-block" style="max-width: 70%;">
                ${!isOwnMessage ? `
                    <small class="text-muted d-block mb-1">
                        ${escapeHtml(window.conversationData.otherUserPseudo)}
                    </small>
                ` : ''}
                <div class="p-3 rounded ${bgClass}" style="word-wrap: break-word;">
                    ${escapeHtml(message.content).replace(/\n/g, '<br>')}
                </div>
                <small class="text-muted d-block mt-1">
                    ${message.created_at_formatted || formatTime(message.created_at)}
                </small>
            </div>
        </div>
    `;

    const list = document.getElementById('messages-list');
    list.insertAdjacentHTML('beforeend', messageHtml);
}

/**
 * Démarrer le polling (vérification régulière des nouveaux messages)
 */
function startPolling() {
    console.log('🔄 Démarrage du polling...');

    // Vérifier immédiatement
    checkNewMessages();

    // Puis vérifier toutes les 3 secondes
    pollingInterval = setInterval(checkNewMessages, 3000);

    // Arrêter le polling si l'utilisateur quitte la page
    window.addEventListener('beforeunload', function() {
        stopPolling();
    });
}

/**
 * Arrêter le polling
 */
function stopPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
        console.log('⏸️ Polling arrêté');
    }
}

/**
 * Vérifier s'il y a de nouveaux messages
 */
async function checkNewMessages() {
    // Éviter les appels simultanés
    if (isPolling) {
        return;
    }

    isPolling = true;

    try {
        // Construire l'URL avec le timestamp
        let url = `/api/messages/get_messages.php?conversation_id=${window.conversationData.conversationId}`;
        
        if (lastMessageTimestamp) {
            url += `&since=${lastMessageTimestamp}`;
        }

        const response = await fetch(url);
        const data = await response.json();

        if (data.success && data.messages.length > 0) {
            console.log(`📨 ${data.messages.length} nouveau(x) message(s)`);

            // Ajouter chaque nouveau message
            data.messages.forEach(message => {
                // Vérifier si le message n'existe pas déjà
                const exists = document.querySelector(`[data-message-id="${message.id}"]`);
                if (!exists) {
                    appendMessage(message, message.is_own_message);
                    
                    // Mettre à jour le timestamp
                    if (message.created_at > lastMessageTimestamp) {
                        lastMessageTimestamp = message.created_at;
                    }
                }
            });

            // Scroll vers le bas
            scrollToBottom(true); // smooth scroll

            // Marquer comme lus
            markMessagesAsRead();
        }

    } catch (error) {
        console.error('Erreur lors de la vérification des messages :', error);
    } finally {
        isPolling = false;
    }
}

/**
 * Marquer les messages comme lus
 */
async function markMessagesAsRead() {
    try {
        await fetch('/api/messages/mark_as_read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                conversation_id: window.conversationData.conversationId
            })
        });
    } catch (error) {
        console.error('Erreur lors du marquage comme lu :', error);
    }
}

/**
 * Scroll automatique vers le bas
 */
function scrollToBottom(smooth = false) {
    const container = document.getElementById('messages-container');
    if (container) {
        if (smooth) {
            container.scrollTo({
                top: container.scrollHeight,
                behavior: 'smooth'
            });
        } else {
            container.scrollTop = container.scrollHeight;
        }
    }
}

/**
 * Formater un timestamp en heure (HH:MM)
 */
function formatTime(timestamp) {
    const date = new Date(timestamp * 1000);
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${hours}:${minutes}`;
}

/**
 * Échapper le HTML pour éviter les failles XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Mettre à jour le badge de notification dans le header (optionnel)
 * À appeler depuis une autre page pour afficher le nombre de messages non lus
 */
async function updateNotificationBadge() {
    try {
        const response = await fetch('/messagerie/unread-count');
        const data = await response.json();
        
        if (data.success) {
            const badge = document.getElementById('notification-badge');
            if (badge) {
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count;
                    badge.style.display = 'inline';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
    } catch (error) {
        console.error('Erreur mise à jour badge :', error);
    }
}

// Exporter pour utilisation dans d'autres pages
window.MessagerieUtils = {
    updateNotificationBadge: updateNotificationBadge
};