<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: connexion.php');
    exit();
}

require 'requetes.php';

// Récupérer les statistiques
$produitsPlusVendus = getProduitsPlusVendus();
$produits7Jours = getProduitsPlusVendus7Jours();
$produitsPlusRentables = getProduitsPlusRentables();
$clientsPlusAcheteurs = getClientsPlusAcheteurs();
$clientsPlusRentables = getClientsPlusRentables();
$commandesAnnulees = getCommandesAnnulees();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Administration - Statistiques</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Inclure la bibliothèque Chart.js -->
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h2>Statistiques de la Boutique</h2>

        <div class="row">
            <!-- Produits les plus vendus -->
            <div class="col-6">
                <h3>Produits les plus vendus</h3>
                <canvas id="produitsPlusVendusChart" width="400" height="200"></canvas>
            </div>

            <!-- Produits les plus vendus des 7 derniers jours -->
            <div class="col-6">
                <h3>Produits les plus vendus des 7 derniers jours</h3>
                <canvas id="produits7JoursChart" width="400" height="200"></canvas>
            </div>
        </div>

        <br>
        <div class="row">
            <!-- Produits qui rapportent le plus d'argent -->
            <div class="col-8">
                <h3>Produits qui rapportent le plus d'argent</h3>
                <canvas id="produitsPlusRentablesChart" width="400" height="200"></canvas>
            </div>

            <!-- Clients qui achètent le plus -->
            <div class="col-4">
                <h3>Clients qui achètent le plus</h3>
                <canvas id="clientsPlusAcheteursChart" width="200" height="100"></canvas>
            </div>
        </div>
        <br>
        <div class="row">

            <!-- Clients qui rapportent le plus d'argent -->
            <div class="col-4">
                <h3>Clients qui rapportent le plus d'argent</h3>
                <canvas id="clientsPlusRentablesChart" width="400" height="200"></canvas>
            </div>
            <!-- Clients qui achètent le plus -->
            <div class="col-8">
                <!-- Commandes annulées -->
                <h3>Commandes annulées</h3>
                <p>Nombre de commandes annulées : <strong><?= $commandesAnnulees; ?></strong></p>
            </div>
        </div>

    </div>

    <?php include 'footer.php'; ?>

    <!-- Script Chart.js pour créer les graphiques -->
    <script>
        // Produits les plus vendus
        var produitsPlusVendusCtx = document.getElementById('produitsPlusVendusChart').getContext('2d');
        var produitsPlusVendusChart = new Chart(produitsPlusVendusCtx, {
            type: 'bar',
            data: {
                labels: [<?php foreach ($produitsPlusVendus as $produit) {
                                echo '"' . $produit['nom'] . '",';
                            } ?>],
                datasets: [{
                    label: 'Quantité vendue',
                    data: [<?php foreach ($produitsPlusVendus as $produit) {
                                echo $produit['quantite_totale'] . ',';
                            } ?>],
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Produits les plus vendus des 7 derniers jours
        var produits7JoursCtx = document.getElementById('produits7JoursChart').getContext('2d');
        var produits7JoursChart = new Chart(produits7JoursCtx, {
            type: 'bar',
            data: {
                labels: [<?php foreach ($produits7Jours as $produit) {
                                echo '"' . $produit['nom'] . '",';
                            } ?>],
                datasets: [{
                    label: 'Quantité vendue (7 derniers jours)',
                    data: [<?php foreach ($produits7Jours as $produit) {
                                echo $produit['quantite_totale'] . ',';
                            } ?>],
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Produits qui rapportent le plus d'argent
        var produitsPlusRentablesCtx = document.getElementById('produitsPlusRentablesChart').getContext('2d');
        var produitsPlusRentablesChart = new Chart(produitsPlusRentablesCtx, {
            type: 'bar',
            data: {
                labels: [<?php foreach ($produitsPlusRentables as $produit) {
                                echo '"' . $produit['nom'] . '",';
                            } ?>],
                datasets: [{
                    label: 'Revenu total (€)',
                    data: [<?php foreach ($produitsPlusRentables as $produit) {
                                echo $produit['revenu_total'] . ',';
                            } ?>],
                    backgroundColor: 'rgba(255, 159, 64, 0.6)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Clients qui achètent le plus
        var clientsPlusAcheteursCtx = document.getElementById('clientsPlusAcheteursChart').getContext('2d');
        var clientsPlusAcheteursChart = new Chart(clientsPlusAcheteursCtx, {
            type: 'pie',
            data: {
                labels: [<?php foreach ($clientsPlusAcheteurs as $client) {
                                echo '"' . $client['nom_complet'] . '",';
                            } ?>],
                datasets: [{
                    label: 'Nombre de commandes',
                    data: [<?php foreach ($clientsPlusAcheteurs as $client) {
                                echo $client['nb_commandes'] . ',';
                            } ?>],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            }
        });

        // Clients qui rapportent le plus d'argent
        var clientsPlusRentablesCtx = document.getElementById('clientsPlusRentablesChart').getContext('2d');
        var clientsPlusRentablesChart = new Chart(clientsPlusRentablesCtx, {
            type: 'pie',
            data: {
                labels: [<?php foreach ($clientsPlusRentables as $client) {
                                echo '"' . $client['nom_complet'] . '",';
                            } ?>],
                datasets: [{
                    label: 'Total dépensé (€)',
                    data: [<?php foreach ($clientsPlusRentables as $client) {
                                echo $client['total_depense'] . ',';
                            } ?>],
                    backgroundColor: [
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)'
                    ],
                    borderColor: [
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            }
        });
    </script>

</body>

</html>