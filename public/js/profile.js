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

    // ========== ANNULATION DE COVOITURAGE ==========
    
    function openCancelCovoiturageModal(covoiturageId, route, date) {
        document.getElementById('covoiturageIdToCancel').value = covoiturageId;
        document.getElementById('covoiturageRoute').textContent = route;
        document.getElementById('covoiturageDate').textContent = date;
        document.getElementById('cancelCovoiturageModal').style.display = 'flex';
    }
    
    function closeCancelCovoiturageModal() {
        document.getElementById('cancelCovoiturageModal').style.display = 'none';
    }
    
    // Boutons d'annulation de covoiturage
    const cancelCovoiturageButtons = document.querySelectorAll('.cancel-covoiturage-btn');
    cancelCovoiturageButtons.forEach(button => {
        button.addEventListener('click', function() {
            const covoiturageId = this.dataset.covoiturageId;
            const route = this.dataset.covoiturageRoute;
            const date = this.dataset.covoiturageDate;
            openCancelCovoiturageModal(covoiturageId, route, date);
        });
    });
    
    const closeCancelCovoiturageBtn = document.getElementById('closeCancelCovoiturageModal');
    if (closeCancelCovoiturageBtn) {
        closeCancelCovoiturageBtn.addEventListener('click', closeCancelCovoiturageModal);
    }
    
    // ========== SUPPRESSION DE COVOITURAGE ==========
    
    function openDeleteCovoiturageModal(covoiturageId, route) {
        document.getElementById('covoiturageIdToDelete').value = covoiturageId;
        document.getElementById('deleteCovoiturageRoute').textContent = route;
        document.getElementById('deleteCovoiturageModal').style.display = 'flex';
    }
    
    function closeDeleteCovoiturageModal() {
        document.getElementById('deleteCovoiturageModal').style.display = 'none';
    }
    
    // Boutons de suppression de covoiturage
    const deleteCovoiturageButtons = document.querySelectorAll('.delete-covoiturage-btn');
    deleteCovoiturageButtons.forEach(button => {
        button.addEventListener('click', function() {
            const covoiturageId = this.dataset.covoiturageId;
            const route = this.dataset.covoiturageRoute;
            openDeleteCovoiturageModal(covoiturageId, route);
        });
    });
    
    const closeDeleteCovoiturageBtn = document.getElementById('closeDeleteCovoiturageModal');
    if (closeDeleteCovoiturageBtn) {
        closeDeleteCovoiturageBtn.addEventListener('click', closeDeleteCovoiturageModal);
    }
    
    // Fermer les modals en cliquant sur l'overlay
    const cancelCovoiturageModal = document.getElementById('cancelCovoiturageModal');
    if (cancelCovoiturageModal) {
        cancelCovoiturageModal.addEventListener('click', function(e) {
            if (e.target.id === 'cancelCovoiturageModal') {
                closeCancelCovoiturageModal();
            }
        });
    }
    
    const deleteCovoiturageModal = document.getElementById('deleteCovoiturageModal');
    if (deleteCovoiturageModal) {
        deleteCovoiturageModal.addEventListener('click', function(e) {
            if (e.target.id === 'deleteCovoiturageModal') {
                closeDeleteCovoiturageModal();
            }
        });
    }
    
    // Fermer avec Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCancelCovoiturageModal();
            closeDeleteCovoiturageModal();
        }
    });
});