<?php
session_start();
require 'requetes.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: connexion.php');
    exit();
}

// Ajout d'un nouveau produit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_produit'])) {
    $nom = $_POST['nom'];
    $prix = $_POST['prix'];
    $description = $_POST['description'];
    $categorie_id = $_POST['categorie_id'];
    $image = $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "images/$image");

    ajouterProduit($nom, $prix, $description, $image, $categorie_id);
}

// Modification d'un produit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_produit'])) {
    $produit_id = $_POST['produit_id'];
    $nom = $_POST['nom'];
    $prix = $_POST['prix'];
    $description = $_POST['description'];
    $categorie_id = $_POST['categorie_id'];
    $image = $_FILES['image']['name'];

    if (!empty($image)) {
        move_uploaded_file($_FILES['image']['tmp_name'], "images/$image");
    }

    modifierProduit($produit_id, $nom, $prix, $description, $image, $categorie_id);
    header('Location: gerer_produits.php');
    exit();
}

// Suppression d'un produit
if (isset($_GET['supprimer'])) {
    $produit_id = $_GET['supprimer'];
    supprimerProduit($produit_id);
    header('Location: gerer_produits.php');
    exit();
}

// Filtre de recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';
$produits = getProduitsFiltre($search);
$categories = getAllCategories();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Gérer les Produits</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h2>Gérer les Produits</h2>

        <!-- Formulaire d'ajout -->
        <form action="gerer_produits.php" method="POST" enctype="multipart/form-data" class="mb-4">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <input type="text" name="nom" class="form-control" placeholder="Nom du produit" required>
                </div>
                <div class="form-group col-md-4">
                    <input type="number" name="prix" class="form-control" placeholder="Prix" required>
                </div>
                <div class="form-group col-md-4">
                    <input type="text" name="description" class="form-control" placeholder="Description" required>
                </div>
                <div class="form-group col-md-4">
                    <select name="categorie_id" class="form-control">
                        <?php foreach ($categories as $categorie): ?>
                            <option value="<?= $categorie['id']; ?>"><?= $categorie['nom']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <input type="file" name="image" class="form-control" required>
                </div>
            </div>
            <button type="submit" name="ajouter_produit" class="btn btn-primary">Ajouter Produit</button>
        </form>

        <!-- Formulaire de recherche -->
        <form method="GET" class="form-group mb-4">
            <input type="text" name="search" class="form-control mr-2" placeholder="Rechercher un produit" value="<?= $search ?>">
            <button type="submit" class="btn btn-info mt-2">Rechercher</button>
        </form>

        <!-- Liste des produits -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prix</th>
                    <th>Catégorie</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produits as $produit): ?>
                    <tr>
                        <td><?= $produit['nom']; ?></td>
                        <td><?= $produit['prix']; ?> €</td>
                        <td><?= getCategorieById($produit['categorie_id'])['nom']; ?></td>
                        <td><?= $produit['description']; ?></td>
                        <td><img src="images/<?= $produit['image']; ?>" width="50" height="50"></td>
                        <td>
                            <button class="btn btn-warning" data-toggle="modal" data-target="#editModal"
                                data-id="<?= $produit['id']; ?>"
                                data-nom="<?= $produit['nom']; ?>"
                                data-prix="<?= $produit['prix']; ?>"
                                data-description="<?= $produit['description']; ?>"
                                data-categorie="<?= $produit['categorie_id']; ?>">
                                Modifier
                            </button>
                            <a href="gerer_produits.php?supprimer=<?= $produit['id']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal pour la modification des produits -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Modifier Produit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php if (!empty($produit['image'])): ?>
                        <div class="form-group">
                            <label>Image actuelle</label>
                            <div>
                                <img src="images/<?= $produit['image']; ?>" width="100" height="100">
                            </div>
                        </div>
                    <?php endif; ?>
                    <form id="editForm" action="gerer_produits.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="produit_id" id="edit-id">
                        <div class="form-group">
                            <label for="edit-nom">Nom</label>
                            <input type="text" class="form-control" id="edit-nom" name="nom" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-prix">Prix</label>
                            <input type="number" class="form-control" id="edit-prix" name="prix" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-description">Description</label>
                            <textarea class="form-control" id="edit-description" name="description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit-categorie">Catégorie</label>
                            <select class="form-control" id="edit-categorie" name="categorie_id">
                                <?php foreach ($categories as $categorie): ?>
                                    <option value="<?= $categorie['id']; ?>"><?= $categorie['nom']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-image">Image</label>
                            <input type="file" class="form-control" id="edit-image" name="image">
                        </div>
                        <button type="submit" name="modifier_produit" class="btn btn-primary">Enregistrer les modifications</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        $('#editModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Bouton qui a déclenché le modal
            var id = button.data('id');
            var nom = button.data('nom');
            var prix = button.data('prix');
            var description = button.data('description');
            var categorie = button.data('categorie');

            var modal = $(this);
            modal.find('#edit-id').val(id);
            modal.find('#edit-nom').val(nom);
            modal.find('#edit-prix').val(prix);
            modal.find('#edit-description').val(description);
            modal.find('#edit-categorie').val(categorie);
        });
    </script>

</body>

</html>