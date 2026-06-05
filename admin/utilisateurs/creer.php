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

    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mdp = $_POST['mot_de_passe'] ?? '';

    if (empty($prenom)) $erreurs[] = 'Le prénom est obligatoire.';
    if (empty($nom)) $erreurs[] = 'Le nom est obligatoire.';
    if (empty($email)) $erreurs[] = 'L\'email est obligatoire.';
    if (empty($mdp)) $erreurs[] = 'Le mot de passe est obligatoire.';

    if (empty($erreurs)) {
        $hash = password_hash($mdp, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO administrateurs (prenom, nom, email, mot_de_passe) VALUES (?, ?, ?, ?)');
        $stmt->execute([$prenom, $nom, $email, $hash]);
        $succes = 'Administrateur créé !';
    }
}

$token = generer_token_csrf();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvel administrateur</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<h1>Nouvel administrateur</h1>
<?php foreach ($erreurs as $e): ?>
    <p style="color:red"><?= htmlspecialchars($e) ?></p>
<?php endforeach; ?>
<?php if ($succes): ?>
    <p style="color:green"><?= $succes ?></p>
<?php endif; ?>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $token ?>">
    <label>Prénom : <input type="text" name="prenom" required></label><br><br>
    <label>Nom : <input type="text" name="nom" required></label><br><br>
    <label>Email : <input type="email" name="email" required></label><br><br>
    <label>Mot de passe : <input type="password" name="mot_de_passe" required></label><br><br>
    <button type="submit">Créer</button>
</form>
<a href="index.php">← Retour</a>
</body>
</html>