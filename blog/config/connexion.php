<?php
define('DB_HOST', 'sql209.infinityfree.com');
define('DB_NAME', 'if0_41918765_portfolio');
define('DB_USER', 'if0_41918765_root');
define('DB_PASS', 'Nanouche14');
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die('Erreur de connexion à la base de données.');
}
?>