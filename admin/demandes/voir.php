<?php
session_start();
require_once '../../config/connexion.php';
require_once '../../fonctions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM demandes_projet WHERE id = ?');
$stmt->execute([$id]);
$demande = $stmt->fetch();

if (!$demande) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('UPDATE demandes_projet SET lu = 1 WHERE id = ?');
$stmt->execute([$id]);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande de <?= htmlspecialchars($demande['nom']) ?></title>
    <link rel="stylesheet" href="../../style.css">
</head>
<body
<h1>Demande de <?= htmlspecialchars($demande['nom']) ?></h1>
<p><strong>Email :</strong> <?= htmlspecialchars($demande['email']) ?></p>
<p><strong>Type de projet :</strong> <?= htmlspecialchars($demande['type_projet']) ?></p>
<p><strong>Budget :</strong> <?= htmlspecialchars($demande['budget'] ?? 'Non précisé') ?></p>
<p><strong>Description :</strong><br><?= htmlspecialchars($demande['description']) ?></p>
<p><strong>Date :</strong> <?= htmlspecialchars($demande['date_demande']) ?></p>
<a href="index.php">← Retour</a>
</body>
</html>