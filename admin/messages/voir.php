<?php
session_start();
require_once '../../config/connexion.php';
require_once '../../fonctions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM messages_contact WHERE id = ?');
$stmt->execute([$id]);
$message = $stmt->fetch();

if (!$message) {
    header('Location: index.php');
    exit;
}

// Marquer comme lu
$stmt = $pdo->prepare('UPDATE messages_contact SET lu = 1 WHERE id = ?');
$stmt->execute([$id]);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Message de <?= htmlspecialchars($message['nom']) ?></title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<h1>Message de <?= htmlspecialchars($message['nom']) ?></h1>
<p><strong>Email :</strong> <?= htmlspecialchars($message['email']) ?></p>
<p><strong>Date :</strong> <?= htmlspecialchars($message['date_envoi']) ?></p>
<p><strong>Message :</strong><br><?= htmlspecialchars($message['message']) ?></p>
<a href="index.php">← Retour</a>
</body>
</html>