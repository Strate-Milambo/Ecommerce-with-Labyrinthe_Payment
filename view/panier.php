<?php
session_start();
require 'requetes.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'client') {
    header('Location: connexion.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Votre Panier</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h2>Votre Panier</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Produit</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                if (isset($_SESSION['panier'])):
                    foreach ($_SESSION['panier'] as $item):
                        $produit = getProduitById($item['produit_id']);
                        $totalItem = $produit['prix'] * $item['quantite'];
                        $total += $totalItem;
                ?>
                        <tr>
                            <td><img src="images/<?= $produit['image']; ?>" alt="<?= $produit['nom']; ?>" style="width: 50px; height: 50px;"></td>
                            <td><?= $produit['nom']; ?></td>
                            <td><?= getCategorieById($produit['categorie_id'])['nom']; ?></td>
                            <td><?= $produit['prix']; ?>€</td>
                            <td><?= $item['quantite']; ?></td>
                            <td><?= $totalItem; ?>€</td>
                            <td>
                                <form action="panier_action.php" method="POST">
                                    <input type="hidden" name="produit_id" value="<?= $produit['id']; ?>">
                                    <button type="submit" name="augmenter_quantite" class="btn btn-info">+</button>
                                    <button type="submit" name="diminuer_quantite" class="btn btn-warning">-</button>
                                    <button type="submit" name="supprimer_panier" class="btn btn-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                <?php endforeach;
                endif; ?>
            </tbody>
        </table>

        <?php if (isset($_SESSION['panier']) && count($_SESSION['panier']) > 0): ?>
            <h3>Total : <?= $total; ?>€</h3>
            <a href="Labyrinthe-Payment-Interface-master/payment.php" class="btn btn-success">Acheter</a>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>

</body>

</html>