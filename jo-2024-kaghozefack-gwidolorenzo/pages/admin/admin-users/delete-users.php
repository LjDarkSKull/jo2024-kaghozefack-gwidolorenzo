<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'ID du sport est fourni dans l'URL
if (!isset($_GET['id_users'])) {
    $_SESSION['error'] = "ID du users manquant.";
    header("Location: manage-users.php");
    exit();
} else {
    $id_sport = filter_input(INPUT_GET, 'id_u', FILTER_VALIDATE_INT);
    // Vérifiez si l'ID du u est un entier valide
    if (!$id_u && $id_u !== 0) {
        $_SESSION['error'] = "ID du u invalide.";
        header("Location: manage-users.php");
        exit();
    } else {
        try {
            // Récupérez l'ID de l'œuvre à supprimer depuis la requête GET
            $id_u = $_GET['id_users'];
            // Préparez la requête SQL pour supprimer l'œuvre
            $sql = "DELETE FROM utilisateur WHERE id_users = :id_users";
            // Exécutez la requête SQL avec le paramètre
            $statement = $connexion->prepare($sql);
            $statement->bindParam(':id_users', $id_users, PDO::PARAM_INT);
            $statement->execute();
            // Redirigez vers la page précédente après la suppression
            header('Location: manage-users.php');
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
        }
    }
}
// Afficher les erreurs en PHP (fonctionne à condition d’avoir activé l’option en local)
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>