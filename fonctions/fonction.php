<?php 

function unique(string $name) : bool{

    require __DIR__ . "/../db1/connexion.php";

    $req = $db -> prepare("SELECT * FROM user WHERE title=:title");
    $req -> bindValue(":title", $name);

    $req -> execute();

    $row = $req -> rowCount();

    if ($row == 1){

        return true;
    }
     return false;

}




?>