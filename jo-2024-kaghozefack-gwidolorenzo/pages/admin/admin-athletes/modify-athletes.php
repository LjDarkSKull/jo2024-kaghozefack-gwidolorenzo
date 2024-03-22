<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'ID du athlete est fourni dans l'URL
if (!isset($_GET['id_athlete'])) {
    $_SESSION['error'] = "ID du athlete manquant.";
    header("Location: manage-athletes.php");
    exit();
}

$id_athlete = filter_input(INPUT_GET, 'id_athlete', FILTER_VALIDATE_INT);

// Vérifiez si l'ID du athlete est un entier valide
if (!$id_athlete && $id_athlete !== 0) {
    $_SESSION['error'] = "ID du athlete invalide.";
    header("Location: manage-athletes.php");
    exit();
}

// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous d'obtenir des données sécurisées et filtrées
    $nomAthlete = filter_input(INPUT_POST, 'nomAthlete', FILTER_SANITIZE_STRING);
    $prenomAthlete = filter_input(INPUT_POST, 'prenomAthlete', FILTER_SANITIZE_STRING);
    $idPays = filter_input(INPUT_POST, 'idPays', FILTER_SANITIZE_NUMBER_INT);
    $idGenre = filter_input(INPUT_POST, 'idGenre', FILTER_SANITIZE_NUMBER_INT);

    // Vérifiez si les champs du athlete est vide
    if (empty($nomAthlete) || empty($prenomAthlete) || empty($idPays) || empty($idGenre)) {
        $_SESSION['error'] = "Please fill all fields.";
        header("Location: modify-athletes.php=$id_athlete");
        exit();
    }
    try {
        // // Vérifiez si le athlete existe déjà
        // $queryCheck = "SELECT id_athlete FROM ATHLETE WHERE nom_athlete = :nomAthlete AND prenom_athlete = :prenomAthlete";
        // $statementCheck = $connexion->prepare($queryCheck);
        // $statementCheck->bindParam(":nomAthlete", $nomAthlete, PDO::PARAM_STR);
        // $statementCheck->bindParam(":prenomAthlete", $prenomAthlete, PDO::PARAM_STR);
        // $statementCheck->execute();

        // if ($statementCheck->rowCount() > 0) {
        //     $_SESSION['error'] = "L'athlete existe déjà.";
        //     header("Location: modify-athletes.php");
        //     exit();
        // } else {
            // Requête pour mettre à jour le athlete
            $query = "UPDATE ATHLETE SET nom_athlete = :nomAthlete, prenom_athlete = :prenomAthlete, id_pays = :idPays, id_genre = :idGenre WHERE id_athlete = :idAthlete";
            $statement = $connexion->prepare($query);
            $statement->bindParam(":nomAthlete", $nomAthlete, PDO::PARAM_STR);
            $statement->bindParam(":prenomAthlete", $prenomAthlete, PDO::PARAM_STR);
            $statement->bindParam(":idPays", $idPays, PDO::PARAM_INT);
            $statement->bindParam(":idGenre", $idGenre, PDO::PARAM_INT);
            $statement->bindParam(":idAthlete", $id_athlete, PDO::PARAM_INT);
        // }
        // Exécutez la requête
        if ($statement->execute()) {
            $_SESSION['success'] = "L'Athlete a été modifié avec succès.";
            header("Location: manage-athletes.php");
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de la modification du Athlete.";
            header("Location: modify-athletes.php?id_athlete=$id_athlete");
            exit();
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: modify-athletes.php?id_athlete=$id_athlete");
        exit();
    }
}

// Récupérez les informations du athlete pour affichage dans le formulaire
try {
    $query = "SELECT * FROM ATHLETE 
              INNER JOIN PAYS ON ATHLETE.id_pays = PAYS.id_pays 
              INNER JOIN GENRE ON ATHLETE.id_genre = GENRE.id_genre
              WHERE id_athlete = :idAthlete";

    $statement = $connexion->prepare($query);
    $statement->bindParam(":idAthlete", $id_athlete, PDO::PARAM_INT);
    $statement->execute();

    if ($statement->rowCount() > 0) {
        $Athlete = $statement->fetch(PDO::FETCH_ASSOC);
    } else {
        $_SESSION['error'] = "Athlete non trouvé.";
        header("Location: manage-athletes.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
    header("Location: manage-athletes.php");
    exit();
}
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
    <title>Modifier une Athlete - Jeux Olympiques 2024</title>

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
            <h1>Modifier un Athlete</h1>
        </figure>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="modify-athletes.php?id_athlete=<?php echo $id_athlete; ?>" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir modifier ce athlete?')">
            <label for=" nomLieu">Nom du Athlete :</label>
            <input type="text" name="nomAthlete" id="nomAthlete"
                value="<?php echo htmlspecialchars($Athlete['nom_athlete']); ?>" required>

            <label for="prenomAthlete">Prenom du Athlete :</label>
            <input type="text" name="prenomAthlete" id="prenomAthlete"
                value="<?php echo htmlspecialchars($Athlete['prenom_athlete']); ?>" required>

            <label for="idPays">Pays :</label>
            <?php
            try {
                $statement = $connexion->query("SELECT * FROM PAYS");
                if ($statement->rowCount() > 0) {
                    echo "<select name='idPays' onfocus='this.size=2;' onblur='this.size=1;' onchange='this.size=1; this.blur();'>";
                    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                        $selectedPays = isset($_POST['idPays']) ? $_POST['idPays'] : $Athlete['id_pays'];
                        echo '<option value="' . htmlspecialchars($row["id_pays"]) . '" ' . ($row["id_pays"] == $selectedPays ? 'selected' : '') . '>' . htmlspecialchars($row["nom_pays"]) . '</option>';
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
                    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                        $selectedGenre = isset($_POST['idGenre']) ? $_POST['idGenre'] : $Athlete['id_genre'];
                        echo '<option value="' . htmlspecialchars($row["id_genre"]) . '" ' . ($row["id_genre"] == $selectedGenre ? 'selected' : '') . '>' . htmlspecialchars($row["nom_genre"]) . '</option>';
                    }
                    echo "</select><br>";
                } else {
                    echo "No database found";
                }
            } catch (mysqli_sql_exception $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
            <input type="submit" value="Modifier l'Athlete">
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
