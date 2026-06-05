<?php
session_start();
require_once '../../config/connexion.php';
require_once '../../fonctions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion.php');
    exit;
}

$projets = $pdo->query('SELECT * FROM projets ORDER BY date_creation DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des projets</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<h1>Projets</h1>
<a href="creer.php">+ Nouveau projet</a>
<table border="1">
    <tr>
        <th>Titre</th>
        <th>Technologies</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($projets as $p): ?>
    <tr>
        <td><?= htmlspecialchars($p['titre']) ?></td>
        <td><?= htmlspecialchars($p['technologies']) ?></td>
        <td><?= htmlspecialchars($p['date_creation']) ?></td>
        <td>
            <a href="modifier.php?id=<?= $p['id'] ?>">Modifier</a>
            <form method="POST" action="supprimer.php" style="display:inline">
                <input type="hidden" name="csrf_token" value="<?= generer_token_csrf() ?>">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" onclick="return confirm('Supprimer ?')">Supprimer</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<a href="../dashboard.php">← Dashboard</a>
</body>
</html>