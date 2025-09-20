<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - EcoRide</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <div class="logo">
                <a href="/"><h1>🌱 EcoRide</h1></a>
            </div>
            <ul class="nav-menu">
                <li><a href="/">Accueil</a></li>
                <li><a href="/covoiturages">Covoiturages</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="/profil">Mon compte</a></li>
                    <li><a href="/deconnexion">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="/connexion" class="active">Connexion</a></li>
                    <li><a href="/inscription">Inscription</a></li>
                <?php endif; ?>
                <li><a href="/contact">Contact</a></li>
            </ul>
        </div>
    </nav>

    <!-- Formulaire de connexion -->
    <main class="auth-page">
        <div class="auth-container">
            <h2>Se connecter à EcoRide</h2>
            <p>Accédez à votre espace personnel</p>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form action="/connexion" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="identifier">Email ou Pseudo</label>
                    <input type="text" id="identifier" name="identifier" required 
                            value="<?= isset($_POST['identifier']) ? htmlspecialchars($_POST['identifier']) : '' ?>"
                            placeholder="votre@email.com ou votre_pseudo">
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary">Se connecter</button>
            </form>

            <div class="auth-links">
                <p>Pas encore de compte ? <a href="/inscription">Créer un compte</a></p>
                <p><a href="/mot-de-passe-oublie">Mot de passe oublié ?</a></p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 EcoRide - Contact: contact@ecoride.fr</p>
            <p><a href="/mentions-legales">Mentions légales</a></p>
        </div>
    </footer>
</body>
</html>