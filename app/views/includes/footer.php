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
    <script src="/public/js/main.js"></script>

    <!-- JS commun -->
    <!-- <script src="/js/main.js" ></script> -->

    <!-- JS spécifiques selon la page -->
    <?php if (isset($pageSpecificJs)): ?>
        <?php if (is_array($pageSpecificJs)): ?>
            <?php foreach ($pageSpecificJs as $jsFile): ?>
                <script src="/public/js/<?= $jsFile ?>"></script>
            <?php endforeach; ?>
        <?php else: ?>
            <script src="/public/js/<?= $pageSpecificJs ?>"></script>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>