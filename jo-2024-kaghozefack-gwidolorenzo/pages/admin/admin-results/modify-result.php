<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'ID du sport est fourni dans l'URL
if (!isset($_GET['id_resultat'])) {
    $_SESSION['error'] = "IDsport du sport manquant.";
    header("Location: manage-result.php");
    exit();
}

$id_result = filter_input(INPUT_GET, 'id_result', FILTER_VALIDATE_INT);

// Vérifiez si l'ID du result est un entier valide
if (!$id_result && $id_result !== 0) {
    $_SESSION['error'] = "ID du result invalide.";
    header("Location: manage-result.php");
    exit();
}

// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous d'obtenir des données sécurisées et filtrées
    $resultat = filter_input(INPUT_POST, 'resultat', FILTER_SANITIZE_STRING);

    // Vérifiez si le nom du resultat est vide
    if (empty($resultat)) {
        $_SESSION['error'] = "Le nom du resultat ne peut pas être vide.";
        header("Location: modify-resultat.php?id_resultat=$id_resultat");
        exit();
    }

    try {
        // Vérifiez si le resultat existe déjà
        $queryCheck = "SELECT id_resultat FROM SPORT WHERE nom_sport = :resultat AND id_sport <> :idSport";
        $statementCheck = $connexion->prepare($queryCheck);
        $statementCheck->bindParam(":resultat", $resultat, PDO::PARAM_STR);
        $statementCheck->bindParam(":idSport", $id_sport, PDO::PARAM_INT);
        $statementCheck->execute();

        if ($statementCheck->rowCount() > 0) {
            $_SESSION['error'] = "Le sport existe déjà.";
            header("Location: modify-sport.php?id_sport=$id_sport");
            exit();
        }

        // Requête pour mettre à jour le resultat
        $query = "UPDATE participer SET id_athlete = :idAthlete, id_epreuve = :idEpreuve, resultat = :resultatValue WHERE resultat = :resultat";
        $statement = $connexion->prepare($query);
        $statement->bindParam(":idAthlete", $idAthlete, PDO::PARAM_INT);
        $statement->bindParam(":idEpreuve", $idEpreuve, PDO::PARAM_INT);
        $statement->bindParam(":resultatValue", $resultatValue, PDO::PARAM_STR);
        $statement->bindParam(":resultat", $resultat, PDO::PARAM_STR);

        // Exécutez la requête
        if ($statement->execute()) {
            $_SESSION['success'] = "Le resultat a été modifié avec succès.";
            header("Location: manage-resultat.php");
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de la modification du resultat.";
            header("Location: modify-resultat.php?id_resultat=$id_resultat");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: modify-resultat.php?id_resultat=$id_resultat");
        exit();
    }
}

// Récupérez les informations du resultat pour affichage dans le formulaire
try {
    $query = "SELECT * FROM participer WHERE resultat = :resultat";
    $statement = $connexion->prepare($query);
    $statement->bindParam(":resultat", $resultat, PDO::PARAM_STR);
    $statement->execute();

        if ($statementSport->rowCount() > 0) {
        $resultat = $statementSport->fetch(PDO::FETCH_ASSOC);
    } else {
        $_SESSION['error'] = "resultat non trouvé.";
        header("Location: manage-resultats.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
    header("Location: manage-resultats.php");
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
    <title>Modifier un Sport - Jeux Olympiques 2024</title>
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
        <h1>Modifier un resultat</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
       <form action="modify-result.php?id_athlete=<?php echo $id_athlete; ?>" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir modifier ce résultat?')">
            <label for="resultat">Résultat de l'Athlète :</label>
            <input type="text" name="resultat" id="resultat" value="<?php echo htmlspecialchars($athlete['resultat']); ?>" required>
            <input type="submit" value="Modifier le Résultat">
        </form>
        <p class="paragraph-link">
            <a class="link-home" href="manage-sports.php">Retour à la gestion des sports</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>
</body>

</html>