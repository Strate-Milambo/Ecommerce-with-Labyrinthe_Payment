<?php
session_start();
require 'requetes.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: connexion.php');
    exit();
}

// Ajout d'une nouvelle catégorie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_categorie'])) {
    $nom = $_POST['nom'];
    ajouterCategorie($nom);
}

// Modification d'une catégorie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_categorie'])) {
    $categorie_id = $_POST['categorie_id'];
    $nom = $_POST['nom'];
    modifierCategorie($categorie_id, $nom);
    header('Location: gerer_categories.php');
    exit();
}

// Suppression d'une catégorie
if (isset($_GET['supprimer'])) {
    $categorie_id = $_GET['supprimer'];
    supprimerCategorie($categorie_id);
    header('Location: gerer_categories.php');
    exit();
}

// Filtre de recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';
$categories = getCategoriesFiltre($search);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Gérer les Catégories</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h2>Gérer les Catégories</h2>

    <!-- Formulaire d'ajout -->
    <form action="gerer_categories.php" method="POST" class="form-inline mb-4">
        <input type="text" name="nom" class="form-control mr-2" placeholder="Nom de la catégorie" required>
        <button type="submit" name="ajouter_categorie" class="btn btn-primary">Ajouter Catégorie</button>
    </form>

    <!-- Formulaire de recherche -->
    <form method="GET" class="form-inline mb-4">
        <input type="text" name="search" class="form-control mr-2" placeholder="Rechercher une catégorie" value="<?= $search ?>">
        <button type="submit" class="btn btn-info">Rechercher</button>
    </form>

    <!-- Liste des catégories -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $categorie): ?>
                <tr>
                    <td><?= $categorie['nom']; ?></td>
                    <td>
                        <button class="btn btn-warning" data-toggle="modal" data-target="#editModal" 
                                data-id="<?= $categorie['id']; ?>"
                                data-nom="<?= $categorie['nom']; ?>">
                            Modifier
                        </button>
                        <a href="gerer_categories.php?supprimer=<?= $categorie['id']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal pour la modification des catégories -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Modifier Catégorie</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" action="gerer_categories.php" method="POST">
                    <input type="hidden" name="categorie_id" id="edit-id">
                    <div class="form-group">
                        <label for="edit-nom">Nom</label>
                        <input type="text" class="form-control" id="edit-nom" name="nom" required>
                    </div>
                    <button type="submit" name="modifier_categorie" class="btn btn-primary">Enregistrer les modifications</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Bouton qui a déclenché le modal
        var id = button.data('id');
        var nom = button.data('nom');

        var modal = $(this);
        modal.find('#edit-id').val(id);
        modal.find('#edit-nom').val(nom);
    });
</script>

</body>
</html>
