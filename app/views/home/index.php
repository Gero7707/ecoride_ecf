<?php 
$pageSpecificCss = 'style.css';
require_once 'app/views/includes/head-header.php';
?>

    <!-- Page d'accueil -->
    <main>
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
        
        <!-- Section héro -->
        <section class="hero">
            <div class="hero-content">
                <h2 class="p-3">Voyagez écologique, économique et convivial</h2>
                <p class="p-2">EcoRide, la plateforme de covoiturage pour un transport plus vert et plus social.</p>
                
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

        

        <!-- Section présentation -->
        <section class="presentation">
            <div class="container">
                <h2 class="text-center">Pourquoi choisir EcoRide ?</h2>
                <div class="features">
                    <div class="feature">
                        <h4><img src="https://www.gifsgratuits.fr/planete/planete%20(2).gif" alt="Emoji écologique" class="gif"> Écologique</h4>
                        <p>Réduisez votre empreinte carbone en partageant vos trajets</p>
                    </div>
                    <div class="feature">
                        <h4><img src="https://www.gifsgratuits.fr/emojis/argent/1%20(156).gif" alt="Emoji économique"> Économique</h4>
                        <p>Partagez les frais de route et voyagez moins cher en faisant de économies</p>
                    </div>
                    <div class="feature">
                        <h4><img src="https://www.gifsgratuits.fr/main/m%20(323).gif" alt="Emoji de convivialité" class="gif"> Convivial</h4>
                        <p>Rencontrez de nouvelles personnes et échangez durant votre trajet </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

<?php 
require_once 'app/views/includes/footer.php';
?>
