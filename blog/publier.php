<?php
session_start();
require_once 'config/connexion.php';
require_once 'fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php');
    exit;
}

$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_token_csrf($_POST['csrf_token'] ?? '');

    $titre = trim($_POST['titre'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $image = null;

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
        $stmt = $pdo->prepare('INSERT INTO blog_articles (titre, contenu, image_couverture, auteur_id) VALUES (?, ?, ?, ?)');
        $stmt->execute([$titre, $contenu, $image, $_SESSION['utilisateur_id']]);
        $id = $pdo->lastInsertId();
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
    <title>Publier un article</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php require 'composants/navigation.php'; ?>
<main>
<h1>Publier un article</h1>
<?php foreach ($erreurs as $e): ?>
    <p style="color:red"><?= htmlspecialchars($e) ?></p>
<?php endforeach; ?>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $token ?>">
    <label>Titre : <input type="text" name="titre" required></label><br><br>
    <label>Contenu :<br><textarea name="contenu" rows="10" required></textarea></label><br><br>
    <label>Image de couverture : <input type="file" name="image_couverture"></label><br><br>
    <button type="submit">Publier</button>
</form>
<a href="accueil.php">← Retour</a>
</main>
<?php require 'composants/pied-de-page.php'; ?>
</body>
</html>