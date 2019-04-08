<?php
require 'database.php';

// Si id existe alors on le stocke dans $id
if (!empty($_GET['id'])) {
    $id = checkInput($_GET['id']);
}

// On initialise les variables
$nameError = $descriptionError = $priceError = $categoryError = $imageError = $name = $description = $price = $category = $image = "";
$imagePath = '';

// Si on soumet le formulaire, on entre dans la condition
if (!empty($_POST)) {
    // On stocke les valeurs
    $name = checkInput($_POST['name']);
    $description = checkInput($_POST['description']);
    $price = checkInput($_POST['price']);
    $category = checkInput($_POST['category']);
    $image = checkInput($_FILES["image"]["name"]);
    $imagePath = '/assets/images/' . basename($image);
    $imageExtension = pathinfo($imagePath, PATHINFO_EXTENSION);
    $isSuccess = true;

    // Si le formulaire n'est pas correctement rempli, on ne le valide pas et on affiche les messages d'erreurs
    if (empty($name)) {
        $nameError = 'Ce champ ne peut pas être vide';
        $isSuccess = false;
    }
    if (empty($description)) {
        $descriptionError = 'Ce champ ne peut pas être vide';
        $isSuccess = false;
    }
    if (empty($price)) {
        $priceError = 'Ce champ ne peut pas être vide';
        $isSuccess = false;
    }

    if (empty($category)) {
        $categoryError = 'Ce champ ne peut pas être vide';
        $isSuccess = false;
    }
    if (empty($image)) {
        // le input file est vide, ce qui signifie que l'image n'a pas été updated
        $isImageUpdated = false;
    } else {
        $isImageUpdated = true;
        $isUploadSuccess = true;
        if ($imageExtension != "jpg" && $imageExtension != "png" && $imageExtension != "jpeg" && $imageExtension != "gif") {
            $imageError = "Les fichiers autorises sont: .jpg, .jpeg, .png, .gif";
            $isUploadSuccess = false;
        }
        if (file_exists($imagePath)) {
            $imageError = "Le fichier existe deja";
            $isUploadSuccess = false;
        }
        if ($_FILES["image"]["size"] > 500000) {
            $imageError = "Le fichier ne doit pas depasser les 500KB";
            $isUploadSuccess = false;
        }
        if ($isUploadSuccess) {
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
                $imageError = "Il y a eu une erreur lors de l'upload";
                $isUploadSuccess = false;
            }
        }
    }

    if (($isSuccess && $isImageUpdated && $isUploadSuccess) || ($isSuccess && !$isImageUpdated)) {
        $db = Database::connect();
        if ($isImageUpdated) {
            $statement = $db->prepare("UPDATE `items`  SET `name` = ?, `description` = ?, `price` = ?, `category` = ?, `image` = ? WHERE `id` = ?");
            $statement->execute(array($name, $description, $price, $category, $image, $id));
        } else {
            $statement = $db->prepare("UPDATE `items`  SET `name` = ?, `description` = ?, `price` = ?, `category` = ? WHERE `id` = ?");
            $statement->execute(array($name, $description, $price, $category, $id));
        }
        Database::disconnect();
        header("Location: index.php");
    } else if ($isImageUpdated && !$isUploadSuccess) {
        $db = Database::connect();
        $statement = $db->prepare("SELECT `image` FROM `items` WHERE `id` = ?");
        $statement->execute(array($id));
        $item = $statement->fetch();
        $image = $item['image'];
        Database::disconnect();
    }
} else {
    $db = Database::connect();
    $statement = $db->prepare("SELECT * FROM `items` WHERE `id` = ?");
    $statement->execute(array($id));
    $item = $statement->fetch();
    $name = $item['name'];
    $description = $item['description'];
    $price = $item['price'];
    $category = $item['category'];
    $image = $item['image'];
    Database::disconnect();
}

function checkInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
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
                    <h1><strong>Modifier un item</strong></h1>
                    <br>
                    <!-- On ajoute l'attribut enctype pour uploader des fichiers -->
                    <form class="form" action="<?= 'update.php?id=' . $id; ?>" role="form" method="post" enctype="multipart/form-data">
                        <div class = "form-group">
                            <label for = "name">Nom:</label>
                            <input type = "text" class = "form-control" id = "name" name = "name" placeholder = "Nom" value="<?= $name ?>" />
                            <span class = "help-inline"><?= $nameError ?></span>
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <input type="text" class="form-control" id="description" name="description" placeholder="Description" value="<?= $description ?>" />
                            <span class="help-inline"><?= $descriptionError ?></span>
                        </div>
                        <div class="form-group">
                            <label for="price">Prix: (en €)</label>
                            <!-- On utilise l'attribut step pour augmenter ou diminuer la valeur du prix -->
                            <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="Prix" value="<?= $price ?>" />
                            <span class="help-inline"><?= $priceError ?></span>

                        </div>
                        <div class="form-group">
                            <label for="category">Catégorie:</label>
                            <select class="form-control" id="category" name="category">
                                <?php
                                // On se connecte à la database pour aller chercher les noms et les id de la table categories
                                $db = Database::connect();

                                // On récupère toutes les valeurs et on les stocke dans la variable php $row
                                foreach ($db->query('SELECT * FROM `categories`') as $row) {
                                    if ($row['id'] == $category) {
                                        ?>
                                        <!-- On affiche l'id de la category que l'on veut modifier sur la page update.php -->
                                        <option selected="selected" value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                    <?php } else { ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                        <?php
                                    }
                                }
                                // On se deconnecte
                                Database::disconnect();
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Image:</label>
                            <p><?= $image ?></p>
                            <label for="image">Sélectionner une image:</label>
                            <input type="file" class="form-control" id="image" name="image" />
                            <span class="help-inline"><?= $imageError ?></span>
                        </div>
                        <br>
                        <div class="form-action">
                            <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-pencil">Modifier</span></button>
                            <a class="btn btn-primary" href="index.php"><span class="glyphicon glyphicon-arrow-left"></span> Retour</a>
                        </div>
                    </form>
                </div>
                <div class="col-sm-6 site">
                    <div class="thumbnail">
                        <img src="<?= '/assets/images/' . $image; ?>" alt="..."/>
                        <div class="price"><?= number_format((float) $price, 2, '.', '') . ' €'; ?></div>
                        <div class="caption">
                            <h4><?= $name; ?></h4>
                            <p><?= $description; ?></p>
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