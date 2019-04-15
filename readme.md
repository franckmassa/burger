1 - Créer un fichier database.php dans le dossier admin.
2 - Copier le code suivant dans le fichier database.php.

<?php

// On crée une class database
class Database {

// On utilise static car on utilise la classe directement et non une instance de class
    private static $dbHost = 'localhost';
    private static $dbName = 'restaurant';
    private static $dbUser = '';
    private static $dbUserPassword = '';
    // On déclare la variable $connexion pour stocker la connexion
    private static $connection = null;

    /* On crée une fonction pour la connexion en public qui est accessible de l'extérieur de la class database
      et on utilise des paramètres static qui appartiennent à la class et non à une instance de class
      On ajoutera SELF:: à tous les paramètres pour spécifier qu'ils sont static
     */

    public static function connect() {

        try {
            // Exemple code de base: $connection = new PDO('mysql:host=localhost;dbname=restaurant'; 'userName', 'userPassword');
            self::$connection = new PDO('mysql:host=' . self::$dbHost . ';dbname=' . self::$dbName, self::$dbUser, self::$dbUserPassword);
        } catch (PDOException $ex) {

            die($ex->getMessage());
        }
        // On retourne la connexion dans la varaible $connection
        return self::$connection;
    }

// On crée une fonction pour la déconnexion
    public static function disconnect() {
        self::$connection = null;
    }

}

// Bout de code pour tester la connexion:
// Database::connect();
// Taper l'url "restauBurger/admin/database.php. Il ne doit pas y avoir d'erreur sur la page
