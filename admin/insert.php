<?php
require 'database.php';
// on initialise les variables à rien pour le premier passage
$nameError = $descriptionError = $priceError = $categoryError = $imageError = $name = $description = $price = $category = $image = ' ';
$imagePath = '';

// On soumet le formulaire et on vérifie que la super global $_POST n'est pas vide
if (!empty($_POST)) {
    // on stocke les valeurs dans les variables
    $name = checkInput($_POST['name']);
    $description = checkInput($_POST['description']);
    $price = checkInput($_POST['price']);
    $category = checkInput($_POST['category']);

    // On utilise la super global $_FILES pour récupérer le nom de l'image
    $image = checkInput($_FILES['image']['name']);
    // On récupèreble chemin du dossier de l'image
    $imagepath = '/assets/images/' . basename($image);
    // On récupère le nom de l'extension de l'image
    $imageExtension = pathinfo($imagepath, PATHINFO_EXTENSION);

    // On regarde si le formulaire a été rempli avec succès
    $isSuccess = true;
    // On regarde si le fichier a été uploader avec succès
    $isUploadSuccess = false;

    // Vérifications pour la validation du formulaire
    if (empty($name)) {
        $nameError = 'Ce champ doit être rempli';
        $isSuccess = false;
    }
    if (empty($description)) {
        $descriptionError = 'Ce champ doit être rempli';
        $isSuccess = false;
    }
    if (empty($price)) {
        $priceError = 'Ce champ doit être rempli';
        $isSuccess = false;
    }
    if (empty($category)) {
        $categoryError = 'Vous devez choisir une catégorie';
        $isSuccess = false;
    }

    // Vérification pour l'image
    if (empty($image)) {
        $imageError = 'Ce champ ne peut pas être vide';
        $isSuccess = false;
    } else {
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
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
                $imageError = "Il y a eu une erreur lors de l'upload";
                $isUploadSuccess = false;
            }
        }
    }

    // Si le formulaire a été soumis avec succès, on se connecte à la database
    if ($isSuccess && $isUploadSuccess) {
        $db = Database::connect();
        // On prepare et on stocke la requête dans la variable $statement
        $statement = $db->prepare("INSERT INTO `items` (`name`,`description`,`price`,`category`,`image`) values(?, ?, ?, ?, ?)");
        // On execute la requête
        $statement->execute(array($name, $description, $price, $category, $image));
        // On se deconnecte de la database
        Database::disconnect();
        // On redirige vers la page index.php
        header("Location: index.php");
    }
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

                <h1><strong>Ajouter un item</strong></h1>
                <br>
                <!-- On ajoute l'attribut enctype pour uploader des fichiers -->
                <form class = "form" role = "form" action = "insert.php" method = "post" enctype = "multipart/form-data">
                    <div class = "form-group">
                        <label for = "name">Nom:</label>
                        <input type = "text" class = "form-control" id = "name" name = "name" placeholder = "Nom" value = "<?= $name ?>" />
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
                                ?>
                                <!-- On selectionne l'id de  la table categories et on affiche le nom de la table categories -->
                                <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                <?php
                            }
                            // On se deconnecte
                            Database::disconnect();
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="image">Sélectionner une image:</label>
                        <input type="file" class="form-control" id="image" name="image" />
                        <span class="help-inline"><?= $imageError ?></span>
                    </div>
                    <br>
                    <div class="form-action">
                        <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-pencil">Ajouter</span></button>
                        <a class="btn btn-primary" href="index.php"><span class="glyphicon glyphicon-arrow-left"></span> Retour</a>
                    </div>
                </form>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="assets/js/script.js"></script>
    </body>

</html>