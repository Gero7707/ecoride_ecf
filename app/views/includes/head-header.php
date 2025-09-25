<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRide - Covoiturage Écologique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Datepicker CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <!-- <link rel="stylesheet" href="public/css/style.css"> -->
    <?php
    // htmlspecialchars($_SESSION['user_pseudo'])
    if (isset($pageSpecificCss)) {
        if (is_array($pageSpecificCss)) {
            // Si c'est un tableau, parcourir et inclure chaque fichier CSS.
            foreach ($pageSpecificCss as $cssFile) {
                 // htmlspecialchars() pour la sécurité sur le nom de fichier.
                // Ajout de '?v=' . time() pour le cache busting.
                echo '<link rel="stylesheet" href="public/css/' . htmlspecialchars($cssFile) . '?v=' . time() . '">' . PHP_EOL;
            }
        } else {
            // Si c'est une seule chaîne, inclure ce fichier CSS.
            // htmlspecialchars() pour la sécurité sur le nom de fichier.
            // Ajout de '?v=' . time() pour le cache busting.
            echo '<link rel="stylesheet" href="public/css/' . htmlspecialchars($pageSpecificCss) . '?v=' . time() . '">' . PHP_EOL;
        }
    }
    ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <div class="logo d-flex align-items-center justify-content-between">
                <img src="public/images/logo.png" alt="Logo EcoRide">
                <h1>EcoRide</h1>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarNavDropdown">
                <ul class="navbar-nav ">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/covoiturages">Covoiturages</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/profil">Mon compte </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/deconnexion">Déconnexion</a>
                    </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/connexion">Connexion</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/inscription">Inscription</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>