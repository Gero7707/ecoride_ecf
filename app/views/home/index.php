<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRide - Covoiturage Écologique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Datepicker CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <div class="logo d-flex align-items-center justify-content-between">
                <img src="public/images/logo.png" alt="Logo EcoRide">
                <h1>EcoRide</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="/">Accueil</a></li>
                <li><a href="/covoiturages">Covoiturages</a></li>
                <li><a href="/connexion">Connexion</a></li>
                <li><a href="/contact">Contact</a></li>
            </ul>
        </div>
    </nav>

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

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 EcoRide - Contact: contact@ecoride.fr</p>
            <p><a href="/mentions-legales">Mentions légales</a></p>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Datepicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="public/js/main.js"></script>
</body>
</html>