<?php
session_start();
require_once 'config/connexion.php';
require_once 'fonctions.php';

if (isset($_SESSION['utilisateur_id'])) {
    header('Location: accueil.php');
    exit;
}

$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_token_csrf($_POST['csrf_token'] ?? '');

    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mdp = $_POST['mot_de_passe'] ?? '';
    $mdp_confirm = $_POST['mot_de_passe_confirm'] ?? '';

    if (empty($prenom)) $erreurs[] = 'Le prénom est obligatoire.';
    if (empty($nom)) $erreurs[] = 'Le nom est obligatoire.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erreurs[] = 'Email invalide.';
    if (empty($mdp)) $erreurs[] = 'Le mot de passe est obligatoire.';
    if ($mdp !== $mdp_confirm) $erreurs[] = 'Les mots de passe ne correspondent pas.';

    if (empty($erreurs)) {
        $stmt = $pdo->prepare('SELECT id FROM blog_utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erreurs[] = 'Cet email est déjà utilisé.';
        } else {
            $hash = password_hash($mdp, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('INSERT INTO blog_utilisateurs (prenom, nom, email, mot_de_passe) VALUES (?, ?, ?, ?)');
            $stmt->execute([$prenom, $nom, $email, $hash]);
            session_regenerate_id(true);
            $_SESSION['utilisateur_id'] = $pdo->lastInsertId();
            $_SESSION['utilisateur_prenom'] = $prenom;
            $_SESSION['utilisateur_nom'] = $nom;
            header('Location: accueil.php');
            exit;
        }
    }
}

$token = generer_token_csrf();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php require 'composants/navigation.php'; ?>
<main>
<h1>Inscription</h1>
<?php foreach ($erreurs as $e): ?>
    <p style="color:red"><?= htmlspecialchars($e) ?></p>
<?php endforeach; ?>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $token ?>">
    <label>Prénom : <input type="text" name="prenom" required></label><br><br>
    <label>Nom : <input type="text" name="nom" required></label><br><br>
    <label>Email : <input type="email" name="email" required></label><br><br>
    <label>Mot de passe : <input type="password" name="mot_de_passe" required></label><br><br>
    <label>Confirmer : <input type="password" name="mot_de_passe_confirm" required></label><br><br>
    <button type="submit">S'inscrire</button>
</form>
<a href="connexion.php">Déjà inscrit ? Connectez-vous</a>
</main>
<?php require 'composants/pied-de-page.php'; ?>
</body>
</html>