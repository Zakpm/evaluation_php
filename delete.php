<?php 
session_start();

// si aucun livre n'a été envoyé via la méthode GET 
if( !isset($_GET['livre_id']) || empty($_GET['livre_id'])){

    // effectuer une redirection vers la page d'accueil 
    // arreter le script 
    return header("Location: index.php");
}

// dans le cas contraire on récupère l'identifiant du livre de $_GET
$livre_id = strip_tags($_GET['livre_id']);


// convertion de l'identifiant en entier
$livre_id_converted = (int) $livre_id;


// etablir une connexion avec la base données
require __DIR__ . "/db1/connexion.php";


// effectuer la requête de selection pour vérifier si l'identifiant de livre correspond à celui de la table user
$req = $db -> prepare("SELECT * FROM user WHERE id = :id");

$req -> bindValue(":id", $livre_id_converted);
$req -> execute();
$count = $req -> rowCount();

// si le nombre d'enregistrement n'est pas égal à 1
// arrêter le script et redirection vers la page d'accueil
if ($count != 1) {

   return header("Location: index.php");
}

// dans le cas contraire récupérer le livre en quetion 
$livre = $req -> fetch();

// fermer le curseur
$req -> closeCursor();




//effectur la requête pour supprimer le livre dans la base de données

$req = $db -> prepare("DELETE FROM user WHERE id = :id")  ;
$req -> bindValue(":id", $livre['id']);
$req -> execute();
$req -> closeCursor();


// génération d'un message flash 
$_SESSION['success'] = "Le livre " . $livre['title'] . " a été retiré de la liste.";

// redirection de l'utilisateur vers la page d'accueil
return header("Location: index.php");






?>