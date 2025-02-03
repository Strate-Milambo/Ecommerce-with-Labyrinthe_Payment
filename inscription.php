<?php
session_start();
require 'requetes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_complet = $_POST['nom_complet'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $password = $_POST['password'];

    ajouterClient($nom_complet, $email, $telephone, $password);
    header('Location: connexion.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Inscription - Restaurant Aldar</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h2>Inscription</h2>

        <form method="POST" action="inscription.php">
            <div class="form-group">
                <label for="nom_complet">Nom complet:</label>
                <input type="text" class="form-control" name="nom_complet" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="form-group">
                <label for="telephone">Téléphone:</label>
                <input type="text" class="form-control" name="telephone" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe:</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Inscription</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>

</body>

</html>