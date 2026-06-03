<?php
session_start();
require_once '../config/connexion.php';
require_once '../fonctions.php';
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}
$erreur = '';
if ($_SERVER['REQUEST_METHOD']==='POST'){
    verifier_token_csrf($_POST['csrf_token']??'');
    $email=trim($_POST['email']??'');
    $mdp=trim($_POST['mot_de_passe']??'');
    $stmt = $pdo->prepare('SELECT * FROM administrateurs WHERE email = ?');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($mdp, $admin['mot_de_passe'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_prenom'] = $admin['prenom'];
        header('Location: dashboard.php');
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
    <title>Connexion Admin</title>
</head>
<body>
<h1>Connexion</h1>
<?php if ($erreur): ?>
    <p style="color:red"><?= htmlspecialchars($erreur) ?></p>
<?php endif; ?>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $token ?>">
    <label>Email : <input type="email" name="email" required></label><br>
    <label>Mot de passe : <input type="password" name="mot_de_passe" required></label><br>
    <button type="submit">Se connecter</button>
</form>
</body>
</html>