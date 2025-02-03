<?php
session_start();
require 'requetes.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403); // Interdiction d'accès si non admin
    echo json_encode(['error' => 'Accès non autorisé']);
    exit();
}

// Vérifier si l'ID de la commande est fourni
if (!isset($_GET['commande_id'])) {
    http_response_code(400); // Mauvaise requête si l'ID de la commande n'est pas fourni
    echo json_encode(['error' => 'ID de la commande manquant']);
    exit();
}

$commande_id = $_GET['commande_id'];

// Récupérer les détails de la commande
$details = getDetailsCommandeById($commande_id);

if ($details) {
    // Retourner les détails sous format JSON
    echo json_encode($details);
} else {
    // Retourner une erreur si aucun détail n'est trouvé
    http_response_code(404); // Ressource non trouvée
    echo json_encode(['error' => 'Détails de la commande introuvables']);
}
