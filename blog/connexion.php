<?php
session_start();
require_once 'config/connexion.php';
require_once 'fonctions.php';

if (isset($_SESSION['utilisateur_id'])) {
    header('Location: accueil.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_token_csrf($_POST['csrf_token'] ?? '');

    $email = trim($_POST['email'] ?? '');
    $mdp = $_POST['mot_de_passe'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM blog_utilisateurs WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($mdp, $user['mot_de_passe'])) {
        session_regenerate_id(true);
        $_SESSION['utilisateur_id'] = $user['id'];
        $_SESSION['utilisateur_prenom'] = $user['prenom'];
        $_SESSION['utilisateur_nom'] = $user['nom'];
        header('Location: accueil.php');
        exit;
    } else {
        $erreur = 'Email ou mot de passe incorrect.';
    }
}

$token = generer_token_csrf();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php require 'composants/navigation.php'; ?>
<main>
<h1>Connexion</h1>
<?php if ($erreur): ?>
    <p style="color:red"><?= htmlspecialchars($erreur) ?></p>
<?php endif; ?>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $token ?>">
    <label>Email : <input type="email" name="email" required></label><br><br>
    <label>Mot de passe : <input type="password" name="mot_de_passe" required></label><br><br>
    <button type="submit">Se connecter</button>
</form>
<a href="inscription.php">Pas encore inscrit ?</a>
</main>
<?php require 'composants/pied-de-page.php'; ?>
</body>
</html>