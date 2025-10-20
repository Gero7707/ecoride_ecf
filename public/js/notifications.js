/**
 * Gestion des notifications de messagerie
 * Met à jour le badge de messages non lus dans la navbar
 */

// Fonction pour mettre à jour le compteur de messages non lus
async function updateMessageBadge() {
    try {
        const response = await fetch('/messagerie/unread-count');
        const data = await response.json();
        
        if (data.success) {
            const badge = document.getElementById('notification-badge');
            if (badge) {
                const oldCount = parseInt(badge.textContent) || 0;
                const newCount = data.unread_count;
                
                if (newCount > 0) {
                    // Afficher le badge avec le nombre
                    badge.textContent = newCount > 99 ? '99+' : newCount;
                    badge.style.display = 'flex';
                    
                    // Animation si nouveau message depuis la dernière vérification
                    if (newCount > oldCount && oldCount > 0) {
                        badge.classList.add('new-message');
                        setTimeout(() => badge.classList.remove('new-message'), 2400);
                    }
                } else {
                    // Cacher le badge si aucun message non lu
                    badge.style.display = 'none';
                }
            }
        }
    } catch (error) {
        console.error('Erreur badge messagerie:', error);
    }
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si l'utilisateur est connecté (le badge existe)
    const badge = document.getElementById('notification-badge');
    
    if (badge) {
        // Mettre à jour immédiatement
        updateMessageBadge();
        
        // Puis mettre à jour toutes les 30 secondes
        setInterval(updateMessageBadge, 30000);
    }
});