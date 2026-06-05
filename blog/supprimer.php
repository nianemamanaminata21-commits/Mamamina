<?php
session_start();
require_once 'config/connexion.php';
require_once 'fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: accueil.php');
    exit;
}

verifier_token_csrf($_POST['csrf_token'] ?? '');

$id = intval($_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM blog_articles WHERE id = ?');
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article || $article['auteur_id'] != $_SESSION['utilisateur_id']) {
    header('Location: accueil.php');
    exit;
}

if ($article['image_couverture']) {
    $chemin = 'images/articles/' . $article['image_couverture'];
    if (file_exists($chemin)) unlink($chemin);
}

$stmt = $pdo->prepare('DELETE FROM blog_articles WHERE id = ?');
$stmt->execute([$id]);

header('Location: accueil.php');
exit;
?>