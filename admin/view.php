<?php
// On requiert le fichier class Database
require 'database.php';

// Si l'id existe alors on nettoie les champs avec la fonction checkInput et on récupère l'id
if (!empty($_GET['id'])) {
    $id = checkInput($_GET['id']);
}
// On se connecte à la database et on stocke dans $db
$db = Database::connect();

// On prerpare la requête et on la stocke dans $statement
$statement = $db->prepare('SELECT `items`.`id`,`items`.`name`,`items`.`description`,`items`.`price`, `items`.`image`,`categories`.`name` AS `category`
                        FROM `items`
                        LEFT JOIN `categories`
                        ON `items`.`category` = `categories`.`id`
                        WHERE `items`.`id` = ?');

// On execute la requête
$statement->execute(array($id));
// On récupère la ligne
$item = $statement->fetch();
// On se deconnecte de la database
Database::disconnect();

// On déclare la fonction checkInput pour nettoyer les champs inputs
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
                <div class="col-sm-6">
                    <h1><strong>Voir un item</strong></h1>
                    <br>
                    <form>
                        <div class="form-group">
                            <label>Nom:</label><?= ' ' . $item['name']; ?>
                        </div>
                        <div class="form-group">
                            <label>Description:</label><?= ' ' . $item['description']; ?>
                        </div>
                        <div class="form-group">
                            <label>Prix:</label><?= ' ' . number_format((float) $item['price'], 2, '.', '') . ' €'; ?>
                        </div>
                        <div class="form-group">
                            <label>Catégorie:</label><?= ' ' . $item['category']; ?>
                        </div>
                        <div class="form-group">
                            <label>Nom:</label><?= ' ' . $item['image']; ?>
                        </div>
                    </form>
                    <br>
                    <div class="form-action">
                        <a class="btn btn-primary" href="index.php"><span class="glyphicon glyphicon-arrow-left"></span> Retour</a>
                    </div>
                </div>
                <div class="col-sm-6 site">
                    <div class="thumbnail">
                        <img src="<?= '/assets/images/' . $item['image']; ?>" alt="..."/>
                        <div class="price"><?= number_format((float) $item['price'], 2, '.', '') . ' €'; ?></div>
                        <div class="caption">
                            <h4><?= $item['name']; ?></h4>
                            <p><?= $item['description']; ?></p>
                            <a href="#" class="btn btn-order" role="button"><span class="glyphicon glyphicon-shopping-cart"></span> Commander</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="assets/js/script.js"></script>
    </body>

</html>