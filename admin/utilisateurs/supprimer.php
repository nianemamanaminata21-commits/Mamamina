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

// Un admin ne peut pas supprimer son propre compte
if ($id === $_SESSION['admin_id']) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM administrateurs WHERE id = ?');
$stmt->execute([$id]);

header('Location: index.php');
exit;
?>