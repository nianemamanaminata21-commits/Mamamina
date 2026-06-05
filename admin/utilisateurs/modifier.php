<?php
session_start();
require_once '../../config/connexion.php';
require_once '../../fonctions.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion.php');
    exit;
}
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM administrateurs WHERE id = ?');
$stmt->execute([$id]);
$admin = $stmt->fetch();
if (!$admin) {
    header('Location: index.php');
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
    if (!empty($mdp) && strlen($mdp) < 6) $erreurs[] = 'Le mot de passe doit faire au moins 6 caractères.';
    if (empty($erreurs)) {
        if (!empty($mdp)) {
            $hash = password_hash($mdp, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('UPDATE administrateurs SET prenom=?, nom=?, email=?, mot_de_passe=? WHERE id=?');
            $stmt->execute([$prenom, $nom, $email, $hash, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE administrateurs SET prenom=?, nom=?, email=? WHERE id=?');
            $stmt->execute([$prenom, $nom, $email, $id]);
        }
        $succes = 'Administrateur modifié !';
        $admin = array_merge($admin, compact('prenom', 'nom', 'email'));
    }
}
$token = generer_token_csrf();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier administrateur</title>
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
    <?php foreach ($erreurs as $e): ?>
        <p style="color:red"><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>
    <?php if ($succes): ?>
        <p style="color:green"><?= $succes ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $token ?>">
        <label>Prénom : <input type="text" name="prenom" value="<?= htmlspecialchars($admin['prenom']) ?>" required></label><br><br>
        <label>Nom : <input type="text" name="nom" value="<?= htmlspecialchars($admin['nom']) ?>" required></label><br><br>
        <label>Email : <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required></label><br><br>
        <label>Mot de passe (laisser vide pour ne pas changer) : <input type="password" name="mot_de_passe"></label><br><br>
        <button type="submit">Modifier</button>
    </form>
    <a href="index.php">← Retour</a>
</body>
</html>