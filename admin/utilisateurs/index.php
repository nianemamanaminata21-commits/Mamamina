<?php
session_start();
require_once '../../config/connexion.php';
require_once '../../fonctions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion.php');
    exit;
}
$admins=$pdo->query("SELECT id,prenom,nom,email,date_creation FROM administrateurs")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrateurs</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<h1>Administrateurs</h1>
<a href="creer.php">+ Nouvel administrateur</a>
<table border="1">
    <tr>
        <th>Prénom</th>
        <th>Nom</th>
        <th>Email</th>
        <th>Date de création</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($admins as $a): ?>
    <tr>
        <td><?= htmlspecialchars($a['prenom']) ?></td>
        <td><?= htmlspecialchars($a['nom']) ?></td>
        <td><?= htmlspecialchars($a['email']) ?></td>
        <td><?= htmlspecialchars($a['date_creation']) ?></td>
        <td>
            <a href="modifier.php?id=<?= $a['id'] ?>">Modifier</a>
            <?php if ($a['id'] != $_SESSION['admin_id']): ?>
            <form method="POST" action="supprimer.php" style="display:inline">
                <input type="hidden" name="csrf_token" value="<?= generer_token_csrf() ?>">
                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                <button type="submit" onclick="return confirm('Supprimer ?')">Supprimer</button>
            </form>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<a href="../dashboard.php">← Dashboard</a>
</body>
</html>
