<?php
// app/models/EmailService.php
// Version adaptée pour PDO

class EmailService {
    private $pdo; // Connexion PDO
    private $mongodb;
    public function __construct($pdo, $mongodb = null) {
        $this->pdo = $pdo;
        $this->mongodb = $mongodb;
    }
    
    /**
     * Envoi d'email avec la fonction mail() native PHP
     */
    private function envoyerEmail($destinataire, $sujet, $corpsHtml, $typeNotification = 'reservation') {
        // Configuration des headers pour HTML
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: EcoRide <noreply@ecoride.fr>" . "\r\n";
        $headers .= "Reply-To: support@ecoride.fr" . "\r\n";
        
        // Envoi de l'email
        $succes = mail($destinataire, $sujet, $corpsHtml, $headers);
        
        // Enregistrement dans les logs
        $this->loggerEmail($destinataire, $sujet, $typeNotification, $succes);
        
        return $succes;
    }
    
    /**
     * Enregistre l'historique des emails envoyés
     */
    private function loggerEmail($destinataire, $sujet, $typeNotification, $succes, $erreur = null) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO email_logs (destinataire, sujet, type_notification, statut, message_erreur, date_envoi) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $statut = $succes ? 'envoye' : 'echec';
            $stmt->execute([$destinataire, $sujet, $typeNotification, $statut, $erreur]);
        } catch (PDOException $e) {
            error_log("Erreur log email : " . $e->getMessage());
        }
    }
    
    /**
     * Récupère l'email d'un utilisateur depuis son ID
     */
    private function getEmailUtilisateur($utilisateurId) {
        $stmt = $this->pdo->prepare("SELECT email, pseudo FROM utilisateur WHERE id = ?");
        $stmt->execute([$utilisateurId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * NOTIFICATION 1 : Nouvelle réservation créée
     */
    public function notifierNouvelleReservation($reservationId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                r.id as reservation_id,
                r.passager_id,
                r.statut as statut_reservation,
                c.id as covoiturage_id,
                c.chauffeur_id,
                c.ville_depart,
                c.ville_arrivee,
                c.date_depart,
                c.heure_depart,
                c.heure_arrivee,
                c.prix,
                c.places_disponibles,
                c.confirmation_requise,
                u_passager.email as email_passager,
                u_passager.pseudo as pseudo_passager,
                u_chauffeur.email as email_chauffeur,
                u_chauffeur.pseudo as pseudo_chauffeur,
                u_chauffeur.telephone as tel_chauffeur,
                v.marque,
                v.modele,
                v.couleur
            FROM reservation r
            INNER JOIN covoiturage c ON r.covoiturage_id = c.id
            INNER JOIN utilisateur u_passager ON r.passager_id = u_passager.id
            INNER JOIN utilisateur u_chauffeur ON c.chauffeur_id = u_chauffeur.id
            INNER JOIN vehicule v ON c.vehicule_id = v.id
            WHERE r.id = ?
        ");
        $stmt->execute([$reservationId]);
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$reservation) return false;
        
        // Email au chauffeur
        $sujetChauffeur = $reservation['confirmation_requise'] 
            ? "Nouvelle demande de réservation à confirmer"
            : "Nouvelle réservation pour votre trajet";
        $corpsChauffeur = $this->templateReservationChauffeur($reservation);
        $this->envoyerEmail($reservation['email_chauffeur'], $sujetChauffeur, $corpsChauffeur, 'reservation');
        
        // Email au passager
        $sujetPassager = "Votre demande de réservation est enregistrée";
        $corpsPassager = $this->templateReservationPassager($reservation);
        $this->envoyerEmail($reservation['email_passager'], $sujetPassager, $corpsPassager, 'reservation');
        
        return true;
    }
    
    /**
     * NOTIFICATION 2 : Réservation confirmée
     */
    public function notifierReservationConfirmee($reservationId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                r.id,
                c.ville_depart,
                c.ville_arrivee,
                c.date_depart,
                c.heure_depart,
                c.prix,
                u_passager.email as email_passager,
                u_passager.pseudo as pseudo_passager,
                u_chauffeur.pseudo as pseudo_chauffeur,
                u_chauffeur.telephone as tel_chauffeur,
                v.marque,
                v.modele
            FROM reservation r
            INNER JOIN covoiturage c ON r.covoiturage_id = c.id
            INNER JOIN utilisateur u_passager ON r.passager_id = u_passager.id
            INNER JOIN utilisateur u_chauffeur ON c.chauffeur_id = u_chauffeur.id
            INNER JOIN vehicule v ON c.vehicule_id = v.id
            WHERE r.id = ?
        ");
        $stmt->execute([$reservationId]);
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$reservation) return false;
        
        $sujet = "🎉 Votre réservation a été confirmée !";
        $corps = $this->templateReservationConfirmee($reservation);
        
        return $this->envoyerEmail($reservation['email_passager'], $sujet, $corps, 'confirmation');
    }
    
    /**
     * NOTIFICATION 3 : Réservation refusée
     */
    public function notifierReservationRefusee($reservationId, $motif = null) {
        $stmt = $this->pdo->prepare("
            SELECT 
                c.ville_depart,
                c.ville_arrivee,
                c.date_depart,
                u_passager.email as email_passager,
                u_passager.pseudo as pseudo_passager
            FROM reservation r
            INNER JOIN covoiturage c ON r.covoiturage_id = c.id
            INNER JOIN utilisateur u_passager ON r.passager_id = u_passager.id
            WHERE r.id = ?
        ");
        $stmt->execute([$reservationId]);
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$reservation) return false;
        
        $sujet = "Information concernant votre réservation";
        $corps = $this->templateReservationRefusee($reservation, $motif);
        
        return $this->envoyerEmail($reservation['email_passager'], $sujet, $corps, 'annulation');
    }
    
    /**
     * NOTIFICATION 4 : Covoiturage annulé
     */
    public function notifierAnnulationCovoiturage($covoiturageId, $motif = null) {
        $stmt = $this->pdo->prepare("
            SELECT 
                u.email,
                u.pseudo,
                c.ville_depart,
                c.ville_arrivee,
                c.date_depart,
                c.heure_depart
            FROM reservation r
            INNER JOIN utilisateur u ON r.passager_id = u.id
            INNER JOIN covoiturage c ON r.covoiturage_id = c.id
            WHERE r.covoiturage_id = ? 
            AND r.statut IN ('confirmee', 'en_attente')
        ");
        $stmt->execute([$covoiturageId]);
        $passagers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $compteur = 0;
        foreach ($passagers as $passager) {
            $sujet = "⚠️ Annulation de votre covoiturage";
            $corps = $this->templateAnnulationCovoiturage($passager, $motif);
            
            if ($this->envoyerEmail($passager['email'], $sujet, $corps, 'annulation')) {
                $compteur++;
            }
        }
        
        return $compteur;
    }
    
    // ==================== TEMPLATES HTML ====================
    
    private function getTemplateBase($contenu) {
        $baseUrl = 'http://localhost/ecoride'; // TODO: Changer en production
        
        return "
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f4f4f4; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 20px auto; background: white; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { padding: 30px; }
                .card { background: #f9f9f9; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #667eea; }
                .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
                .info-row:last-child { border-bottom: none; }
                .info-label { font-weight: bold; color: #667eea; }
                .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .button:hover { background: #5568d3; }
                .footer { text-align: center; color: #999; font-size: 12px; padding: 20px; background: #f4f4f4; }
                .footer a { color: #667eea; text-decoration: none; }
            </style>
        </head>
        <body>
            <div class='container'>
                $contenu
                <div class='footer'>
                    <p>Cet email a été envoyé automatiquement par EcoRide</p>
                    <p>Pour toute question : <a href='mailto:support@ecoride.fr'>support@ecoride.fr</a></p>
                    <p><a href='$baseUrl/desinscription'>Se désinscrire des notifications</a></p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    private function templateReservationChauffeur($data) {
        $baseUrl = 'http://localhost/ecoride';
        $messageConfirmation = $data['confirmation_requise'] 
            ? "<p style='background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>
                   <strong>⚠️ Action requise :</strong> Cette réservation nécessite votre confirmation.
               </p>
               <a href='$baseUrl/covoiturage/{$data['covoiturage_id']}/passagers' class='button'>Gérer les réservations</a>"
            : "<p style='background: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>
                   ✅ Cette réservation a été automatiquement confirmée.
               </p>";
        
        $contenu = "
            <div class='header'>
                <h1>🚗 Nouvelle Réservation</h1>
            </div>
            <div class='content'>
                <p>Bonjour <strong>{$data['pseudo_chauffeur']}</strong>,</p>
                <p><strong>{$data['pseudo_passager']}</strong> vient de réserver une place pour votre trajet !</p>
                
                <div class='card'>
                    <h3 style='margin-top: 0; color: #667eea;'>📍 Détails du trajet</h3>
                    <div class='info-row'>
                        <span class='info-label'>Départ</span>
                        <span>{$data['ville_depart']}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Arrivée</span>
                        <span>{$data['ville_arrivee']}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Date</span>
                        <span>" . date('d/m/Y', strtotime($data['date_depart'])) . "</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Heure</span>
                        <span>" . date('H:i', strtotime($data['heure_depart'])) . "</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Prix</span>
                        <span><strong>{$data['prix']} €</strong></span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Places restantes</span>
                        <span><strong>{$data['places_disponibles']}</strong></span>
                    </div>
                </div>
                
                $messageConfirmation
            </div>
        ";
        
        return $this->getTemplateBase($contenu);
    }
    
    private function templateReservationPassager($data) {
        $baseUrl = 'http://localhost/ecoride';
        $messageAttente = $data['confirmation_requise']
            ? "<p style='background: #fff3cd; padding: 15px; border-radius: 5px;'>
                   ⏳ Votre réservation est <strong>en attente de confirmation</strong> par le chauffeur. 
                   Vous recevrez un email dès validation.
               </p>"
            : "<p style='background: #d4edda; padding: 15px; border-radius: 5px;'>
                   🎉 Votre réservation est <strong>automatiquement confirmée</strong> !
               </p>";
        
        $contenu = "
            <div class='header'>
                <h1>✅ Réservation Enregistrée</h1>
            </div>
            <div class='content'>
                <p>Bonjour <strong>{$data['pseudo_passager']}</strong>,</p>
                <p>Votre demande de réservation a bien été enregistrée.</p>
                
                $messageAttente
                
                <div class='card'>
                    <h3 style='margin-top: 0; color: #667eea;'>📍 Votre trajet</h3>
                    <div class='info-row'>
                        <span class='info-label'>De</span>
                        <span>{$data['ville_depart']}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>À</span>
                        <span>{$data['ville_arrivee']}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Le</span>
                        <span>" . date('d/m/Y à H:i', strtotime($data['date_depart'] . ' ' . $data['heure_depart'])) . "</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Chauffeur</span>
                        <span>{$data['pseudo_chauffeur']}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Véhicule</span>
                        <span>{$data['couleur']} {$data['marque']} {$data['modele']}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Prix</span>
                        <span><strong>{$data['prix']} €</strong></span>
                    </div>
                </div>
                
                <a href='$baseUrl/profil' class='button'>Voir mes réservations</a>
            </div>
        ";
        
        return $this->getTemplateBase($contenu);
    }
    
    private function templateReservationConfirmee($data) {
        $baseUrl = 'http://localhost/ecoride';
        
        $contenu = "
            <div class='header'>
                <h1>🎉 Réservation Confirmée !</h1>
            </div>
            <div class='content'>
                <p>Bonjour <strong>{$data['pseudo_passager']}</strong>,</p>
                <p>Bonne nouvelle ! <strong>{$data['pseudo_chauffeur']}</strong> a confirmé votre réservation.</p>
                
                <div class='card'>
                    <h3 style='margin-top: 0; color: #28a745;'>✅ Rendez-vous confirmé</h3>
                    <div class='info-row'>
                        <span class='info-label'>Trajet</span>
                        <span>{$data['ville_depart']} → {$data['ville_arrivee']}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Date</span>
                        <span><strong>" . date('d/m/Y à H:i', strtotime($data['date_depart'] . ' ' . $data['heure_depart'])) . "</strong></span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Chauffeur</span>
                        <span>{$data['pseudo_chauffeur']}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Contact</span>
                        <span>{$data['tel_chauffeur']}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Véhicule</span>
                        <span>{$data['marque']} {$data['modele']}</span>
                    </div>
                </div>
                
                <p>💡 <strong>Conseil :</strong> Pensez à échanger avec votre chauffeur pour convenir du point de rencontre exact.</p>
                
                <a href='$baseUrl/profil' class='button'>Accéder à mes réservations</a>
            </div>
        ";
        
        return $this->getTemplateBase($contenu);
    }
    
    private function templateReservationRefusee($data, $motif) {
        $baseUrl = 'http://localhost/ecoride';
        $messageMotif = $motif 
            ? "<p style='background: #f8d7da; padding: 15px; border-radius: 5px;'><strong>Motif :</strong> $motif</p>" 
            : "";
        
        $contenu = "
            <div class='header'>
                <h1>ℹ️ Information Réservation</h1>
            </div>
            <div class='content'>
                <p>Bonjour <strong>{$data['pseudo_passager']}</strong>,</p>
                <p>Malheureusement, votre demande de réservation pour le trajet <strong>{$data['ville_depart']} → {$data['ville_arrivee']}</strong> 
                   le " . date('d/m/Y', strtotime($data['date_depart'])) . " n'a pas pu être confirmée.</p>
                
                $messageMotif
                
                <p>Ne vous inquiétez pas, de nombreux autres trajets sont disponibles !</p>
                
                <a href='$baseUrl/covoiturages' class='button'>Rechercher un autre trajet</a>
            </div>
        ";
        
        return $this->getTemplateBase($contenu);
    }
    
    private function templateAnnulationCovoiturage($data, $motif) {
        $baseUrl = 'http://localhost/ecoride';
        $messageMotif = $motif 
            ? "<p style='background: #f8d7da; padding: 15px; border-radius: 5px;'><strong>Raison :</strong> $motif</p>" 
            : "";
        
        $contenu = "
            <div class='header' style='background: #dc3545;'>
                <h1>⚠️ Annulation de Covoiturage</h1>
            </div>
            <div class='content'>
                <p>Bonjour <strong>{$data['pseudo']}</strong>,</p>
                <p>Nous sommes désolés de vous informer que le covoiturage suivant a été annulé par le chauffeur :</p>
                
                <div class='card'>
                    <div class='info-row'>
                        <span class='info-label'>Trajet</span>
                        <span>{$data['ville_depart']} → {$data['ville_arrivee']}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Date</span>
                        <span>" . date('d/m/Y à H:i', strtotime($data['date_depart'] . ' ' . $data['heure_depart'])) . "</span>
                    </div>
                </div>
                
                $messageMotif
                
                <p style='background: #d4edda; padding: 15px; border-radius: 5px;'>
                    ✅ Vos crédits vous ont été automatiquement recrédités.
                </p>
                
                <a href='$baseUrl/covoiturages' class='button'>Trouver un autre trajet</a>
            </div>
        ";
        
        return $this->getTemplateBase($contenu);
    }

    /**
     * NOTIFICATION 5 : Nouveau message reçu (MongoDB)
     */
    public function notifierNouveauMessage($messageId) {
        // Vérifier que MongoDB est configuré
        if (!$this->mongodb) {
            error_log("MongoDB non configuré pour les notifications");
            return false;
        }
        
        try {
            // Récupération du message depuis MongoDB
            $collection = $this->mongodb->messages;
            $message = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($messageId)]);
            
            if (!$message) return false;
            
            // Récupération des emails depuis MySQL/PostgreSQL
            $destinataire = $this->getEmailUtilisateur($message['destinataire_id']);
            $expediteur = $this->getEmailUtilisateur($message['expediteur_id']);
            
            if (!$destinataire) return false;
            
            $sujet = "💬 Nouveau message de " . $expediteur['pseudo'];
            $corps = $this->templateNouveauMessage($expediteur, $message);
            
            return $this->envoyerEmail($destinataire['email'], $sujet, $corps, 'message');
        } catch (Exception $e) {
            error_log("Erreur notification message : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * NOTIFICATION 6 : Rappel de trajet (24h avant le départ)
     * À exécuter via un CRON job quotidien
     */
    public function envoyerRappelsTrajet() {
        try {
            // Récupère les covoiturages dans les prochaines 24-48h
            $stmt = $this->pdo->prepare("
                SELECT DISTINCT
                    r.id as reservation_id,
                    c.id as covoiturage_id,
                    c.ville_depart,
                    c.ville_arrivee,
                    c.date_depart,
                    c.heure_depart,
                    u_passager.email as email_passager,
                    u_passager.pseudo as pseudo_passager,
                    u_chauffeur.pseudo as pseudo_chauffeur,
                    u_chauffeur.telephone as tel_chauffeur,
                    'passager' as role
                FROM reservation r
                INNER JOIN covoiturage c ON r.covoiturage_id = c.id
                INNER JOIN utilisateur u_passager ON r.passager_id = u_passager.id
                INNER JOIN utilisateur u_chauffeur ON c.chauffeur_id = u_chauffeur.id
                WHERE r.statut = 'confirmee'
                AND c.statut = 'prevu'
                AND DATE(c.date_depart) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
                
                UNION
                
                SELECT DISTINCT
                    NULL as reservation_id,
                    c.id as covoiturage_id,
                    c.ville_depart,
                    c.ville_arrivee,
                    c.date_depart,
                    c.heure_depart,
                    u_chauffeur.email as email_passager,
                    u_chauffeur.pseudo as pseudo_passager,
                    NULL as pseudo_chauffeur,
                    NULL as tel_chauffeur,
                    'chauffeur' as role
                FROM covoiturage c
                INNER JOIN utilisateur u_chauffeur ON c.chauffeur_id = u_chauffeur.id
                WHERE c.statut = 'prevu'
                AND DATE(c.date_depart) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
            ");
            $stmt->execute();
            $trajets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $compteur = 0;
            foreach ($trajets as $trajet) {
                $sujet = "🚗 Rappel : Votre trajet est demain !";
                $corps = $this->templateRappelTrajet($trajet);
                
                if ($this->envoyerEmail($trajet['email_passager'], $sujet, $corps, 'rappel')) {
                    $compteur++;
                }
            }
            
            return $compteur;
        } catch (Exception $e) {
            error_log("Erreur rappels : " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Template : Nouveau message
     */
    private function templateNouveauMessage($expediteur, $message) {
        $baseUrl = 'http://localhost/ecoride';
        $apercu = substr($message['contenu'], 0, 100);
        
        $contenu = "
            <div class='header'>
                <h1>💬 Nouveau Message</h1>
            </div>
            <div class='content'>
                <p>Vous avez reçu un nouveau message de <strong>{$expediteur['pseudo']}</strong> :</p>
                
                <div class='card'>
                    <p style='font-style: italic; color: #666;'>\"$apercu...\"</p>
                </div>
                
                <a href='$baseUrl/messagerie' class='button'>Lire le message</a>
            </div>
        ";
        
        return $this->getTemplateBase($contenu);
    }
    
    /**
     * Template : Rappel de trajet
     */
    private function templateRappelTrajet($data) {
        $baseUrl = 'http://localhost/ecoride';
        
        $contenu = "
            <div class='header'>
                <h1>🚗 Rappel : Trajet Demain !</h1>
            </div>
            <div class='content'>
                <p>Bonjour <strong>{$data['pseudo_passager']}</strong>,</p>
                <p>Petit rappel : votre trajet est <strong>demain</strong> ! 🎯</p>
                
                <div class='card'>
                    <h3 style='margin-top: 0; color: #667eea;'>📍 Votre trajet</h3>
                    <div class='info-row'>
                        <span class='info-label'>Trajet</span>
                        <span>{$data['ville_depart']} → {$data['ville_arrivee']}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Date & Heure</span>
                        <span><strong>" . date('d/m/Y à H:i', strtotime($data['date_depart'] . ' ' . $data['heure_depart'])) . "</strong></span>
                    </div>
                </div>
                
                <p>✅ <strong>Pensez à :</strong></p>
                <ul style='line-height: 2;'>
                    <li>Être ponctuel au point de rendez-vous</li>
                    <li>Contacter votre " . ($data['role'] == 'passager' ? 'chauffeur' : 'passagers') . " si besoin</li>
                    <li>Vérifier vos affaires avant de partir</li>
                </ul>
                
                <a href='$baseUrl/profil' class='button'>Voir les détails</a>
            </div>
        ";
        
        return $this->getTemplateBase($contenu);
    }
}