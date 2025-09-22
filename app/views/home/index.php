<?php 
require_once 'app/views/includes/head-header.php';
?>

    <!-- Page d'accueil -->
    <main>
        <!-- Section héro -->
        <section class="hero">
            <div class="hero-content">
                <h2>Voyagez écologique, économique et convivial</h2>
                <p>EcoRide, la plateforme de covoiturage pour un transport plus vert et plus social.</p>
                
                <!-- Barre de recherche -->
                <div class="search-box">
                    <form action="/covoiturages" method="GET">
                        <input type="text" name="depart" placeholder="Ville de départ" required>
                        <input type="text" name="arrivee" placeholder="Ville d'arrivée" required>
                        <!-- <input type="date" name="date" required> -->
                        <input type="text" id="datepicker" class="form-control" placeholder="Choisis une date">
                        <button type="submit">Rechercher</button>
                    </form>
                </div>
            </div>
        </section>

        <?php if (isset($_SESSION['user_id'])): ?>
            <section class="welcome-user">
                <div class="container">
                    <div class="alert alert-success">
                        Bienvenue <?= htmlspecialchars($_SESSION['user_pseudo']) ?> ! 
                        Vous avez <?= isset($_SESSION['user_credits']) ? $_SESSION['user_credits'] : '0' ?> crédits.
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Section présentation -->
        <section class="presentation">
            <div class="container">
                <h3>Pourquoi choisir EcoRide ?</h3>
                <div class="features">
                    <div class="feature">
                        <h4>🌍 Écologique</h4>
                        <p>Réduisez votre empreinte carbone en partageant vos trajets</p>
                    </div>
                    <div class="feature">
                        <h4>💰 Économique</h4>
                        <p>Partagez les frais de route et voyagez moins cher</p>
                    </div>
                    <div class="feature">
                        <h4>🤝 Convivial</h4>
                        <p>Rencontrez de nouvelles personnes et créez du lien</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

<?php 
require_once 'app/views/includes/footer.php';
?>
