<?php
session_start();
require 'requetes.php';

// Vérifier si l'utilisateur est bien un client et non un admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'client') {
    header('Location: connexion.php');
    exit();
}

// Récupérer l'ID du produit depuis l'URL
if (isset($_GET['id'])) {
    $produit_id = $_GET['id'];
    $quantite = 1; // Par défaut, ajouter 1 article
    
    // Récupérer le produit à partir de la base de données
    $produit = getProduitById($produit_id);

    if ($produit) {
        // Vérifier si le panier est déjà créé, sinon initialiser
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }

        // Vérifier si le produit est déjà dans le panier
        $produit_existe = false;
        foreach ($_SESSION['panier'] as &$item) {
            if ($item['produit_id'] == $produit_id) {
                $item['quantite'] += $quantite; // Augmenter la quantité
                $produit_existe = true;
                break;
            }
        }

        // Si le produit n'est pas déjà dans le panier, l'ajouter
        if (!$produit_existe) {
            $_SESSION['panier'][] = [
                'produit_id' => $produit_id,
                'quantite' => $quantite,
            ];
        }

        // Redirection vers la page du panier ou d'accueil après l'ajout
        header('Location: panier.php');
        exit();
    } else {
        // Si le produit n'existe pas dans la base de données
        header('Location: index.php?error=Produit non trouvé');
        exit();
    }
} else {
    // Si aucun ID de produit n'est passé dans l'URL
    header('Location: index.php');
    exit();
}
