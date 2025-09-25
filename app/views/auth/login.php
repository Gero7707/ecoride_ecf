<?php 
$pageSpecificCss = 'style.css';
require_once 'app/views/includes/head-header.php';
?>
    

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

<?php 
require_once 'app/views/includes/footer.php';
?>