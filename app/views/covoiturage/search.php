<?php
require_once 'app/views/includes/head-header.php';
?>
<main class="search-page">
        <!-- Section de recherche -->
        <section class="search-section">
            <div class="search-box">
                <h2>Trouvez votre covoiturage</h2>
                <p>Recherchez parmi nos trajets écologiques et économiques</p>
                
                <form action="/covoiturages" method="GET" class="search-form">
                    <div class="search-inputs">
                        <div class="input-group">
                            <label for="depart">Ville de départ</label>
                            <input type="text" id="depart" name="depart" 
                                    value="<?= isset($_GET['depart']) ? htmlspecialchars($_GET['depart']) : '' ?>"
                                    placeholder="Ex: Paris" required>
                        </div>
                        
                        <div class="input-group">
                            <label for="arrivee">Ville d'arrivée</label>
                            <input type="text" id="arrivee" name="arrivee" 
                                    value="<?= isset($_GET['arrivee']) ? htmlspecialchars($_GET['arrivee']) : '' ?>"
                                    placeholder="Ex: Lyon" required>
                        </div>
                        
                        <div class="input-group">
                            <label for="date">Date de départ</label>
                            <input type="date" id="date" name="date" 
                                    value="<?= isset($_GET['date']) ? htmlspecialchars($_GET['date']) : '' ?>"
                                    min="<?= date('Y-m-d') ?>" required>
                        </div>
                        
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary">Rechercher</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <!-- Section filtres (affichée seulement si recherche effectuée) -->
        <?php if (isset($_GET['depart']) && isset($_GET['arrivee'])): ?>
        <section class="filters-section">
            <div class="search-box">
                <h3>Filtrer les résultats</h3>
                <div class="filters">
                    <div class="filter-group">
                        <label>
                            <input type="checkbox" name="ecologique" value="1" 
                                    <?= isset($_GET['ecologique']) ? 'checked' : '' ?>>
                            Trajets écologiques uniquement
                        </label>
                    </div>
                    
                    <div class="filter-group">
                        <label for="prix_max">Prix maximum</label>
                        <input type="number" id="prix_max" name="prix_max" 
                                value="<?= isset($_GET['prix_max']) ? htmlspecialchars($_GET['prix_max']) : '' ?>"
                                min="0" step="5" placeholder="€">
                    </div>
                    
                    <div class="filter-group">
                        <label for="note_min">Note minimale du chauffeur</label>
                        <select id="note_min" name="note_min">
                            <option value="">Toutes les notes</option>
                            <option value="4" <?= (isset($_GET['note_min']) && $_GET['note_min'] == '4') ? 'selected' : '' ?>>4 étoiles et plus</option>
                            <option value="5" <?= (isset($_GET['note_min']) && $_GET['note_min'] == '5') ? 'selected' : '' ?>>5 étoiles uniquement</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Section résultats -->
        <section class="results-section">
            <div class="search-box">
                <?php if (isset($_GET['depart']) && isset($_GET['arrivee'])): ?>
                    <?php if (isset($covoiturages) && !empty($covoiturages)): ?>
                        <h3><?= count($covoiturages) ?> trajet(s) trouvé(s)</h3>
                        
                        <div class="covoiturage-list">
                            <?php foreach ($covoiturages as $trajet): ?>
                                <div class="covoiturage-card">
                                    <div class="trajet-info">
                                        <div class="route">
                                            <strong><?= htmlspecialchars($trajet['ville_depart']) ?></strong>
                                            <span class="arrow">→</span>
                                            <strong><?= htmlspecialchars($trajet['ville_arrivee']) ?></strong>
                                        </div>
                                        
                                        <div class="datetime">
                                            <?= date('d/m/Y', strtotime($trajet['date_depart'])) ?> à 
                                            <?= date('H:i', strtotime($trajet['heure_depart'])) ?>
                                        </div>
                                        
                                        <?php if ($trajet['energie'] === 'electrique'): ?>
                                            <div class="eco-badge">🌱 Trajet écologique</div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="driver-info">
                                        <div class="driver-name">
                                            👤 <?= htmlspecialchars($trajet['pseudo']) ?>
                                        </div>
                                        <div class="rating">
                                            ⭐ <?= number_format($trajet['note_moyenne'] ?? 5, 1) ?>
                                        </div>
                                        <div class="vehicle">
                                            🚗 <?= htmlspecialchars($trajet['marque']) ?> <?= htmlspecialchars($trajet['modele']) ?>
                                        </div>
                                    </div>
                                    
                                    <div class="booking-info">
                                        <div class="price">
                                            <strong><?= number_format($trajet['prix'], 2) ?> €</strong>
                                            <small>par personne</small>
                                        </div>
                                        
                                        <div class="places">
                                            <?= $trajet['places_disponibles'] ?> place(s) disponible(s)
                                        </div>
                                        
                                        <a href="/covoiturage/<?= $trajet['id'] ?>" class="btn btn-secondary">
                                            Voir détails
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                    <?php else: ?>
                        <div class="no-results">
                            <h3>Aucun trajet trouvé</h3>
                            <p>Essayez de modifier vos critères de recherche ou de changer la date.</p>
                            
                            <?php if (isset($suggestion_date)): ?>
                                <div class="suggestion">
                                    <p>💡 Suggestion : Il y a des trajets disponibles le 
                                    <a href="/covoiturages?depart=<?= urlencode($_GET['depart']) ?>&arrivee=<?= urlencode($_GET['arrivee']) ?>&date=<?= $suggestion_date ?>">
                                        <?= date('d/m/Y', strtotime($suggestion_date)) ?>
                                    </a></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="search-prompt">
                        <h3>Commencez votre recherche</h3>
                        <p>Utilisez le formulaire ci-dessus pour trouver votre covoiturage idéal.</p>
                        
                        <div class="popular-routes">
                            <h4>Trajets populaires</h4>
                            <div class="route-links d-flex flex-column gap-2">
                                <a href="/covoiturages?depart=Paris&arrivee=Lyon&date=<?= date('Y-m-d', strtotime('+1 day')) ?>">Paris → Lyon</a>
                                <a href="/covoiturages?depart=Lyon&arrivee=Marseille&date=<?= date('Y-m-d', strtotime('+1 day')) ?>">Lyon → Marseille</a>
                                <a href="/covoiturages?depart=Paris&arrivee=Bordeaux&date=<?= date('Y-m-d', strtotime('+2 days')) ?>">Paris → Bordeaux</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>


<?php 
require_once 'app/views/includes/footer.php';
?>