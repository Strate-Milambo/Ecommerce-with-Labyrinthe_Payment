<?php
session_start();
require 'requetes.php';
$categories = getAllCategories();
$produits = getProduitsFiltre($_GET);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Produits - Restaurant Aldar</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h2>Nos recettes disponibles (<?= count($produits); ?>)</h2>

        <!-- Filtres de recherche -->
        <form method="GET" action="index.php" class="form-inline mb-4">
            <input type="text" name="search" class="form-control mr-2" placeholder="Rechercher un produit" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">

            <select name="categorie" class="form-control mr-2">
                <option value="">Toutes les catégories</option>
                <?php foreach ($categories as $categorie): ?>
                    <option value="<?= $categorie['id'] ?>" <?= (isset($_GET['categorie']) && $_GET['categorie'] == $categorie['id']) ? 'selected' : '' ?>><?= $categorie['nom'] ?></option>
                <?php endforeach; ?>
            </select>

            <input type="number" name="min_prix" class="form-control mr-2" placeholder="Prix min" value="<?= isset($_GET['min_prix']) ? $_GET['min_prix'] : '' ?>">
            <input type="number" name="max_prix" class="form-control mr-2" placeholder="Prix max" value="<?= isset($_GET['max_prix']) ? $_GET['max_prix'] : '' ?>">

            <button type="submit" class="btn btn-primary">Filtrer</button>
        </form>

        <!-- Liste des produits -->
        <div class="row">
            <?php foreach ($produits as $produit): ?>
                <div class="col-md-3">
                    <div class="card mb-4">
                        <img src="images/<?= $produit['image']; ?>" class="card-img-top" alt="<?= $produit['nom']; ?>" style="object-fit: cover; height: 200px;">
                        <div class="card-body">
                            <h5 class="card-title"><?= $produit['nom']; ?></h5>
                            <p><span class="badge badge-info"><?= getCategorieById($produit['categorie_id'])['nom']; ?></span></p>
                            <p class="card-text"><?= $produit['description']; ?></p>
                            <p><strong><?= $produit['prix']; ?> €</strong></p>

                            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'client'): ?>
                                <a href="ajouter_panier.php?id=<?= $produit['id']; ?>" class="btn btn-primary">Ajouter au panier</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>

</html>