<?php
session_start();
require_once 'config/connexion.php';
require_once 'fonctions.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare('
    SELECT blog_articles.*, blog_utilisateurs.prenom, blog_utilisateurs.nom
    FROM blog_articles
    LEFT JOIN blog_utilisateurs ON blog_articles.auteur_id = blog_utilisateurs.id
    WHERE blog_articles.id = ?
');
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) {
    header('Location: accueil.php');
    exit;
}

$commentaires = $pdo->prepare('
    SELECT blog_commentaires.*, blog_utilisateurs.prenom, blog_utilisateurs.nom
    FROM blog_commentaires
    LEFT JOIN blog_utilisateurs ON blog_commentaires.auteur_id = blog_utilisateurs.id
    WHERE blog_commentaires.article_id = ?
    ORDER BY blog_commentaires.date_commentaire ASC
');
$commentaires->execute([$id]);
$commentaires = $commentaires->fetchAll();

// Ajouter commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_commentaire'])) {
    verifier_token_csrf($_POST['csrf_token'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    if (!empty($contenu)) {
        $stmt = $pdo->prepare('INSERT INTO blog_commentaires (article_id, auteur_id, contenu) VALUES (?, ?, ?)');
        $stmt->execute([$id, $_SESSION['utilisateur_id'], $contenu]);
        header('Location: article.php?id=' . $id);
        exit;
    }
}

// Supprimer commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_commentaire'])) {
    verifier_token_csrf($_POST['csrf_token'] ?? '');
    $comment_id = intval($_POST['comment_id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM blog_commentaires WHERE id = ? AND auteur_id = ?');
    $stmt->execute([$comment_id, $_SESSION['utilisateur_id']]);
    header('Location: article.php?id=' . $id);
    exit;
}

$token = generer_token_csrf();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($article['titre']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php require 'composants/navigation.php'; ?>
<main>
<?php if ($article['image_couverture']): ?>
    <img src="images/articles/<?= htmlspecialchars($article['image_couverture']) ?>" width="400">
<?php endif; ?>
<h1><?= htmlspecialchars($article['titre']) ?></h1>
<p>Par <?= htmlspecialchars($article['prenom']) ?> <?= htmlspecialchars($article['nom']) ?></p>
<p><?= htmlspecialchars($article['date_publication']) ?></p>
<p><?= nl2br(htmlspecialchars($article['contenu'])) ?></p>

<?php if (isset($_SESSION['utilisateur_id']) && $_SESSION['utilisateur_id'] == $article['auteur_id']): ?>
    <a href="modifier.php?id=<?= $article['id'] ?>">Modifier</a>
    <form method="POST" action="supprimer.php" style="display:inline">
        <input type="hidden" name="csrf_token" value="<?= $token ?>">
        <input type="hidden" name="id" value="<?= $article['id'] ?>">
        <button type="submit" onclick="return confirm('Supprimer ?')">Supprimer</button>
    </form>
<?php endif; ?>

<h2>Commentaires</h2>
<?php foreach ($commentaires as $c): ?>
<div style="border:1px solid #ccc; margin:5px; padding:5px;">
    <p><strong><?= htmlspecialchars($c['prenom']) ?> <?= htmlspecialchars($c['nom']) ?></strong></p>
    <p><?= htmlspecialchars($c['contenu']) ?></p>
    <p><?= htmlspecialchars($c['date_commentaire']) ?></p>
    <?php if (isset($_SESSION['utilisateur_id']) && $_SESSION['utilisateur_id'] == $c['auteur_id']): ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $token ?>">
        <input type="hidden" name="comment_id" value="<?= $c['id'] ?>">
        <button type="submit" name="supprimer_commentaire" value="1">Supprimer</button>
    </form>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<?php if (isset($_SESSION['utilisateur_id'])): ?>
<h3>Laisser un commentaire</h3>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $token ?>">
    <input type="hidden" name="form_commentaire" value="1">
    <textarea name="contenu" rows="4" required></textarea><br>
    <button type="submit">Commenter</button>
</form>
<?php else: ?>
    <p><a href="connexion.php">Connectez-vous</a> pour commenter.</p>
<?php endif; ?>

<a href="accueil.php">← Retour</a>
</main>
<?php require 'composants/pied-de-page.php'; ?>
</body>
</html>