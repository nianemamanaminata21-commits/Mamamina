<?php
session_start();
require_once 'config/connexion.php';
require_once 'fonctions.php';

$articles = $pdo->query('
    SELECT blog_articles.*, blog_utilisateurs.prenom, blog_utilisateurs.nom,
    COUNT(blog_commentaires.id) as nb_commentaires
    FROM blog_articles
    LEFT JOIN blog_utilisateurs ON blog_articles.auteur_id = blog_utilisateurs.id
    LEFT JOIN blog_commentaires ON blog_commentaires.article_id = blog_articles.id
    GROUP BY blog_articles.id
    ORDER BY blog_articles.date_publication DESC
')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Blog ESTM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php require 'composants/navigation.php'; ?>
<main>
<h1>Blog ESTM</h1>
<?php if (empty($articles)): ?>
    <p>Aucun article publié pour le moment.</p>
<?php else: ?>
    <?php foreach ($articles as $a): ?>
    <div style="border:1px solid #ccc; margin:10px; padding:10px;">
        <?php if ($a['image_couverture']): ?>
            <img src="images/articles/<?= htmlspecialchars($a['image_couverture']) ?>" width="200">
        <?php endif; ?>
        <h2><a href="article.php?id=<?= $a['id'] ?>"><?= htmlspecialchars($a['titre']) ?></a></h2>
        <p><?= htmlspecialchars(substr($a['contenu'], 0, 150)) ?>...</p>
        <p>Par <?= htmlspecialchars($a['prenom']) ?> <?= htmlspecialchars($a['nom']) ?></p>
        <p>Le <?= htmlspecialchars($a['date_publication']) ?></p>
        <p><?= $a['nb_commentaires'] ?> commentaire(s)</p>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
<?php if (isset($_SESSION['utilisateur_id'])): ?>
    <a href="publier.php">+ Publier un article</a>
<?php endif; ?>
</main>
<?php require 'composants/pied-de-page.php'; ?>
</body>
</html>