<?php

    /* Connexion à une base MySQL avec l'invocation de pilote */
    $dsn_d = 'mysql:dbname=dwwm8b_evaluation;host=127.0.0.1;port=8889';
    $user_d = 'root';
    $password_d = 'root';

    try {

        $db = new PDO($dsn_d, $user_d, $password_d);

    }
    catch (PDOException $error) {

        die("Error: " . $error -> getMessage());
    }

?>