<?php
session_start();

// etablir une connexion avec la base de données
require __DIR__ . "/db1/connexion.php";

// effectuer la requête de récupération de données de la table user
$req = $db -> prepare("SELECT * FROM user ORDER BY created_at DESC");
$req -> execute();
$livres = $req -> fetchAll();

?>




<!-------------------- View ------------------------->
<?php require __DIR__ . "/partials1/head.php"; ?>

<?php require __DIR__ . "/partials1/nav.php"; ?>


    <main>
        <h1>Liste des livres</h1>
            <?php if(isset($_SESSION['success'])) : ?>
                <div>
                    <?= $_SESSION['success'] ?>
                </div>
                <?php unset($_SESSION['success']) ?>
            <?php endif ?>

        <div>
            <a href="create.php">Ajouter film</a>
        </div>
        
        <?php if(isset($livres) && !empty($livres)) : ?>
            <?php foreach($livres as $livre) : ?>
                <div>
                    <h2>Livre numéro: <?= htmlspecialchars($livre['id']) ?>  </h2>
                    <hr/>
                    <p>Titre : <?= htmlspecialchars($livre['title']) ?> </p>
                    <p>Genre : <?= htmlspecialchars($livre['type_of_book']) ?> </p>
                    <p>Auteur : <?= htmlspecialchars($livre['author']) ?> </p>
                    <p>Note sur 10 : <?= htmlspecialchars($livre['review']) ?> </p>
                    <a href="update.php?livre_id=<?= htmlspecialchars($livre['id']) ?>">Modifier</a>
                    <a href="delete.php?livre_id=<?= htmlspecialchars($livre['id']) ?>" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
                </div>
                <?php endforeach ?>
            <?php else : ?>
                <p>Aucun livre ajouté.</p>    
        <?php endif ?> 
    </main>

<?php require __DIR__ . "/partials1/footer.php"; ?>

<?php require __DIR__ . "/partials1/foot.php"; ?>