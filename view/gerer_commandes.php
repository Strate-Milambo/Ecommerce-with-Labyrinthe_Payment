<?php
session_start();
require 'requetes.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: connexion.php');
    exit();
}

// Modification du statut de la commande
if (isset($_GET['changer_status']) && isset($_GET['commande_id'])) {
    $commande_id = $_GET['commande_id'];
    $status = $_GET['changer_status'];
    updateCommandeStatus($commande_id, $status);
    header('Location: gerer_commandes.php');
    exit();
}

// Filtres
$client_ids = isset($_GET['clients']) ? $_GET['clients'] : [];
$produit_ids = isset($_GET['produits']) ? $_GET['produits'] : [];
$statuses = isset($_GET['status']) ? $_GET['status'] : [];
$date_min = isset($_GET['date_min']) ? $_GET['date_min'] : '';
$date_max = isset($_GET['date_max']) ? $_GET['date_max'] : '';

// Obtenir les listes pour les filtres
$clients = getAllClients();
$produits = getAllProduits();
$commandes = getCommandesFiltreAvance($client_ids, $produit_ids, $statuses, $date_min, $date_max);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Gérer les Commandes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h2>Gérer les Commandes</h2>

        <!-- Formulaire de recherche -->
        <form method="GET" class="form-group mb-4">
            <div class="row">
                <!-- Filtre par client -->
                <div class="col-6 form-group ">
                    <label for="clients" class="mr-2">Clients:</label>
                    <select name="clients[]" id="clients" class="form-control" multiple>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['id']; ?>" <?= in_array($client['id'], $client_ids) ? 'selected' : '' ?>>
                                <?= $client['nom_complet']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Filtre par produit -->
                    <label for="produits" class="mr-2 mt-2">Produits:</label>
                    <select name="produits[]" id="produits" class="form-control" multiple>
                        <?php foreach ($produits as $produit): ?>
                            <option value="<?= $produit['id']; ?>" <?= in_array($produit['id'], $produit_ids) ? 'selected' : '' ?>>
                                <?= $produit['nom']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filtres par statut -->
                <div class="col-6 form-group ">
                    <label for="status" class="mr-2">Statut:</label>
                    <select name="status[]" id="status" class="form-control" multiple>
                        <option value="validée" <?= in_array('validée', $statuses) ? 'selected' : '' ?>>Validée</option>
                        <option value="annulée" <?= in_array('annulée', $statuses) ? 'selected' : '' ?>>Annulée</option>
                        <option value="en_attente" <?= in_array('en_attente', $statuses) ? 'selected' : '' ?>>En attente</option>
                    </select>

                    <!-- Filtre par date -->
                    <label for="date_min" class="mr-2">Date min:</label>
                    <input type="date" name="date_min" id="date_min" class="form-control" value="<?= $date_min ?>">

                    <label for="date_max" class="mr-2">Date max:</label>
                    <input type="date" name="date_max" id="date_max" class="form-control" value="<?= $date_max ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-info">Rechercher</button>
            <button type="reset" class="btn btn-secondary">Réinitialiser</button>
        </form>

        <!-- Liste des commandes -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $commande): ?>
                    <tr>
                        <td><?= $commande['client_nom']; ?></td>
                        <td><?= $commande['total']; ?> €</td>
                        <td>
                            <?php
                            $status = $commande['status'];
                            $statusClass = '';
                            $statusText = '';

                            // Assignation des classes Bootstrap et du texte en fonction du statut
                            switch ($status) {
                                case 'validee':
                                    $statusClass = 'badge-success';
                                    $statusText = 'Commande validée';
                                    break;
                                case 'annulee':
                                    $statusClass = 'badge-danger';
                                    $statusText = 'Commande annulée';
                                    break;
                                case 'en_attente':
                                    $statusClass = 'badge-warning';
                                    $statusText = 'En attente de validation';
                                    break;
                                default:
                                    $statusClass = 'badge-secondary';
                                    $statusText = ucfirst($status);
                                    break;
                            }
                            ?>
                            <span class="badge <?= $statusClass; ?>"><?= $statusText; ?></span>
                        </td>
                        <td><?= $commande['date_creation']; ?></td>
                        <td>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#detailsModal"
                                data-id="<?= $commande['id']; ?>"
                                data-client="<?= $commande['client_nom']; ?>"
                                data-total="<?= $commande['total']; ?>"
                                data-status="<?= ucfirst($commande['status']); ?>"
                                data-date="<?= $commande['date_creation']; ?>">
                                Détails
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal pour afficher les détails de la commande -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Détails de la Commande#<span id="commande-id"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Client:</strong> <span id="client-details"></span></p>
                    <p><strong>Total:</strong> <span id="total-details"></span> €</p>
                    <p><strong>Status:</strong> <span id="status-details"></span></p>
                    <p><strong>Date:</strong> <span id="date-details"></span></p>

                    <h6>Recettes commandés:</h6>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Prix</th>
                            </tr>
                        </thead>
                        <tbody id="produits-details"></tbody>
                    </table>

                    <h6>Actions:</h6>
                    <a href="#" id="valider-commande" class="btn btn-success">Valider</a>
                    <a href="#" id="annuler-commande" class="btn btn-danger">Annuler</a>
                    <a href="#" id="attente-commande" class="btn btn-warning">Mettre en attente</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        $('#detailsModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var client = button.data('client');
            var total = button.data('total');
            var status = button.data('status');
            var date = button.data('date');

            var modal = $(this);
            modal.find('#client-details').text(client);
            modal.find('#total-details').text(total);
            modal.find('#status-details').text(status);
            modal.find('#date-details').text(date);
            modal.find('#commande-id').text(id);

            // Charger les détails des produits
            $.ajax({
                url: 'get_details_commande.php',
                type: 'GET',
                data: {
                    commande_id: id
                },
                success: function(response) {
                    var produits = JSON.parse(response);
                    var produitList = '';
                    produits.forEach(function(produit) {
                        produitList += '<tr><td>' + produit.nom + '</td><td>' + produit.quantite + '</td><td>' + produit.prix + ' €</td></tr>';
                    });
                    modal.find('#produits-details').html(produitList);
                }
            });

            // Liens pour les actions sur la commande
            modal.find('#valider-commande').attr('href', 'gerer_commandes.php?changer_status=validée&commande_id=' + id);
            modal.find('#annuler-commande').attr('href', 'gerer_commandes.php?changer_status=annulée&commande_id=' + id);
            modal.find('#attente-commande').attr('href', 'gerer_commandes.php?changer_status=en_attente&commande_id=' + id);
        });
    </script>

</body>

</html>