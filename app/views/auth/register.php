<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - EcoRide</title>
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
                    <li><a href="/connexion">Connexion</a></li>
                    <li><a href="/inscription" class="active">Inscription</a></li>
                <?php endif; ?>
                <li><a href="/contact">Contact</a></li>
            </ul>
        </div>
    </nav>

    <!-- Formulaire d'inscription -->
    <main class="auth-page">
        <div class="auth-container">
            <h2>Créer un compte EcoRide</h2>
            <p>Rejoignez la communauté du covoiturage écologique</p>

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

            <form action="/inscription" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="pseudo">Pseudo *</label>
                    <input type="text" id="pseudo" name="pseudo" required 
                            value="<?= isset($_POST['pseudo']) ? htmlspecialchars($_POST['pseudo']) : '' ?>"
                            minlength="3" maxlength="20">
                    <small>Entre 3 et 20 caractères, lettres, chiffres, underscore et tiret autorisés</small>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required 
                            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" 
                            value="<?= isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : '' ?>"
                            pattern="[0-9]{10}">
                    <small>Format : 0123456789</small>
                </div>

                <div class="form-group">
                    <label for="mot_de_passe">Mot de passe *</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" required 
                            minlength="8">
                    <small>Minimum 8 caractères avec majuscule, chiffre et caractère spécial</small>
                </div>

                <div class="form-group">
                    <label for="confirmer_mot_de_passe">Confirmer le mot de passe *</label>
                    <input type="password" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" required>
                </div>

                <button type="submit" class="btn btn-primary">Créer mon compte</button>
            </form>

            <div class="auth-links">
                <p>Déjà un compte ? <a href="/connexion">Se connecter</a></p>
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

    <script>
        // Validation côté client pour le mot de passe
        document.getElementById('confirmer_mot_de_passe').addEventListener('input', function() {
            const password = document.getElementById('mot_de_passe').value;
            const confirm = this.value;
            
            if (password !== confirm) {
                this.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>