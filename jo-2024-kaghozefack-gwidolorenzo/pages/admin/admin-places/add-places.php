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
    $nomLieu = filter_input(INPUT_POST, 'nomLieu', FILTER_SANITIZE_STRING);
    $adresseLieu = filter_input(INPUT_POST, 'adresseLieu', FILTER_SANITIZE_STRING);
    $cpLieu = filter_input(INPUT_POST, 'cpLieu', FILTER_SANITIZE_STRING);
    $villeLieu = filter_input(INPUT_POST, 'villeLieu', FILTER_SANITIZE_STRING);


    // Vérifiez si le nom du lieu est vide
    if (empty($nomLieu) || empty($adresseLieu) || empty($cpLieu) || empty($villeLieu)) {
        $_SESSION['error'] = "Please fill all fields.";
        header("Location: add-lieu.php");
        exit();
    }

    try {
        // Vérifiez si le lieu existe déjà
        $queryCheck = "SELECT id_lieu FROM LIEU WHERE cp_lieu = :cpLieu";
        $statementCheck = $connexion->prepare($queryCheck);
        $statementCheck->bindParam(":cpLieu", $cpLieu, PDO::PARAM_STR);
        $statementCheck->execute();

        if ($statementCheck->rowCount() > 0) {
            $_SESSION['error'] = "Le lieu existe déjà.";
            header("Location: add-lieu.php");
            exit();
        } else {
            // Requête pour ajouter un lieu
            $query = "INSERT INTO LIEU (nom_lieu, adresse_lieu, cp_lieu, ville_lieu) VALUES (:nomLieu, :adresseLieu, :cpLieu, :villeLieu)";
            $statement = $connexion->prepare($query);
            $statement->bindParam(":nomLieu", $nomLieu, PDO::PARAM_STR);
            $statement->bindParam(":adresseLieu", $adresseLieu, PDO::PARAM_STR);
            $statement->bindParam(":cpLieu", $cpLieu, PDO::PARAM_STR);
            $statement->bindParam(":villeLieu", $villeLieu, PDO::PARAM_STR);

            // Exécutez la requête
            var_dump($nomLieu, $adresseLieu, $cpLieu, $villeLieu);
            if ($statement->execute()) {
                $_SESSION['success'] = "Le Lieu a été ajouté avec succès.";
                header("Location: manage-places.php");
                exit();
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout du Lieu. " . print_r($statement->errorInfo(), true);
                header("Location: add-lieu.php");
                exit();
            }

        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: add-lieu.php");
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
    <title>Ajouter un Lieu - Jeux Olympiques 2024</title>

</head>

<body>
    <header>
        <nav class="adminNav">
            <!-- Menu vers les pages sports, events, et results -->
            <ul class="menu">
            <li><a href="../admin.php">Accueil Administration</a></li>
                <li><a href="../admint-sport/manage-sports.php">Gestion Sports</a></li>
                <li><a href="manage-places.php">Gestion Lieux</a></li>
                <li><a href="../admin-events/manage-events.php">Gestion Calendrier</a></li>
                <li><a href="../admin-countries/manage-countries.php">Gestion Pays</a></li>
                <li><a href="../admin-gender/manage-gender.php">Gestion Genres</a></li>
                <li><a href="../admin-athletes/manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="../admin-results/manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>

            </ul>
        </nav>
    </header>
    <main>
        <figure>
            <img class="small" src="../../../img/cutLogo-jo-2024.png" alt="logo jeux olympiques 2024">
            <h1>Ajouter un Lieu</h1>
        </figure>

        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="add-lieu.php" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter ce lieu?')">
            <label for=" nomLieu">Nom du Lieu :</label>
            <input type="text" name="nomLieu" id="nomLieu" required>

            <label for=" adresseLieu">Adresse du Lieu :</label>
            <input type="text" name="adresseLieu" id="adresseLieu" required>

            <label for=" cpLieu">Code postale :</label>
            <input type="text" name="cpLieu" id="cpLieu" required>

            <label for=" VilleLieu">Ville du Lieu :</label>
            <input type="text" name="villeLieu" id="villeLieu" required>

            <input type="submit" value="Ajouter le Lieu">
        </form>
        <p class="paragraph-link">
            <a class="link-home" href="manage-places.php">Retour</a>
        </p>
    </main>
    <footer>

        <a href="https://cdc-jo-nkh.netlify.app/" target="blank">Cahier de charge</a>
        <a href="https://nawafkh.webflow.io/" target="blank">Portfolio</a>
    </footer>

</body>

</html>
