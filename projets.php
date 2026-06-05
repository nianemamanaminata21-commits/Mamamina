<?php require 'composants/navigation.php'; ?>
<?php
session_start();
require_once 'config/connexion.php';
require_once 'fonctions.php';
enregistrer_visite($pdo, 'projets');

$mot_cle = nettoyer($_GET['recherche'] ?? '');

if ($mot_cle !== '') {
    $stmt = $pdo->prepare('SELECT * FROM projets WHERE titre LIKE ? OR description LIKE ? ORDER BY date_creation DESC');
    $stmt->execute(['%'.$mot_cle.'%', '%'.$mot_cle.'%']);
    $resultats = $stmt->fetchAll();
} else {
    $resultats = $pdo->query('SELECT * FROM projets ORDER BY date_creation DESC')->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projets</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

 
<main>

<h2>Rechercher un projet</h2>

<form method="GET" action="projets.php" class="search-form">

    <input
    type="text"
    name="recherche"
    placeholder="Rechercher par mot-clé...">

    <button type="submit">Rechercher</button>

</form>
<section class="grid">
<?php foreach ($resultats as $projet): ?>
<div class="card">
    <?php if ($projet['image']): ?>
    <img src="images/projets/<?= htmlspecialchars($projet['image']) ?>" 
         alt="<?= htmlspecialchars($projet['titre']) ?>">
    <?php endif; ?>
    <h3><?= htmlspecialchars($projet['titre']) ?></h3>
    <p><?= htmlspecialchars($projet['description']) ?></p>
    <div class="technologies">
        <?php foreach (explode(',', $projet['technologies']) as $tech): ?>
            <span class="badge"><?= htmlspecialchars(trim($tech)) ?></span>
        <?php endforeach; ?>
    </div>
    <?php if ($projet['lien']): ?>
        <a href="<?= htmlspecialchars($projet['lien']) ?>" target="_blank">Voir le projet</a>
    <?php endif; ?>
</div>
<?php endforeach; ?>
</section>
<?php if (empty($resultats)) : ?>

<p>Aucun projet ne correspond à ta recherche.</p>

<?php endif; ?>

</main>
<?php require 'composants/pied-de-page.php'; ?>
</body>
</html>