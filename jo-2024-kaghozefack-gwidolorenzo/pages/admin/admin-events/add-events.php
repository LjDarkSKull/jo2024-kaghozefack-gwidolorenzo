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
    $nomEpreuve = filter_input(INPUT_POST, 'nomEpreuve', FILTER_SANITIZE_STRING);
    $dateEpreuve = filter_input(INPUT_POST, 'dateEpreuve', FILTER_SANITIZE_STRING);
    $heureEpreuve = filter_input(INPUT_POST, 'heureEpreuve', FILTER_SANITIZE_STRING);
    $idLieu = filter_input(INPUT_POST, 'idLieu', FILTER_SANITIZE_NUMBER_INT);
    $idSport = filter_input(INPUT_POST, 'idSport', FILTER_SANITIZE_NUMBER_INT);


    // Vérifiez si le nom du lieu est vide
    if (empty($nomEpreuve) || empty($dateEpreuve) || empty($heureEpreuve) || empty($idLieu) || empty($idSport)) {
        $_SESSION['error'] = "Please fill all fields.";
        header("Location: add-events.php");
        exit();
    }

    try {
        // Vérifiez si le lieu existe déjà
        $queryCheck = "SELECT id_epreuve FROM EPREUVE WHERE nom_epreuve = :nomEpreuve";
        $statementCheck = $connexion->prepare($queryCheck);
        $statementCheck->bindParam(":nomEpreuve", $nomEpreuve, PDO::PARAM_STR);
        $statementCheck->execute();

        if ($statementCheck->rowCount() > 0) {
            $_SESSION['error'] = "L'epreuve existe déjà.";
            header("Location: add-events.php");
            exit();
        } else {
            // Requête pour ajouter un lieu
            $query = "INSERT INTO EPREUVE (nom_epreuve, date_epreuve, heure_epreuve, id_lieu, id_sport) VALUES (:nomEpreuve, :dateEpreuve, :heureEpreuve, :idLieu, :idSport)";
            $statement = $connexion->prepare($query);
            $statement->bindParam(":nomEpreuve", $nomEpreuve, PDO::PARAM_STR);
            $statement->bindParam(":dateEpreuve", $dateEpreuve, PDO::PARAM_STR);
            $statement->bindParam(":heureEpreuve", $heureEpreuve, PDO::PARAM_STR);
            $statement->bindParam(":idLieu", $idLieu, PDO::PARAM_INT);
            $statement->bindParam(":idSport", $idSport, PDO::PARAM_INT);

            // Exécutez la requête
            var_dump($nomEpreuve, $dateEpreuve, $heureEpreuve, $idLieu, $idSport);
            if ($statement->execute()) {
                $_SESSION['success'] = "L' Epreuve a été ajouté avec succès.";
                header("Location: manage-events.php");
                exit();
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout du Epreuve. " . print_r($statement->errorInfo(), true);
                header("Location: add-events.php");
                exit();
            }

        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: add-events.php");
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
    <title>Ajouter un Epreuve - Jeux Olympiques 2024</title>

</head>

<body>
    <header>
        <nav class="adminNav">
            <!-- Menu vers les pages sports, events, et results -->
            <ul class="menu">
                <li><a href="../admin-sports/manage-sports.php">Gestion Sports</a></li>
                <li><a href="../admin-places/manage-places.php">Gestion Lieux</a></li>
                <li><a href="manage-events.php">Gestion Calendrier</a></li>
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
            <h1>Ajouter un Epreuve</h1>
        </figure>

        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="add-events.php" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter ce Epreuve?')">
            <label for="nomEpreuve">Nom d'Epreuve :</label>
            <input type="text" name="nomEpreuve" id="nomEpreuve" placeholder="Exemple" required>

            <label for="dateEpreuve">Date :</label>
            <input type="text" name="dateEpreuve" id="dateEpreuve" placeholder="AAAA-MM-JJ" required>

            <label for="heureEpreuve">Heure :</label>
            <input type="text" name="heureEpreuve" id="heureEpreuve" placeholder="00:00:00" required>

            <label for="idLieu">Lieu :</label>
            <?php
            try {
                $statement = $connexion->query("SELECT * FROM lieu");
                if ($statement->rowCount() > 0) {
                    echo "<select name='idLieu' onfocus='this.size=2;' onblur='this.size=1;' onchange='this.size=1; this.blur();'>";
                    echo '<option value="">--Choose--</option>';
                    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . $row["id_lieu"] . "'>" . $row["nom_lieu"] . "</option>";
                    }
                    echo "</select><br>";
                } else {
                    echo "No database found";
                }
            } catch (mysqli_sql_exception $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>

            <label for="idSport">Sport :</label>
            <?php
            try {
                $statement = $connexion->query("SELECT * FROM sport");
                if ($statement->rowCount() > 0) {
                    echo "<select name='idSport' onfocus='this.size=2;' onblur='this.size=1;' onchange='this.size=1; this.blur();'>";
                    echo '<option value="">--Choose--</option>';
                    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . $row["id_sport"] . "'>" . $row["nom_sport"] . "</option>";
                    }
                    echo "</select><br>";
                } else {
                    echo "No database found";
                }
            } catch (mysqli_sql_exception $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
            <input type="submit" value="Ajouter l'Epreuve">
        </form>
        <p class="paragraph-link">
            <a class="link-home" href="manage-events.php">Retour</a>
        </p>
    </main>
    <footer>

        <a href="https://cdc-jo-nkh.netlify.app/" target="blank">Cahier de charge</a>
        <a href="https://nawafkh.webflow.io/" target="blank">Portfolio</a>
    </footer>

</body>

</html>