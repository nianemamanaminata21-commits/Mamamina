<?php
session_start();
require_once '../../config/connexion.php';
require_once '../../fonctions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

verifier_token_csrf($_POST['csrf_token'] ?? '');

$id = intval($_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM projets WHERE id = ?');
$stmt->execute([$id]);
$projet = $stmt->fetch();

if (!$projet) {
    header('Location: index.php');
    exit;
}

// Supprimer l'image si elle existe
if ($projet['image']) {
    $chemin = '../../images/projets/' . $projet['image'];
    if (file_exists($chemin)) {
        unlink($chemin);
    }
}

$stmt = $pdo->prepare('DELETE FROM projets WHERE id = ?');
$stmt->execute([$id]);

header('Location: index.php');
exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../styles.css">
</head>
<body>
    
</body>
</html>