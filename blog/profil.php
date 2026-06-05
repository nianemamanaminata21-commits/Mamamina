<?php
session_start();
require_once 'config/connexion.php';
require_once 'fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM blog_utilisateurs WHERE id = ?');
$stmt->execute([$_SESSION['utilisateur_id']]);
$user = $stmt->fetch();

$erreurs = [];
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_token_csrf($_POST['csrf_token'] ?? '');

    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $mdp = $_POST['mot_de_passe'] ?? '';

    if (empty($prenom)) $erreurs[] = 'Le prénom est obligatoire.';
    if (empty($nom)) $erreurs[] = 'Le nom est obligatoire.';

    if (empty($erreurs)) {
        if (!empty($mdp)) {
            $hash = password_hash($mdp, PASSWORD_BCRYPT);
        } else {
            $hash = $user['mot_de_passe'];
        }
        $stmt = $pdo->prepare('UPDATE blog_utilisateurs SET prenom=?, nom=?, mot_de_passe=? WHERE id=?');
        $stmt->execute([$prenom, $nom, $hash, $_SESSION['utilisateur_id']]);
        $_SESSION['utilisateur_prenom'] = $prenom;
        $_SESSION['utilisateur_nom'] = $nom;
        $succes = 'Profil mis à jour !';
        $user['prenom'] = $prenom;
        $user['nom'] = $nom;
    }
}

$token = generer_token_csrf();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon profil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php require 'composants/navigation.php'; ?>
<main>
<h1>Mon profil</h1>
<?php foreach ($erreurs as $e): ?>
    <p style="color:red"><?= htmlspecialchars($e) ?></p>
<?php endforeach; ?>
<?php if ($succes): ?>
    <p style="color:green"><?= $succes ?></p>
<?php endif; ?>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $token ?>">
    <label>Prénom : <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required></label><br><br>
    <label>Nom : <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required></label><br><br>
    <label>Email : <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled></label><br><br>
    <label>Nouveau mot de passe (laisser vide pour ne pas changer) : <input type="password" name="mot_de_passe"></label><br><br>
    <button type="submit">Mettre à jour</button>
</form>
</main>
<?php require 'composants/pied-de-page.php'; ?>
</body>
</html>