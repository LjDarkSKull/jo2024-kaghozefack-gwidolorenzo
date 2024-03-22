<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'ID du sport est fourni dans l'URL
if (!isset($_GET['id_sport'])) {
    $_SESSION['error'] = "ID du sport manquant.";
    header("Location: manage-sports.php");
    exit();
} else {
    $id_athlete = filter_input(INPUT_GET, 'id_athlete', FILTER_VALIDATE_INT);

    // Vérifiez si l'ID de l'athlète est un entier valide
    if (!$id_athlete && $id_athlete !== 0) {
        $_SESSION['error'] = "ID de l'athlète invalide.";
        header("Location: manage-results.php");
        exit();

    } else {
        try {
            // Préparez la requête SQL pour supprimer le résultat de l'athlète
            $sql = "DELETE FROM PARTICIPER WHERE id_athlete = :id_athlete";

            // Exécutez la requête SQL avec le paramètre
            $statement = $connexion->prepare($sql);
            $statement->bindParam(':id_athlete', $id_athlete, PDO::PARAM_INT);
            $statement->execute();

            // Redirigez vers la page précédente après la suppression
            header('Location: manage-results.php');
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur : " . $e->getMessage();
            header("Location: manage-results.php");
            exit();
        }
    }
}
// Afficher les erreurs en PHP (fonctionne à condition d’avoir activé l’option en local)
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>
