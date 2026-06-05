<?php
session_start();
require_once '../../config/connexion.php';
require_once '../../fonctions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion.php');
    exit;
}

$demandes = $pdo->query('SELECT * FROM demandes_projet ORDER BY date_demande DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demandes de projet</title>
    <link rel="stylesheet" href="../../styles.css">
</head>
<body>
<h1>Demandes de projet</h1>
<table border="1">
    <tr>
        <th>Nom</th>
        <th>Email</th>
        <th>Type</th>
        <th>Date</th>
        <th>Statut</th>
        <th>Action</th>
    </tr>
    <?php foreach ($demandes as $d): ?>
    <tr style="<?= $d['lu'] ? '' : 'font-weight:bold' ?>">
        <td><?= htmlspecialchars($d['nom']) ?></td>
        <td><?= htmlspecialchars($d['email']) ?></td>
        <td><?= htmlspecialchars($d['type_projet']) ?></td>
        <td><?= htmlspecialchars($d['date_demande']) ?></td>
        <td><?= $d['lu'] ? 'Lu' : 'Non lu' ?></td>
        <td><a href="voir.php?id=<?= $d['id'] ?>">Voir</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<a href="../dashboard.php">← Dashboard</a>
</body>
</html>