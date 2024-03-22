<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST")
 {
    // Assurez-vous d'obtenir des données sécurisées et filtrées
    $idAthlete = filter_input(INPUT_POST, 'idAthlete', FILTER_SANITIZE_NUMBER_INT);
    $idEpreuve = filter_input(INPUT_POST, 'idEpreuve', FILTER_SANITIZE_NUMBER_INT);
    $resultat = filter_input(INPUT_POST, 'resultat', FILTER_SANITIZE_STRING);


    // Vérifiez si le nom du resultat est vide
    if (empty($nomresultat)) {
        $_SESSION['error'] = "Le nom du resultat ne peut pas être vide.";
        header("Location: add-resultat.php");
        exit();
    }

    try {
        // Vérifiez si le sport existe déjà
        $queryCheck = "SELECT id_sport FROM SPORT WHERE nom_sport = :nomSport";
        $statementCheck = $connexion->prepare($queryCheck);
        $statementCheck->bindParam(":nomSport", $nomSport, PDO::PARAM_STR);
        $statementCheck->execute();

        if ($statementCheck->rowCount() > 0) {
            $_SESSION['error'] = "Le sport existe déjà.";
            header("Location: add-sport.php");
            exit();
        } else {

            // Requête pour ajouter un sport
            $queryInsert = "INSERT INTO PARTICIPER (id_athlete, id_epreuve, resultat) VALUES (:idAthlete, :idEpreuve, :resultat)";
            $statementInsert = $connexion->prepare($queryInsert);
            $statementInsert->bindParam(":idAthlete", $idAthlete, PDO::PARAM_INT);
            $statementInsert->bindParam(":idEpreuve", $idEpreuve, PDO::PARAM_INT);
            $statementInsert->bindParam(":resultat", $resultat, PDO::PARAM_STR);

            // Exécutez la requête
            if ($statement->execute()) {
                $_SESSION['success'] = "Le résultat a été ajouté avec succès.";
                header("Location: manage-results.php");
                exit();
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout du résultat.";
                header("Location: add-result.php");
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
                <li><a href="../admint-sport/manage-sports.php">Gestion Sports</a></li>
                <li><a href="../admin-places/manage-places.php">Gestion Lieux</a></li>
                <li><a href="../admin-events/manage-events.php">Gestion Calendrier</a></li>
                <li><a href="../admin-countries/manage-countries.php">Gestion Pays</a></li>
                <li><a href="../admin-gender/manage-gender.php">Gestion Genres</a></li>
                <li><a href="../admin-athletes/manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Ajouter un Résultat</h1>

        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>

        <form action="add-result.php" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter ce résultat?')">
            <label for="idAthlete">Athlète :</label>
            <select name="idAthlete" id="idAthlete" required>
                <?php
                $queryAthletes = "SELECT * FROM ATHLETE";
                $statementAthletes = $connexion->query($queryAthletes);
                while ($rowAthlete = $statementAthletes->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$rowAthlete['id_athlete']}'>{$rowAthlete['nom_athlete']} {$rowAthlete['prenom_athlete']}</option>";
                }
                ?>
            </select>

            <label for="idEpreuve">Épreuve :</label>
            <select name="idEpreuve" id="idEpreuve" required>
                <?php
                $queryEpreuves = "SELECT * FROM EPREUVE";
                $statementEpreuves = $connexion->query($queryEpreuves);
                while ($rowEpreuve = $statementEpreuves->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$rowEpreuve['id_epreuve']}'>{$rowEpreuve['nom_epreuve']}</option>";
                }
                ?>
            </select>

            <label for="resultat">Résultat :</label>
            <input type="text" name="resultat" id="resultat" required>

            <input type="submit" value="Ajouter le Résultat">
        </form>

        <p class="paragraph-link">
            <a class="link-home" href="manage-results.php">Retour à la gestion des résultats</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>

</body>

</html>