<?php
require 'database.php';

// On récupère l'id dans la variable $id si il existe
if (!empty($_GET['id'])) {
    $id = checkInput($_GET['id']);
}

if (!empty($_POST['id'])) {
    $id = checkInput($_POST['id']);
    $db = Database::connect();
    $statement = $db->prepare('DELETE FROM `items` WHERE `id` = ?');
    $statement->execute(array($id));
    Database::disconnect();
    header('Location: index.php');
}

// On déclare la fonction checkInput pour nettoyer les input
function checkInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    // Une fois nettoyé, on retourne $data
    return $data;
}
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
        <link href="https://fonts.googleapis.com/css?family=Holtwood+One+SC" rel="stylesheet" />
        <link rel="stylesheet" href="/assets/css/style.css">
        <title>Restaurant</title>
    </head>

    <body>
        <h1 class="text-logo"><span class="glyphicon glyphicon-cutlery"></span> Burger code <span class="glyphicon glyphicon-cutlery"></span></h1>
        <div class="container admin">
            <div class="row">

                <h1><strong>Supprimer un item</strong></h1>
                <br>
                <!-- On ajoute l'attribut enctype pour uploader des fichiers -->
                <form class = "form" role = "form" action = "delete.php" method = "post">
                    <input type="hidden" name="id" value="<?= $id ?>" />
                    <p class="alert alert-warning">Êtes vous sûr de vouloir supprimer?</p>
                    <div class="form-action">
                        <button type="submit" class="btn btn-warning">Oui</button>
                        <a class="btn btn-default" href="index.php">Non</a>
                    </div>
                </form>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="assets/js/script.js"></script>
    </body>

</html>