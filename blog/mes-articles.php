<?php
session_start();
require_once 'config/connexion.php';
require_once 'fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php');
    exit;
}

$stmt = $pdo->prepare('
    SELECT blog_articles.*, COUNT(blog_commentaires.id) as nb_commentaires
    FROM blog_articles
    LEFT JOIN blog_commentaires ON blog_commentaires.article_id = blog_articles.id
    WHERE blog_articles.auteur_id = ?
    GROUP BY blog_articles.id
    ORDER BY blog_articles.date_publication DESC
');
$stmt->execute([$_SESSION['utilisateur_id']]);
$articles = $stmt->fetchAll();

$token = generer_token_csrf();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes articles</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php require 'composants/navigation.php'; ?>
<main>
<h1>Mes articles</h1>
<?php if (empty($articles)): ?>
    <p>Vous n'avez pas encore publié d'article. <a href="publier.php">Publier maintenant !</a></p>
<?php else: ?>
    <?php foreach ($articles as $a): ?>
    <div style="border:1px solid #ccc; margin:10px; padding:10px;">
        <h2><?= htmlspecialchars($a['titre']) ?></h2>
        <p><?= htmlspecialchars($a['date_publication']) ?></p>
        <p><?= $a['nb_commentaires'] ?> commentaire(s)</p>
        <a href="modifier.php?id=<?= $a['id'] ?>">Modifier</a>
        <form method="POST" action="supprimer.php" style="display:inline">
            <input type="hidden" name="csrf_token" value="<?= $token ?>">
            <input type="hidden" name="id" value="<?= $a['id'] ?>">
            <button type="submit" onclick="return confirm('Supprimer ?')">Supprimer</button>
        </form>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
</main>
<?php require 'composants/pied-de-page.php'; ?>
</body>
</html>