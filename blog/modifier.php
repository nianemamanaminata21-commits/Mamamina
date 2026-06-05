<?php
session_start();
require_once 'config/connexion.php';
require_once 'fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM blog_articles WHERE id = ?');
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article || $article['auteur_id'] != $_SESSION['utilisateur_id']) {
    header('Location: accueil.php');
    exit;
}

$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_token_csrf($_POST['csrf_token'] ?? '');

    $titre = trim($_POST['titre'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $image = $article['image_couverture'];

    if (empty($titre)) $erreurs[] = 'Le titre est obligatoire.';
    if (empty($contenu)) $erreurs[] = 'Le contenu est obligatoire.';

    if (!empty($_FILES['image_couverture']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image_couverture']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($ext, $allowed)) {
            $erreurs[] = 'Format image non autorisé.';
        } else {
            $image = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image_couverture']['tmp_name'], 'images/articles/' . $image);
        }
    }

    if (empty($erreurs)) {
        $stmt = $pdo->prepare('UPDATE blog_articles SET titre=?, contenu=?, image_couverture=? WHERE id=?');
        $stmt->execute([$titre, $contenu, $image, $id]);
        header('Location: article.php?id=' . $id);
        exit;
    }
}

$token = generer_token_csrf();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'article</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php require 'composants/navigation.php'; ?>
<main>
<h1>Modifier l'article</h1>
<?php foreach ($erreurs as $e): ?>
    <p style="color:red"><?= htmlspecialchars($e) ?></p>
<?php endforeach; ?>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $token ?>">
    <label>Titre : <input type="text" name="titre" value="<?= htmlspecialchars($article['titre']) ?>" required></label><br><br>
    <label>Contenu :<br><textarea name="contenu" rows="10" required><?= htmlspecialchars($article['contenu']) ?></textarea></label><br><br>
    <label>Image : <input type="file" name="image_couverture"></label><br><br>
    <button type="submit">Modifier</button>
</form>
<a href="article.php?id=<?= $id ?>">← Retour</a>
</main>
<?php require 'composants/pied-de-page.php'; ?>
</body>
</html>