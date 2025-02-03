<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">Restaurant Aldar</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="index.php">Recettes</a>
            </li>

        </ul>
        <ul class="navbar-nav ml-auto">
            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user']['role'] === 'client'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="panier.php">Mon Panier <span class="badge badge-pill badge-info"><?= isset($_SESSION['panier']) ? count($_SESSION['panier']) : 0; ?></span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="mes_commandes.php">Mes Commandes</a>
                    </li>
                <?php elseif ($_SESSION['user']['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="gerer_produits.php">Gérer les recettes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gerer_categories.php">Gérer les Catégories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gerer_clients.php">Gérer les Clients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gerer_commandes.php">Gérer les Commandes</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item dropdown">
                    <a class="nav-link " href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= $_SESSION['user']['nom_complet']; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="deconnexion.php">Déconnexion</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="connexion.php">Connexion</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="inscription.php">Inscription</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>