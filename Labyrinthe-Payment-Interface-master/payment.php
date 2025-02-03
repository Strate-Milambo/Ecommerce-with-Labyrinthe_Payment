<?php
  session_start();
  if (!isset($_SESSION['user'])) {
    header('Location: connexion.php');
    exit();
}
  if(isset($_SESSION['user'])){

    $reference = base64_encode($_SESSION['user']['id'].time());
  
  }
  if(isset($_POST) and !empty($_POST)){

    $data = array(
      # Remplacez par vos propres informations
      "token" => $_POST['token'],
      "phone"=> $_POST['phone'],
      "reference" => $_POST['reference'],
    );
    $gateway = "https://payment.labyrinthe-rdc.com/api/beta/mobile";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $gateway);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
    $response = curl_exec($ch);
    if(curl_errno($ch)) {
        # Erreur !
        echo "<script>alert('Le service de paiement ne pas disponible pour l'instant 😑 , Merci d'essayer ultérieurement')</script>";
    }else {
        curl_close($ch);
        $jsonRes = json_decode($response);
        $jsonRes->success;
        if ($jsonRes->success) {
            # La requête retourne la bonne valeur 
            unset($_SESSION['panier']);
            header('Location: ../paiement_reussie.php');
            exit();
        }else{
            # La requête a rencontré des problèmes 
            echo "<script>alert('votre paiement n'a pas aboutit')</script>";
        }
        // echo "<pre>";
        // print_r($jsonRes);
        // echo "<pre>";
    }
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Restaurant-aldar</title>

  <!-- Favicons -->
  <link href="./img/labyrinthe-logo-without-name-green-orange.png" rel="icon">
  <link href="./img/labyrinthe-logo-without-name-green-orange.png" rel="apple-touch-icon">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" />
  <link rel="stylesheet" href="css/mdb.min.css" />
</head>

<body style="height: 100%; background-color: #008374">
  <section style="background-color: #008374">
    <div class="container py-5">
      <div class="row d-flex justify-content-center">
        <div class="col-md-9 col-lg-7 col-xl-5">
          <div class="card">

            <div align="center" class="mt-2">
              <img src="img/labyrinthe-logo-without-name-black.png" class="card-img-top" alt="Logo Labyrinthe"
                style="width: 50%;" />
            </div>

            <div class="rounded-bottom" style="background-color: #eee;">
              <form action="" method="post" class="card-body">
                <p class="mb-4">Formulaire de paiement</p>

                <div data-mdb-input-init class="form-outline mb-3">
                  <input type="text" id="phone" name="phone" class="form-control" placeholder="0891000200" required/>
                  <label class="form-label" for="phone">Numéro de téléphone</label>
                </div>

                <!-- Hidden inputs -->
                <div data-mdb-input-init class="form-outline mb-3">
                  <input type="text" name="token" value="$2y$12$h6OLy.gkIO1G4v2f7ygmBurYCUbz2AHKpHWgepjSM0OtMAIZ9plrK" hidden />
                  <input type="text" name="reference" value="<?= $reference ?? '$xytNull'?>" hidden />
                </div>

                <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-block"
                  style="background: #f85a40; color: white;">
                  VALIDER L'ACHAT
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</body>

</html>


<script type="text/javascript" src="js/mdb.min.js"></script>