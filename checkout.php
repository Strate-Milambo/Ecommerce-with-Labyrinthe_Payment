<?php
session_start();
require 'requetes.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'client') {
    header('Location: connexion.php');
    exit();
}

if (isset($_SESSION['panier']) && count($_SESSION['panier']) > 0) {
    $client_id = $_SESSION['user']['id'];
    $total = 0;

    foreach ($_SESSION['panier'] as $item) {
        $produit = getProduitById($item['produit_id']);
        $total += $produit['prix'] * $item['quantite'];
    }

    $commande_id = ajouterCommande($client_id, $total);

    foreach ($_SESSION['panier'] as $item) {
        $produit = getProduitById($item['produit_id']);
        ajouterDetailsCommande($commande_id, $produit['id'], $item['quantite'], $produit['prix']);
    }


    unset($_SESSION['panier']);
    header('Location: paiement_reussie.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Checkout - Restaurant Aldar</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h2>Merci pour votre achat !</h2>
        <p>Votre commande a bien été enregistrée.</p>
    </div>

    <?php include 'footer.php'; ?>

</body>

</html>