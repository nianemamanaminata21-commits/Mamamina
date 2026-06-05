<?php
session_start();
require_once '../config/connexion.php';
require_once '../fonctions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: connexion.php');
    exit;
}

// Statistiques
$nb_projets = $pdo->query('SELECT COUNT(*) FROM projets')->fetchColumn();
$nb_messages = $pdo->query('SELECT COUNT(*) FROM messages_contact WHERE lu = 0')->fetchColumn();
$nb_demandes = $pdo->query('SELECT COUNT(*) FROM demandes_projet WHERE lu = 0')->fetchColumn();

// 5 dernières visites
$visites = $pdo->query('SELECT * FROM visites ORDER BY date_visite DESC LIMIT 5')->fetchAll();

// 5 dernières demandes
$demandes = $pdo->query('SELECT * FROM demandes_projet ORDER BY date_demande DESC LIMIT 5')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<h1>Bonjour, <?= htmlspecialchars($_SESSION['admin_prenom']) ?> !</h1>

<p>Projets : <?= $nb_projets ?></p>
<p>Messages non lus : <?= $nb_messages ?></p>
<p>Demandes non lues : <?= $nb_demandes ?></p>

<h2>5 dernières visites</h2>
<table border="1">
    <tr><th>IP</th><th>Page</th><th>Date</th></tr>
    <?php foreach ($visites as $v): ?>
    <tr>
        <td><?= htmlspecialchars($v['adresse_ip']) ?></td>
        <td><?= htmlspecialchars($v['page']) ?></td>
        <td><?= htmlspecialchars($v['date_visite']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<h2>5 dernières demandes</h2>
<table border="1">
    <tr><th>Nom</th><th>Type</th><th>Date</th></tr>
    <?php foreach ($demandes as $d): ?>
    <tr>
        <td><?= htmlspecialchars($d['nom']) ?></td>
        <td><?= htmlspecialchars($d['type_projet']) ?></td>
        <td><?= htmlspecialchars($d['date_demande']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<a href="deconnexion.php">Se déconnecter</a>
</body>
</html>