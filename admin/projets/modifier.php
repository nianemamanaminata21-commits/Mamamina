<?php
session_start();
require_once '../../config/connexion.php';
require_once '../../fonctions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM projets WHERE id = ?');
$stmt->execute([$id]);
$projet = $stmt->fetch();

if (!$projet) {
    header('Location: index.php');
    exit;
}

$erreurs = [];
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_token_csrf($_POST['csrf_token'] ?? '');

    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $technologies = trim($_POST['technologies'] ?? '');
    $lien = trim($_POST['lien'] ?? '');
    $image = $projet['image'];

    if (empty($titre)) $erreurs[] = 'Le titre est obligatoire.';
    if (empty($description)) $erreurs[] = 'La description est obligatoire.';
    if (empty($technologies)) $erreurs[] = 'Les technologies sont obligatoires.';

    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($ext, $allowed)) {
            $erreurs[] = 'Format image non autorisé.';
        } else {
            $image = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../../images/projets/' . $image);
        }
    }

    if (empty($erreurs)) {
        $stmt = $pdo->prepare('UPDATE projets SET titre=?, description=?, technologies=?, image=?, lien=? WHERE id=?');
        $stmt->execute([$titre, $description, $technologies, $image, $lien, $id]);
        $succes = 'Projet modifié avec succès !';
        $projet = array_merge($projet, compact('titre', 'description', 'technologies', 'lien', 'image'));
    }
}

$token = generer_token_csrf();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un projet</title>
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
<h1>Modifier le projet</h1>
<?php foreach ($erreurs as $e): ?>
    <p style="color:red"><?= htmlspecialchars($e) ?></p>
<?php endforeach; ?>
<?php if ($succes): ?>
    <p style="color:green"><?= $succes ?></p>
<?php endif; ?>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $token ?>">
    <label>Titre : <input type="text" name="titre" value="<?= htmlspecialchars($projet['titre']) ?>" required></label><br><br>
    <label>Description :<br><textarea name="description" required><?= htmlspecialchars($projet['description']) ?></textarea></label><br><br>
    <label>Technologies : <input type="text" name="technologies" value="<?= htmlspecialchars($projet['technologies']) ?>" required></label><br><br>
    <label>Lien : <input type="text" name="lien" value="<?= htmlspecialchars($projet['lien'] ?? '') ?>"></label><br><br>
    <label>Image : <input type="file" name="image"></label><br><br>
    <button type="submit">Modifier</button>
</form>
<a href="index.php">← Retour</a>
</body>
</html>