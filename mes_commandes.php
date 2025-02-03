<?php
session_start();
require 'requetes.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'client') {
    header('Location: connexion.php');
    exit();
}

$client_id = $_SESSION['user']['id'];
$commandes = getCommandesByClient($client_id);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Mes Commandes - Restaurant Aldar</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h2>Mes Commandes</h2>

        <?php if (count($commandes) > 0): ?>
            <?php foreach ($commandes as $commande): ?>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Commande #<?= $commande['id']; ?></h5>
                        <span class="badge <?= $commande['status'] === 'annulee' ? 'badge-danger' : ($commande['status'] === 'validee' ? 'badge-success' : 'badge-warning'); ?>">
                            <?= $commande['status'] === 'annulee' ? 'Commande annulée' : ($commande['status'] === 'validee' ? 'Commande validée' : 'Commande en attente'); ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <p><strong>Date :</strong> <?= date('d/m/Y', strtotime($commande['date_creation'])); ?></p>

                        <h6>Recettes commandés :</h6>
                        <ul class="list-group mb-3">
                            <?php
                            $details = getDetailsCommande($commande['id']);
                            foreach ($details as $detail):
                                $produit = getProduitById($detail['produit_id']);
                            ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <img src="images/<?= $produit['image']; ?>" alt="<?= $produit['nom']; ?>" class="img-thumbnail mr-2" style="width: 50px; height: 50px;">
                                        <span><?= $produit['nom']; ?> (Quantité : <?= $detail['quantite']; ?>, Catégorie : <span class="badge badge-info"><?= getCategorieById($produit['categorie_id'])['nom']; ?></span>)</span>
                                    </div>
                                    <span><?= number_format($detail['prix'] * $detail['quantite'], 2); ?> €</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <h6>Total : <strong><?= number_format($commande['total'], 2); ?> €</strong></h6>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Vous n'avez pas encore passé de commande.</p>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>

</body>

</html>