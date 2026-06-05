<?php
session_start();
require_once '../../config/connexion.php';
require_once '../../fonctions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../connexion.php');
    exit;
}
$messages=$pdo->query('SELECT * FROM messages_contact ORDER BY date DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages de contact</title>
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
    <h1>Messages de contact</h1>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Message</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $message): ?>
            <tr>
                <td><?= htmlspecialchars($message['nom']) ?></td>
                <td><?= htmlspecialchars($message['email']) ?></td>
                <td><?= htmlspecialchars($message['message']) ?></td>
                <td><?= htmlspecialchars($message['date']) ?></td>
                <td><?= htmlspecialchars($message['statut']) ?></td>
                <td><?= $message['statut'] === 'non lu' ? '<strong>Non lu</strong>' : 'Lu' ?></td>
                <td>
                    <a href="modifier.php?id=<?= $message['id'] ?>">Modifier</a>
                    <a href="supprimer.php?id=<?= $message['id'] ?>">Supprimer</a>
                    <a href="voir.php?id=<?=$message['id']?>">Voir</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <a href="../dashboard.php">Dashboard</a>
        </tbody>
    </table>
</body>
</html>