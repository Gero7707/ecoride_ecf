document.addEventListener('DOMContentLoaded', function() {
    
    // ========== ANNULATION DE RÉSERVATION ==========
    
    function openCancelModal(reservationId, route, date) {
        document.getElementById('reservationIdToCancel').value = reservationId;
        document.getElementById('reservationRoute').textContent = route;
        document.getElementById('reservationDate').textContent = date;
        document.getElementById('cancelReservationModal').style.display = 'flex';
    }
    
    function closeCancelModal() {
        document.getElementById('cancelReservationModal').style.display = 'none';
    }
    
    // Boutons d'annulation
    const cancelButtons = document.querySelectorAll('.cancel-reservation-btn');
    cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
            const reservationId = this.dataset.reservationId;
            const route = this.dataset.reservationRoute;
            const date = this.dataset.reservationDate;
            openCancelModal(reservationId, route, date);
        });
    });
    
    // Bouton fermer modal
    const closeCancelBtn = document.getElementById('closeCancelModal');
    if (closeCancelBtn) {
        closeCancelBtn.addEventListener('click', closeCancelModal);
    }
    
    // Fermer en cliquant sur l'overlay
    const cancelModal = document.getElementById('cancelReservationModal');
    if (cancelModal) {
        cancelModal.addEventListener('click', function(e) {
            if (e.target.id === 'cancelReservationModal') {
                closeCancelModal();
            }
        });
    }
    
    // Fermer avec Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCancelModal();
        }
    });


    // ========== SUPPRESSION DE RÉSERVATION ==========
    
    function openDeleteReservationModal(reservationId, route) {
        document.getElementById('reservationIdToDelete').value = reservationId;
        document.getElementById('deleteReservationRoute').textContent = route;
        document.getElementById('deleteReservationModal').style.display = 'flex';
    }
    
    function closeDeleteReservationModal() {
        document.getElementById('deleteReservationModal').style.display = 'none';
    }
    
    // Boutons de suppression
    const deleteReservationButtons = document.querySelectorAll('.delete-reservation-btn');
    deleteReservationButtons.forEach(button => {
        button.addEventListener('click', function() {
            const reservationId = this.dataset.reservationId;
            const route = this.dataset.reservationRoute;
            openDeleteReservationModal(reservationId, route);
        });
    });
    
    // Bouton fermer modal
    const closeDeleteReservationBtn = document.getElementById('closeDeleteReservationModal');
    if (closeDeleteReservationBtn) {
        closeDeleteReservationBtn.addEventListener('click', closeDeleteReservationModal);
    }
    
    // Fermer en cliquant sur l'overlay
    const deleteReservationModal = document.getElementById('deleteReservationModal');
    if (deleteReservationModal) {
        deleteReservationModal.addEventListener('click', function(e) {
            if (e.target.id === 'deleteReservationModal') {
                closeDeleteReservationModal();
            }
        });
    }
    
    // Fermer avec Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteReservationModal();
        }
    });
});