<?php
session_start();
require_once '../../config/connexion.php';
require_once '../../fonctions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion.php');
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
    $image = null;

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
        $stmt = $pdo->prepare('INSERT INTO projets (titre, description, technologies, image, lien) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$titre, $description, $technologies, $image, $lien]);
        $succes = 'Projet créé avec succès !';
    }
}

$token = generer_token_csrf();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un projet</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<h1>Nouveau projet</h1>
<?php foreach ($erreurs as $e): ?>
    <p style="color:red"><?= htmlspecialchars($e) ?></p>
<?php endforeach; ?>
<?php if ($succes): ?>
    <p style="color:green"><?= $succes ?></p>
<?php endif; ?>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $token ?>">
    <label>Titre : <input type="text" name="titre" required></label><br><br>
    <label>Description :<br><textarea name="description" required></textarea></label><br><br>
    <label>Technologies : <input type="text" name="technologies" required></label><br><br>
    <label>Lien : <input type="text" name="lien"></label><br><br>
    <label>Image : <input type="file" name="image"></label><br><br>
    <button type="submit">Créer</button>
</form>
<a href="index.php">← Retour</a>
</body>
</html>