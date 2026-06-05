<?php
session_start();
require_once 'config/connexion.php';
require_once 'fonctions.php';
enregistrer_visite($pdo,'index');
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Portfolio </title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<?php require 'composants/navigation.php'; ?>
 
<main>
    <section class="hero">
        <img src="images/ma_photo.jpeg" class="ma_photo">
        <h2>Etudiante en Génie logiciel et Administration réseau</h2>
        <p>Je crée des sites modernes, élégants et responsives.</p> 
        <p>A travers les projets, je développe des compétences en programmation et en gestion de réseaux.</p>
        <a href="CV.pdf" class="btn" target="_blank">Voir mon CV</a>
    </section>

    </main>
 
<?php require 'composants/pied-de-page.php'; ?>

</body>
</html>