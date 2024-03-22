<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous d'obtenir des données sécurisées et filtrées
    $nom_users = filter_input(INPUT_POST, 'nom_users', FILTER_SANITIZE_STRING);
    $prenom_users = filter_input(INPUT_POST, 'prenom_users', FILTER_SANITIZE_STRING);
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

// LORENZO TO BE KNOW!!!!!!!!

    // Vérifiez si le nom du users est vide
    if (empty($nom_users)) {
        $_SESSION['error'] = "Le nom du users ne peut pas être vide.";
        header("Location: add-users.php");
        exit();
    }

    try {
        // Vérifiez si le users existe déjà
        $queryCheck = "SELECT id_users FROM users WHERE nom_users = :nomusers";
        $statementCheck = $connexion->prepare($queryCheck);
        $statementCheck->bindParam(":nomusers", $nom_users, PDO::PARAM_STR);
        $statementCheck->execute();

        if ($statementCheck->rowCount() > 0) {
            $_SESSION['error'] = "Le users existe déjà.";
            header("Location: add-users.php");
            exit();
        } else {

            // Hachage du mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Requête pour ajouter un users
            $query = "INSERT INTO utilisateur (nom_users) VALUES (:nomusers)";
            // VALUES (:nom_users, :prenom_users, :login, :hashedPassword)";

            // LORENZO LEARN THAT!!!!!!!!!
           
            $statement = $connexion->prepare($query);
            $statement->bindParam(":nom_users", $nom_users, PDO::PARAM_STR);
            $statement->bindParam(":prenom_users", $prenom_users, PDO::PARAM_STR);
            $statement->bindParam(":login", $login, PDO::PARAM_STR);
            $statement->bindParam(":hashedPassword", $hashedPassword, PDO::PARAM_STR);

            // Exécutez la requête
            if ($statement->execute()) {
                $_SESSION['success'] = "Le users a été ajouté avec succès.";
                header("Location: manage-users.php");
                exit();
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout du users.";
                header("Location: add-users.php");
                exit();
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: add-sport.php");
        exit();
    }
}
// Afficher les erreurs en PHP
// (fonctionne à condition d’avoir activé l’option en local)
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../css/normalize.css">
    <link rel="stylesheet" href="../../../css/styles-computer.css">
    <link rel="stylesheet" href="../../../css/styles-responsive.css">
    <link rel="shortcut icon" href="../../../img/favicon-jo-2024.ico" type="image/x-icon">
    <title>Ajouter un Sport - Jeux Olympiques 2024</title>
    <style>
        /* Ajoutez votre style CSS ici */
    </style>
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages sports, events, et results -->
            <ul class="menu">
                <li><a href="../admin.php">Accueil Administration</a></li>
                <li><a href="manage-sports.php">Gestion Sports</a></li>
                <li><a href="manage-places.php">Gestion Lieux</a></li>
                <li><a href="manage-events.php">Gestion Calendrier</a></li>
                <li><a href="manage-countries.php">Gestion Pays</a></li>
                <li><a href="manage-gender.php">Gestion Genres</a></li>
                <li><a href="manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="manage-results.php">Gestion Résultats</a></li>
                <li><a href="../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Ajouter un Users</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="add-users.php" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter ce users?')">
            <label for=" nom_users">Nom du users :</label>
            <input type="text" name="nom_users" id="nom_users" required>
            <label for="prenom_users">Prenom users :</label>
            <input type="text" name="prenom-users" id="prenom_users" required>
            <label for="login">Login :</label>
            <input type="text" name="login" id="login" required>
            <label for="password">Password :</label>
            <input type="password" name="password" id="password" required>
            <input type="submit" value="Ajouter le utilisateur">

            <!-- LORENZO TO BE LEARN -->


        </form>
        <p class="paragraph-link">
            <a class="link-home" href="manage-users.php">Retour à la gestion des sports</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>

</body>

</html>