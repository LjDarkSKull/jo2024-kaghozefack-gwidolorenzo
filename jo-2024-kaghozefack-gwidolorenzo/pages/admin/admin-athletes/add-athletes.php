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
    $nomAthlete = filter_input(INPUT_POST, 'nomAthlete', FILTER_SANITIZE_STRING);
    $prenomAthlete = filter_input(INPUT_POST, 'prenomAthlete', FILTER_SANITIZE_STRING);
    $idPays = filter_input(INPUT_POST, 'idPays', FILTER_SANITIZE_NUMBER_INT);
    $idGenre = filter_input(INPUT_POST, 'idGenre', FILTER_SANITIZE_NUMBER_INT);


    // Vérifiez si le nom du lieu est vide
    if (empty($nomAthlete) || empty($prenomAthlete) || empty($idPays) || empty($idGenre)) {
        $_SESSION['error'] = "Please fill all fields.";
        header("Location: modify-athletes.php=$id_athlete");
        exit();
    }
    try {
        // Vérifiez si le lieu existe déjà
        $queryCheck = "SELECT id_athlete FROM ATHLETE WHERE nom_athlete = :nomAthlete AND prenom_athlete = :prenomAthlete";
        $statementCheck = $connexion->prepare($queryCheck);
        $statementCheck->bindParam(":nomAthlete", $nomAthlete, PDO::PARAM_STR);
        $statementCheck->bindParam(":prenomAthlete", $prenomAthlete, PDO::PARAM_STR);
        $statementCheck->execute();

        if ($statementCheck->rowCount() > 0) {
            $_SESSION['error'] = "L'athlete existe déjà.";
            header("Location: modify-athletes.php");
            exit();
        } else {

            // Requête pour ajouter un lieu
            $query = "INSERT INTO ATHLETE (nom_athlete, prenom_athlete, id_pays, id_genre) VALUES (:nomAthlete, :prenomAthlete, :idPays, :idGenre)";
            $statement = $connexion->prepare($query);
            $statement->bindParam(":nomAthlete", $nomAthlete, PDO::PARAM_STR);
            $statement->bindParam(":prenomAthlete", $prenomAthlete, PDO::PARAM_STR);
            $statement->bindParam(":idPays", $idPays, PDO::PARAM_INT);
            $statement->bindParam(":idGenre", $idGenre, PDO::PARAM_INT);

            // Exécutez la requête
            var_dump($nomAthlete, $prenomAthlete, $idPays, $idGenre);
            if ($statement->execute()) {
                $_SESSION['success'] = "L'Athlete a été ajouté avec succès.";
                header("Location: manage-athletes.php");
                exit();
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout du Athlete. " . print_r($statement->errorInfo(), true);
                header("Location: add-athletes.php");
                exit();
            }

        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: add-athletes.php");
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
    <title>Ajouter un Athlete - Jeux Olympiques 2024</title>

</head>

<body>
    <header>
        <nav class="adminNav">
            <!-- Menu vers les pages sports, events, et results -->
            <ul class="menu">
            <li><a href="../admin.php">Accueil Administration</a></li>
                <li><a href="../admin-sports/manage-sports.php">Gestion Sports</a></li>
                <li><a href="../admin-places/manage-places.php">Gestion Lieux</a></li>
                <li><a href="../admin-events/manage-events.php">Gestion Calendrier</a></li>
                <li><a href="../admin-countries/manage-countries.php">Gestion Pays</a></li>
                <li><a href="../admin-gender/manage-gender.php">Gestion Genres</a></li>
                <li><a href="manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="../admin-results/manage-results.php">Gestion Résultats</a></li>
                <li><a href="../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <figure>
            <img class="small" src="../../../img/cutLogo-jo-2024.png" alt="logo jeux olympiques 2024">
            <h1>Ajouter un Athlete</h1>
        </figure>

        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="add-athletes.php" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter ce Athlete?')">
            <label for="nomAthlete">Nom d'Athlete :</label>
            <input type="text" name="nomAthlete" id="nomAthlete" required>

            <label for="prenomAthlete">Prenom d'Athlete:</label>
            <input type="text" name="prenomAthlete" id="prenomAthlete" required>

            <label for="idPays">Pays :</label>
            <?php
            try {
                $statement = $connexion->query("SELECT * FROM PAYS");
                if ($statement->rowCount() > 0) {
                    echo "<select name='idPays' onfocus='this.size=2;' onblur='this.size=1;' onchange='this.size=1; this.blur();'>";
                    echo '<option value="">--Choose--</option>';
                    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . $row["id_pays"] . "'>" . $row["nom_pays"] . "</option>";
                    }
                    echo "</select><br>";
                } else {
                    echo "No database found";
                }
            } catch (mysqli_sql_exception $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>

            <label for="idGenre">Genre :</label>
            <?php
            try {
                $statement = $connexion->query("SELECT * FROM GENRE");
                if ($statement->rowCount() > 0) {
                    echo "<select name='idGenre' onfocus='this.size=2;' onblur='this.size=1;' onchange='this.size=1; this.blur();'>";
                    echo '<option value="">--Choose--</option>';
                    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . $row["id_genre"] . "'>" . $row["nom_genre"] . "</option>";
                    }
                    echo "</select><br>";
                } else {
                    echo "No database found";
                }
            } catch (mysqli_sql_exception $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
            <input type="submit" value="Ajouter l'Athlete">
        </form>
        <p class="paragraph-link">
            <a class="link-home" href="manage-athletes.php">Retour</a>
        </p>
    </main>
    <footer>

        <a href="https://cdc-jo-nkh.netlify.app/" target="blank">Cahier de charge</a>
        <a href="https://nawafkh.webflow.io/" target="blank">Portfolio</a>
    </footer>

</body>

</html>