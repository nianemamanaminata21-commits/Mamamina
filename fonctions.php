<?php
function champ_requis(string $valeur): bool {
    return !empty(trim($valeur));
}
function nettoyer(string $valeur): string {
    return htmlspecialchars(trim($valeur));
}
function enregister_visite($pdo,$page){
    $ip=$_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    $stmt=$pdo->prepare("INSERT INTO visites (adresse_ip, page) VALUES (?, ?)");
    $stmt->execute([$ip, $page]);
}
function generer_token_csrf(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function verifier_token_csrf($token){
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        die("Requete invalide.");
    }
}
?>