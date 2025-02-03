<?php

// Fonction pour se connecter à la base de données
function dbConnect()
{
    try {
        $pdo = new PDO('mysql:host=localhost:3306;dbname=restaurant_aldar', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
}

/**
 * Gestion des utilisateurs (clients et administrateurs)
 */

// Récupérer un utilisateur par email
function getUserByEmail($email)
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Ajouter un client (inscription)
function ajouterClient($nom_complet, $email, $telephone, $password)
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare("INSERT INTO users (nom_complet, email, telephone, password, role) VALUES (?, ?, ?, ?, 'client')");
    $stmt->execute([$nom_complet, $email, $telephone, $password]);
}

// Modifier un client
function modifierClient($id, $nom_complet, $email, $telephone)
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare("UPDATE users SET nom_complet = ?, email = ?, telephone = ? WHERE id = ?");
    $stmt->execute([$nom_complet, $email, $telephone, $id]);
}

// Supprimer un client
function supprimerClient($id)
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
}

// Récupérer tous les clients
function getAllClients()
{
    $pdo = dbConnect();
    $stmt = $pdo->query("SELECT * FROM users WHERE role = 'client'");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Gestion des catégories
 */

// Ajouter une catégorie
function ajouterCategorie($nom)
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare("INSERT INTO categories (nom) VALUES (?)");
    $stmt->execute([$nom]);
}

// Modifier une catégorie
function modifierCategorie($id, $nom)
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare("UPDATE categories SET nom = ? WHERE id = ?");
    $stmt->execute([$nom, $id]);
}

// Supprimer une catégorie
function supprimerCategorie($id)
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
}

// Récupérer une catégorie par son ID
function getCategorieById($id)
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Récupérer toutes les catégories
function getAllCategories()
{
    $pdo = dbConnect();
    $stmt = $pdo->query("SELECT * FROM categories");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Gestion des produits
 */

// Ajouter un produit
function ajouterProduit($nom, $prix, $description, $image, $categorie_id)
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare("INSERT INTO produits (nom, prix, description, image, categorie_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $prix, $description, $image, $categorie_id]);
}

// Modifier un produit
function modifierProduit($id, $nom, $prix, $description, $image, $categorie_id)
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare("UPDATE produits SET nom = ?, prix = ?, description = ?, image = ?, categorie_id = ? WHERE id = ?");
    $stmt->execute([$nom, $prix, $description, $image, $categorie_id, $id]);
}

// Supprimer un produit
function supprimerProduit($id)
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare("DELETE FROM produits WHERE id = ?");
    $stmt->execute([$id]);
}

// Récupérer tous les produits
function getAllProduits()
{
    $pdo = dbConnect();
    $stmt = $pdo->query("SELECT p.*, c.nom AS categorie_nom FROM produits p LEFT JOIN categories c ON p.categorie_id = c.id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer un produit par son ID
function getProduitById($id)
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Gestion des commandes
 */

// Ajouter une commande
function ajouterCommande($client_id, $total)
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare("INSERT INTO commandes (client_id, total) VALUES (?, ?)");
    $stmt->execute([$client_id, $total]);
    return $pdo->lastInsertId();
}

// Ajouter les détails d'une commande
function ajouterDetailsCommande($commande_id, $produit_id, $quantite, $prix)
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare("INSERT INTO details_commande (commande_id, produit_id, quantite, prix) VALUES (?, ?, ?, ?)");
    $stmt->execute([$commande_id, $produit_id, $quantite, $prix]);
}

// Annuler une commande
function annulerCommande($id)
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare("UPDATE commandes SET status = 'annulée' WHERE id = ?");
    $stmt->execute([$id]);
}

// Supprimer une commande
function supprimerCommande($id)
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare("DELETE FROM commandes WHERE id = ?");
    $stmt->execute([$id]);
}

