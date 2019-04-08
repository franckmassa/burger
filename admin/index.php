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
                <h1><strong>Liste des items</strong> <a href="insert.php" class="btn btn-success btn-lg"><span class="glyphicon glyphicon-plus"></span> Ajouter</a></h1>
                <!-- On utilise la class table-striped pour griser une ligne sur deux -->
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Prix</th>
                            <th>Catégorie</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // On requiert la page de la class database
                        require 'database.php';
                        // On stocke la connexion dans la variable $db
                        $db = Database::connect();
                        // On écrit la requête pour afficher les données et on utilise un alias car il y a deux name
                        $statement = $db->query('SELECT `items`.`id`,`items`.`name`,`items`.`description`,`items`.`price`,`categories`.`name` AS `category`
                        FROM `items`
                        LEFT JOIN `categories`
                        ON `items`.`category` = `categories`.`id`
                        ORDER BY `items`.`id` DESC');

                        while ($item = $statement->fetch()) {
                            echo '<tr>';
                            echo '<td>' . $item['name'] . '</td>';
                            echo '<td>' . $item['description'] . '</td>';
                            // On formate le prix 2 chiffres après la virgule
                            echo '<td>' . number_format((float) $item['price'], 2, '.', '') . '</td>';
                            echo '<td>' . $item['category'] . '</td>';
                            echo '<td width = 300>';
                            echo ' <a class = "btn btn-default" href = "view.php?id= ' . $item['id'] . '"><span class = "glyphicon glyphicon-eye-open"></span> Voir</a>';
                            echo ' <a class = "btn btn-primary" href = "update.php?id= ' . $item['id'] . '"><span class = "glyphicon glyphicon-pencil"></span> Modifier</a>';
                            echo ' <a class = "btn btn-danger" href = "delete.php?id= ' . $item['id'] . '"><span class = "glyphicon glyphicon-remove"></span> Supprimer</a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        // On se deconnecte de la database
                        Database::disconnect();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>












        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <script src = "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="assets/js/script.js"></script>
    </body>

</html>