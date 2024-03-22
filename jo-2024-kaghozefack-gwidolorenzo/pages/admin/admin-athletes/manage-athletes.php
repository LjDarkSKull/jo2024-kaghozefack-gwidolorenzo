<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

$login = $_SESSION['login'];
$nom_utilisateur = $_SESSION['prenom_utilisateur'];
$prenom_utilisateur = $_SESSION['nom_utilisateur'];
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
    <title>Liste des Athlètes - Jeux Olympiques 2024</title>
</head>

<body class="adminBody">
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
            <h1>Liste des Athlètes</h1>
        </figure>

        <div class="table-container bigTable">
            <!-- Tableau des sports -->
            <?php
            require_once("../../../database/database.php");

            try {
                // Requête pour récupérer la liste des sports depuis la base de données
                $query = "SELECT * FROM ATHLETE 
                INNER JOIN PAYS ON ATHLETE.id_pays = PAYS.id_pays
                INNER JOIN GENRE ON ATHLETE.id_genre = GENRE.id_genre
                ORDER BY nom_athlete";
                $statement = $connexion->prepare($query);
                $statement->execute();

                // Vérifier s'il y a des résultats
                if ($statement->rowCount() > 0) {
                    echo "<table>";
                    echo "<thead>
                <th class='color'>Nom</th>
                <th class='color'>Prenom</th>
                <th class='color'>Pays</th>
                <th class='color'>Genre</th>
                <th class='color'>Modifier</th>
                <th class='color'>supprimer</th>
                </thead>";

                    // Afficher les données dans un tableau
                    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['nom_athlete']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['prenom_athlete']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nom_pays']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nom_genre']) . "</td>";
                        echo "<td><button onclick='openModifyAthletesForm({$row['id_athlete']})'>Modifier</button></td>";
                        echo "<td><button class='delete' onclick='deleteAthletesConfirmation({$row['id_athlete']})'>Supprimer</button></td>";
                        echo "</tr>";
                    }

                    echo "</table>";
                } else {
                    echo "<p>Aucun Athlete trouvé.</p>";
                }
            } catch (PDOException $e) {
                echo "Erreur : " . $e->getMessage();
            }
            // Afficher les erreurs en PHP
            // (fonctionne à condition d’avoir activé l’option en local)
            error_reporting(E_ALL);
            ini_set("display_errors", 1);
            ?>
        </div>
        <div class="action-buttons">
            <button onclick="openAddAthletesForm()">Ajouter un Athlete +</button>
            <!-- Autres boutons... -->
        </div>
    </main>
    <footer>

        <a href="https://cdc-jo-nkh.netlify.app/" target="blank">Cahier de charge</a>
        <a href="https://nawafkh.webflow.io/" target="blank">Portfolio</a>
    </footer>
    <script>
        function openAddAthletesForm() {
            // Ouvrir une fenêtre pop-up avec le formulaire de modification
            // L'URL contien un paramètre "id"
            window.location.href = 'add-athletes.php';
        }

        function openModifyAthletesForm(id_athlete) {
            // Ajoutez ici le code pour afficher un formulaire stylisé pour modifier un Athlete
            // alert(id_athlete);
            window.location.href = 'modify-athletes.php?id_athlete=' + id_athlete;
        }

        function deleteAthletesConfirmation(id_athlete) {
            // Ajoutez ici le code pour afficher une fenêtre de confirmation pour supprimer un Athlete
            if (confirm("Êtes-vous sûr de vouloir supprimer ce Athlete?")) {
                // Ajoutez ici le code pour la suppression du Athlete
                // alert(id_athlete);
                window.location.href = 'delete-athletes.php?id_athlete=' + id_athlete;
            }
        }
    </script>
</body>

</html>