// Récupérer toutes les commandes
function getAllCommandes()
{
    $pdo = dbConnect();
    $stmt = $pdo->query("SELECT c.*, u.nom_complet AS client_nom FROM commandes c LEFT JOIN users u ON c.client_id = u.id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Gestion du panier (sessions)
 */

// Ajouter un produit au panier
function ajouterAuPanier($produit_id, $quantite)
{
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }
    // Vérifie si le produit existe déjà dans le panier
    $found = false;
    foreach ($_SESSION['panier'] as &$item) {
        if ($item['produit_id'] == $produit_id) {
            $item['quantite'] += $quantite;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION['panier'][] = ['produit_id' => $produit_id, 'quantite' => $quantite];
    }
}

// Supprimer un produit du panier
function supprimerDuPanier($produit_id)
{
    if (isset($_SESSION['panier'])) {
        foreach ($_SESSION['panier'] as $key => $item) {
            if ($item['produit_id'] == $produit_id) {
                unset($_SESSION['panier'][$key]);
                break;
            }
        }
        $_SESSION['panier'] = array_values($_SESSION['panier']); // Réindexer les clés
    }
}

// Augmenter la quantité d'un produit dans le panier
function augmenterQuantite($produit_id)
{
    if (isset($_SESSION['panier'])) {
        foreach ($_SESSION['panier'] as &$item) {
            if ($item['produit_id'] == $produit_id) {
                $item['quantite']++;
                break;
            }
        }
    }
}

// Diminuer la quantité d'un produit dans le panier
function diminuerQuantite($produit_id)
{
    if (isset($_SESSION['panier'])) {
        foreach ($_SESSION['panier'] as &$item) {
            if ($item['produit_id'] == $produit_id && $item['quantite'] > 1) {
                $item['quantite']--;
                break;
            }
        }
    }
}

/**
 * Fonction de recherche et filtres des produits
 */

// Récupérer les produits avec filtre (catégorie, prix, recherche)
function getProduitsFiltre($filters)
{
    $pdo = dbConnect();
    $sql = "SELECT * FROM produits WHERE 1=1";

    // Filtrer par recherche de texte
    if (isset($filters['search']) && !empty($filters['search'])) {
        $search = "%" . $filters['search'] . "%";
        $sql .= " AND (nom LIKE :search OR description LIKE :search)";
    }

    // Filtrer par catégorie
    if (isset($filters['categorie']) && !empty($filters['categorie'])) {
        $categorie_id = $filters['categorie'];
        $sql .= " AND categorie_id = :categorie_id";
    }

    // Filtrer par intervalle de prix
    if (isset($filters['min_prix']) && !empty($filters['min_prix'])) {
        $min_prix = $filters['min_prix'];
        $sql .= " AND prix >= :min_prix";
    }

    if (isset($filters['max_prix']) && !empty($filters['max_prix'])) {
        $max_prix = $filters['max_prix'];
        $sql .= " AND prix <= :max_prix";
    }

    $stmt = $pdo->prepare($sql);

    if (isset($filters['search']) && !empty($filters['search'])) {
        $stmt->bindParam(':search', $search);
    }

    if (isset($filters['categorie']) && !empty($filters['categorie'])) {
        $stmt->bindParam(':categorie_id', $categorie_id);
    }

    if (isset($filters['min_prix']) && !empty($filters['min_prix'])) {
        $stmt->bindParam(':min_prix', $min_prix);
    }

    if (isset($filters['max_prix']) && !empty($filters['max_prix'])) {
        $stmt->bindParam(':max_prix', $max_prix);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer les commandes par client
function getCommandesByClient($client_id)
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare("SELECT * FROM commandes WHERE client_id = ? ORDER BY date_creation DESC");
    $stmt->execute([$client_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Récupérer les détails d'une commande
function getDetailsCommande($commande_id)
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare("SELECT * FROM details_commande WHERE commande_id = ?");
    $stmt->execute([$commande_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Récupérer les catégories avec un filtre de recherche
function getCategoriesFiltre($search)
{
    $pdo = dbConnect();
    $sql = "SELECT * FROM categories WHERE nom LIKE ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$search%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Récupérer les clients avec un filtre de recherche
function getClientsFiltre($search)
{
    $pdo = dbConnect();
    $sql = "SELECT * FROM users WHERE role = 'client' AND (nom_complet LIKE ? OR email LIKE ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$search%", "%$search%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Récupérer les commandes avec un filtre de recherche
function getCommandesFiltre($search)
{
    $pdo = dbConnect();
    $sql = "SELECT c.*, u.nom_complet AS client_nom FROM commandes c LEFT JOIN users u ON c.client_id = u.id WHERE u.nom_complet LIKE ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$search%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Mettre à jour le statut de la commande
function updateCommandeStatus($commande_id, $status)
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare("UPDATE commandes SET status = ? WHERE id = ?");
    $stmt->execute([$status, $commande_id]);
}
// Récupérer les produits avec un filtre de recherche
function getProduitsFiltreSearch($search)
{
    $pdo = dbConnect();
    $sql = "SELECT p.*, c.nom AS categorie_nom FROM produits p LEFT JOIN categories c ON p.categorie_id = c.id WHERE p.nom LIKE ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$search%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getCommandesFiltreAvance($client_ids = [], $produit_ids = [], $statuses = [], $date_min = '', $date_max = '')
{
    $pdo = dbConnect();
    $sql = "SELECT DISTINCT c.*, u.nom_complet AS client_nom 
            FROM commandes c 
            JOIN users u ON c.client_id = u.id
            JOIN details_commande dc ON dc.commande_id = c.id
            JOIN produits p ON p.id = dc.produit_id
            WHERE 1=1";

    $params = [];

    // Filtrer par clients
    if (!empty($client_ids)) {
        $inQuery = implode(',', array_fill(0, count($client_ids), '?'));
        $sql .= " AND c.client_id IN ($inQuery)";
        $params = array_merge($params, $client_ids);
    }

    // Filtrer par produits
    if (!empty($produit_ids)) {
        $inQuery = implode(',', array_fill(0, count($produit_ids), '?'));
        $sql .= " AND dc.produit_id IN ($inQuery)";
        $params = array_merge($params, $produit_ids);
    }

    // Filtrer par statuts
    if (!empty($statuses)) {
        $inQuery = implode(',', array_fill(0, count($statuses), '?'));
        $sql .= " AND c.status IN ($inQuery)";
        $params = array_merge($params, $statuses);
    }

    // Filtrer par date
    if (!empty($date_min)) {
        $sql .= " AND c.date_creation >= ?";
        $params[] = $date_min;
    }
    if (!empty($date_max)) {
        $sql .= " AND c.date_creation <= ?";
        $params[] = $date_max;
    }

    $sql .= " ORDER BY c.date_creation DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getDetailsCommandeById($commande_id)
{
    $pdo = dbConnect();
    $sql = "SELECT dc.produit_id, p.nom, dc.quantite, dc.prix 
            FROM details_commande dc
            JOIN produits p ON dc.produit_id = p.id
            WHERE dc.commande_id = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$commande_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProduitsPlusVendus()
{
    $pdo = dbConnect();
    $sql = "SELECT p.nom, SUM(dc.quantite) AS quantite_totale
            FROM produits p
            JOIN details_commande dc ON p.id = dc.produit_id
            GROUP BY p.nom
            ORDER BY quantite_totale DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProduitsPlusVendus7Jours()
{
    $pdo = dbConnect();
    $sql = "SELECT p.nom, SUM(dc.quantite) AS quantite_totale
            FROM produits p
            JOIN details_commande dc ON p.id = dc.produit_id
            JOIN commandes c ON dc.commande_id = c.id
            WHERE c.date_creation >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY p.nom
            ORDER BY quantite_totale DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProduitsPlusRentables()
{
    $pdo = dbConnect();
    $sql = "SELECT p.nom, SUM(dc.quantite * dc.prix) AS revenu_total
            FROM produits p
            JOIN details_commande dc ON p.id = dc.produit_id
            GROUP BY p.nom
            ORDER BY revenu_total DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getClientsPlusAcheteurs()
{
    $pdo = dbConnect();
    $sql = "SELECT u.nom_complet, COUNT(c.id) AS nb_commandes
            FROM users u
            JOIN commandes c ON u.id = c.client_id
            GROUP BY u.nom_complet
            ORDER BY nb_commandes DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getClientsPlusRentables()
{
    $pdo = dbConnect();
    $sql = "SELECT u.nom_complet, SUM(c.total) AS total_depense
            FROM users u
            JOIN commandes c ON u.id = c.client_id
            GROUP BY u.nom_complet
            ORDER BY total_depense DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCommandesAnnulees()
{
    $pdo = dbConnect();
    $sql = "SELECT COUNT(*) AS nb_annulees FROM commandes WHERE status = 'annulée'";
    $stmt = $pdo->query($sql);
    return $stmt->fetchColumn();
}
