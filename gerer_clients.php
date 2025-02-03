<?php
session_start();
require 'requetes.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: connexion.php');
    exit();
}

// Modification d'un client
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_client'])) {
    $client_id = $_POST['client_id'];
    $nom_complet = $_POST['nom_complet'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    modifierClient($client_id, $nom_complet, $email, $telephone);
    header('Location: gerer_clients.php');
    exit();
}

// Suppression d'un client
if (isset($_GET['supprimer'])) {
    $client_id = $_GET['supprimer'];
    supprimerClient($client_id);
    header('Location: gerer_clients.php');
    exit();
}

// Filtre de recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';
$clients = getClientsFiltre($search);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Gérer les Clients</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h2>Gérer les Clients</h2>

    <!-- Formulaire de recherche -->
    <form method="GET" class="form-inline mb-4">
        <input type="text" name="search" class="form-control mr-2" placeholder="Rechercher un client" value="<?= $search ?>">
        <button type="submit" class="btn btn-info">Rechercher</button>
    </form>

    <!-- Liste des clients -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom complet</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $client): ?>
                <tr>
                    <td><?= $client['nom_complet']; ?></td>
                    <td><?= $client['email']; ?></td>
                    <td><?= $client['telephone']; ?></td>
                    <td>
                        <button class="btn btn-warning" data-toggle="modal" data-target="#editModal" 
                                data-id="<?= $client['id']; ?>"
                                data-nom_complet="<?= $client['nom_complet']; ?>"
                                data-email="<?= $client['email']; ?>"
                                data-telephone="<?= $client['telephone']; ?>">
                            Modifier
                        </button>
                        <a href="gerer_clients.php?supprimer=<?= $client['id']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal pour la modification des clients -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Modifier Client</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" action="gerer_clients.php" method="POST">
                    <input type="hidden" name="client_id" id="edit-id">
                    <div class="form-group">
                        <label for="edit-nom_complet">Nom complet</label>
                        <input type="text" class="form-control" id="edit-nom_complet" name="nom_complet" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-email">Email</label>
                        <input type="email" class="form-control" id="edit-email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-telephone">Téléphone</label>
                        <input type="text" class="form-control" id="edit-telephone" name="telephone" required>
                    </div>
                    <button type="submit" name="modifier_client" class="btn btn-primary">Enregistrer les modifications</button>
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
        var nom_complet = button.data('nom_complet');
        var email = button.data('email');
        var telephone = button.data('telephone');

        var modal = $(this);
        modal.find('#edit-id').val(id);
        modal.find('#edit-nom_complet').val(nom_complet);
        modal.find('#edit-email').val(email);
        modal.find('#edit-telephone').val(telephone);
    });
</script>

</body>
</html>
