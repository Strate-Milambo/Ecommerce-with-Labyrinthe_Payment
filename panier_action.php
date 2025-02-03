<?php
session_start();
require 'requetes.php';

// Vérification que l'utilisateur est connecté et est un client
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'client') {
    header('Location: connexion.php');
    exit();
}

// Vérifier quelle action a été effectuée (augmentation, diminution, suppression)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Augmenter la quantité
    if (isset($_POST['augmenter_quantite']) && isset($_POST['produit_id'])) {
        $produit_id = $_POST['produit_id'];
        
        // Parcourir le panier et augmenter la quantité du produit
        foreach ($_SESSION['panier'] as &$item) {
            if ($item['produit_id'] == $produit_id) {
                $item['quantite']++;
                break;
            }
        }
    }

    // Diminuer la quantité
    if (isset($_POST['diminuer_quantite']) && isset($_POST['produit_id'])) {
        $produit_id = $_POST['produit_id'];
        
        // Parcourir le panier et diminuer la quantité du produit
        foreach ($_SESSION['panier'] as &$item) {
            if ($item['produit_id'] == $produit_id && $item['quantite'] > 1) {
                $item['quantite']--;
                break;
            }
        }
    }

    // Supprimer le produit du panier
    if (isset($_POST['supprimer_panier']) && isset($_POST['produit_id'])) {
        $produit_id = $_POST['produit_id'];

        // Parcourir le panier et supprimer le produit correspondant
        foreach ($_SESSION['panier'] as $key => $item) {
            if ($item['produit_id'] == $produit_id) {
                unset($_SESSION['panier'][$key]);
                break;
            }
        }

        // Réindexer le tableau après suppression
        $_SESSION['panier'] = array_values($_SESSION['panier']);
    }
}

// Rediriger vers la page du panier après l'action
header('Location: panier.php');
exit();
