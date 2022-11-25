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



// si les données du formaulaire ont été envoyé via la méthode post

if ($_SERVER['REQUEST_METHOD'] === 'POST'){

    $post_clean = [];
    $create_form_errors = [];

    foreach ($_POST as $key => $value) {
        $post_clean[$key] = strip_tags(trim($value));
    }

    // mise en place des contraintes de validation du formulaire 

    // pour le nom du livre 

    if (isset($post_clean['title'])){

        if(empty($post_clean['title'])){

            $create_form_errors['title'] = "Le nom du livre est obligatoire.";

        } else if (mb_strlen($post_clean['title']) > 255){

            $create_form_errors['title'] = "Le nom du livre doit contenir au maximu 255 caractères.";
        }

    }
    // pour l'auteur du livre 

    if (isset($post_clean['author'])){

        if(empty($post_clean['author'])){

            $create_form_errors['author'] = "Le nom de l'auteur est obligatoire.";

        } else if (mb_strlen($post_clean['author']) > 255){

            $create_form_errors['author'] = "Le nom du livre doit contenir au maximu 255 caractères.";
        }

    }
    // pour le genre du livre 

    if (isset($post_clean['type_of_book'])){

        if(empty($post_clean['type_of_book'])){

            $create_form_errors['type_of_book'] = "Le genre du film est obligatoire.";

        } else if (mb_strlen($post_clean['type_of_book']) > 255){

            $create_form_errors['type_of_book'] = "Le nom du livre doit contenir au maximu 255 caractères.";
        }

    }
    // pour la note du livre 

    if (isset($post_clean['review'])){

        if(is_string($post_clean['review']) && ($post_clean['review']) == ''){

            $create_form_errors['review'] = "La note du livre est obligatoire.";
        
        } else if(empty($post_clean['review']) && ($post_clean['review']) != 0){

            $create_form_errors['review'] = "La note est obligatoire.";

        } else if ( !is_numeric($post_clean['review']) ){

            $create_form_errors['review'] = "La note est obligatoire.";

        } else if (($post_clean['review'] < 0) || ($post_clean['review'] > 10)){

            $create_form_errors['review'] = "La note doit être comprise entre 0 et 10.";
        }

    }

    
    // s'il y a des erreurs 
    
    if (count($create_form_errors) > 0){
        
        // stocker les messages d'erreurs en session 
        $_SESSION['create_form_errors'] = $create_form_errors;
        
        // stocker les données du formulaire en session
        $_SESSION['old'] = $post_clean;
        
        // redirection de l'utilisateur vers la page de laquelle proviennent les données
        
        return header("Location: " . $_SERVER['HTTP_REFERER']);
        
    }
    
    // dans le cas contraire, proteger le serveur contre les failles xss une seconde fois 
    
    $final_post_clean = [];
    
    foreach ($post_clean as $key => $value) {
        
        $final_post_clean[$key] = htmlspecialchars($value);
    }
    
    $book_title = $final_post_clean['title'];
    $book_author = $final_post_clean['author'];
    $book_type = $final_post_clean['type_of_book'];
    $book_review = $final_post_clean['review'];

    
    $book_review_rounded = round($book_review, 1);


    // etablir une connexion avec la base de données 
    require __DIR__ . "/db1/connexion.php";


    //effectur la requête de modification des données dans la base de données

    $req = $db -> prepare("UPDATE user SET title=:title, author=:author, type_of_book=:type_of_book, review=:review, updated_at=now() WHERE id=:id")  ;

    $req -> bindValue(":title", $book_title);
    $req -> bindValue(":author", $book_author);
    $req -> bindValue(":type_of_book", $book_type);
    $req -> bindValue(":review", $book_review_rounded);
    $req -> bindValue(":id", $livre['id']);

    $req -> execute();
    $req -> closeCursor();


    // génération d'un message flash 
    $_SESSION['success'] = "Le livre a été modifié avec succes !";

    // redirection de l'utilisateur vers la page d'accueil
    return header("Location: index.php");


}

?>





<!------------------------- View --------------------->
<?php $title = "Modifier livre"; ?>

<?php include __DIR__ . "/partials1/head.php"; ?>

<?php include __DIR__ . "/partials1/nav.php"; ?>


    <main>
        <h1>Modifier livre</h1>






        <!---------------- message d'erreur  ------------------------->

<?php if (isset ($_SESSION['create_form_errors']) && !empty($_SESSION['create_form_errors']) ) : ?>
    <div>
        <ul>
        <?php foreach($_SESSION['create_form_errors'] as $error) : ?>
            <li> - <?= $error ?></li>
            <?php endforeach ?>
        </ul>
    </div>
    <?php unset($_SESSION['create_form_errors']); ?>

<?php endif?>


<!------------------------- Le formulaire  ---------------------->
        
        <div>
            <form method="post">
                <div>
                    <label for="title">Titre</label>
                    <input type="text" name="title" id="title" value="<?= isset($_SESSION['old']['title']) ? $_SESSION['old']['title'] : $livre['title']; unset($_SESSION['old']['title']) ?>">
                </div>
                <div>
                    <label for="author">Auteur</label>
                    <input type="text" name="author" id="author" value="<?= isset($_SESSION['old']['author']) ? $_SESSION['old']['author'] : $livre['author']; unset($_SESSION['old']['author']) ?>" />
                </div>
                <div>
                    <label for="type_of_book">Genre</label>
                    <input type="text" name="type_of_book" id="type_of_book" value="<?= isset($_SESSION['old']['type_of_book']) ? $_SESSION['old']['type_of_book'] : $livre['type_of_book']; unset($_SESSION['old']['type_of_book']) ?>">
                </div>
                <div>
                    <label for="review">Note</label>
                    <input type="text" name="review" id="review" value="<?= isset($_SESSION['old']['review']) ? $_SESSION['old']['review'] : $livre['review']; unset($_SESSION['old']['review']) ?>">
                </div>
                <div>
                    <input type="submit">
                </div>
            </form>
        </div>
    </main>

<?php include __DIR__ . "/partials1/footer.php"; ?>

<?php include __DIR__ . "/partials1/foot.php"; ?>