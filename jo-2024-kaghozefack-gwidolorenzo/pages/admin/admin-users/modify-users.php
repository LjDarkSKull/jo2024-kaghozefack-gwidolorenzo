<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'users est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'ID du users est fourni dans l'URL
if (!isset($_GET['id_users'])) {
    $_SESSION['error'] = "ID du users manquant.";
    header("Location: manage-users.php");
    exit();
}

$id_users = filter_input(INPUT_GET, 'id_users', FILTER_VALIDATE_INT);

// Vérifiez si l'ID du users est un entier valide
if (!$id_users && $id_users !== 0) {
    $_SESSION['error'] = "ID du users invalide.";
    header("Location: manage-users.php");
    exit();
}

// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous d'obtenir des données sécurisées et filtrées
    $nom_users = filter_input(INPUT_POST, 'nom_users', FILTER_SANITIZE_STRING);
    $prenom_users = filter_input(INPUT_POST, 'prenom_users', FILTER_SANITIZE_STRING);
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Vérifiez si le nom du users est vide
    if (empty($nom_users)) {
        $_SESSION['error'] = "Le nom du users ne peut pas être vide.";
        header("Location: modify-users.php?id_users=$id_users");
        exit();
    }

    try {
        // Vérifiez si le users existe déjà
        $queryCheck = "SELECT id_users FROM users WHERE nom_users = :nom_users AND id_users <> :idusers";
        $statementCheck = $connexion->prepare($queryCheck);
        $statementCheck->bindParam(":nom_users", $nom_users, PDO::PARAM_STR);
        $statement->bindParam(":prenomUtilisateur", $prenom_users, PDO::PARAM_STR);
        $statement->bindParam(":login", $login, PDO::PARAM_STR);
        $statement->bindParam(":password", $password, PDO::PARAM_STR);
        $statementCheck->bindParam(":idusers", $id_users, PDO::PARAM_INT);
        $statementCheck->execute();

        if ($statementCheck->rowCount() > 0) {
            $_SESSION['error'] = "Le users existe déjà.";
            header("Location: modify-users.php?id_users=$id_users");
            exit();
        }

        // Requête pour mettre à jour le users
        $query = "UPDATE users SET nom_users = :nom_users WHERE id_users = :idusers";
        $statement = $connexion->prepare($query);
        $statement->bindParam(":nom_users", $nom_users, PDO::PARAM_STR);
        $statement->bindParam(":prenomUtilisateur", $prenom_users, PDO::PARAM_STR);
        $statement->bindParam(":login", $login, PDO::PARAM_STR);
        $statement->bindParam(":password", $password, PDO::PARAM_STR);
        $statement->bindParam(":idusers", $id_users, PDO::PARAM_INT);

        // Exécutez la requête
        if ($statement->execute()) {
            $_SESSION['success'] = "Le users a été modifié avec succès.";
            header("Location: manage-users.php");
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de la modification du users.";
            header("Location: modify-users.php?id_users=$id_users");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: modify-users.php?id_users=$id_users");
        exit();
    }
}

// Récupérez les informations du users pour affichage dans le formulaire
try {
    $queryUser = "SELECT nom_users, prenom_users, login FROM UTILISATEUR WHERE id_users = :idusers";
    // $queryusers = "SELECT * FROM users WHERE id_users = :idusers";
    $statementusers = $connexion->prepare($queryusers);
    $statementusers->bindParam(":idusers", $id_users, PDO::PARAM_INT);
    $statementusers->execute();

    if ($statementusers->rowCount() > 0) {
        $users = $statementusers->fetch(PDO::FETCH_ASSOC);
    } else {
        $_SESSION['error'] = "users non trouvé.";
        header("Location: manage-users.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
    header("Location: manage-users.php");
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
    <title>Modifier un users - Jeux Olympiques 2024</title>
    <style>
        /* Ajoutez votre style CSS ici */
    </style>
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages users, events, et results -->
            <ul class="menu">
                <li><a href="../admin.php">Accueil Administration</a></li>
                <li><a href="manage-sports.php">Gestion sports</a></li>
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
        <h1>Modifier un users</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="modify-users.php?id_users=<?php echo $id_users; ?>" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir modifier ce users?')">
            <label for=" nom_users">Nom du users :</label>
            <input type="text" name="nom_users" id="nom_users"
                value="<?php echo htmlspecialchars($users['nom_users']); ?>" required>
            <input type="submit" value="Modifier le users">
            <label for="prenom_users">Prenom de l'Utilisateur :</label>
            <input type="text" name="prenom_users" id="prenom_users"
                value="<?php echo htmlspecialchars($user['prenom_users']); ?>" required>
            <label for="login">Login :</label>
            <input type="text" name="login" id="login" value="<?php echo htmlspecialchars($user['login']); ?>"
                required>
            <label for="password">Password :</label>
            <input type="password" name="password" id="password" required>
            <input type="submit" value="Modifier l'Utilisateur">





        </form>
        <p class="paragraph-link">
            <a class="link-home" href="manage-users.php">Retour à la gestion des users</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo  -jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>N
</body>

</html>