<?php require 'composants/navigation.php'; ?>
<?php
session_start();
require_once 'config/connexion.php';
require_once 'fonctions.php';
enregistrer_visite($pdo, 'contact');

$erreurs = [];
$succes = false;
$erreurs_demande = [];
$succes_demande = false;

// Formulaire contact
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_contact'])) {
    verifier_token_csrf($_POST['csrf_token'] ?? '');
    
    $nom = nettoyer($_POST['nom'] ?? '');
    $email = nettoyer($_POST['email'] ?? '');
    $message = nettoyer($_POST['message'] ?? '');

    if (!champ_requis($nom)) $erreurs[] = 'Le nom est obligatoire.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erreurs[] = 'Email invalide.';
    if (!champ_requis($message)) $erreurs[] = 'Le message est obligatoire.';

    if (empty($erreurs)) {
        $stmt = $pdo->prepare('INSERT INTO messages_contact (nom, email, message) VALUES (?, ?, ?)');
        $stmt->execute([$nom, $email, $message]);
        $succes = true;
    }
}

// Formulaire demande de projet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_demande'])) {
    verifier_token_csrf($_POST['csrf_token'] ?? '');

    $nom = nettoyer($_POST['nom'] ?? '');
    $email = nettoyer($_POST['email'] ?? '');
    $type_projet = nettoyer($_POST['type_projet'] ?? '');
    $description = nettoyer($_POST['description'] ?? '');
    $budget = nettoyer($_POST['budget'] ?? '');

    if (!champ_requis($nom)) $erreurs_demande[] = 'Le nom est obligatoire.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erreurs_demande[] = 'Email invalide.';
    if (!champ_requis($type_projet)) $erreurs_demande[] = 'Le type de projet est obligatoire.';
    if (!champ_requis($description)) $erreurs_demande[] = 'La description est obligatoire.';

    if (empty($erreurs_demande)) {
        $stmt = $pdo->prepare('INSERT INTO demandes_projet (nom, email, type_projet, description, budget) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$nom, $email, $type_projet, $description, $budget]);
        $succes_demande = true;
    }
}

$token = generer_token_csrf();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>    

<main>
    <section class="center">
        <h2>Contactez-moi</h2>
<?php if ($succes): ?>
    <p class="success">Votre message a été envoyé avec succès !</p>
<?php endif; ?>
        <form method="POST">
            <input type="text" name="prenom" placeholder="Prénom" required>
            <input type="text" name="nom" placeholder="Nom" required>
            <input type="email" name="email" placeholder="Email" required>
            <select name="sujet" id="sujet">
                <option value="">Sélectionnez un sujet</option>
                <option value="information">Demande d'information</option>
                <option value="projet">Demande de projet</option>
                <option value="autre">Autre</option>
            </select>
            
            <textarea name="message" placeholder="Votre message..." rows="5" required></textarea>
            <button type="submit">Envoyer</button>
        </form>
    </section>


    <h2>Demande de projet</h2>
    <?php
if (isset($_POST['demande_projet'])) {
   $demande = [
       'nom'         => nettoyer($_POST['nom']         ?? ''),
       'email'       => nettoyer($_POST['email']       ?? ''),
       'type_projet' => nettoyer($_POST['type_projet'] ?? ''),
       'description' => nettoyer($_POST['description'] ?? ''),
       'budget'      => nettoyer($_POST['budget']      ?? ''),
   ];
}
?>
    <form method="POST" class="form-contact">
    
        <input type="text" name="nom" placeholder="Votre nom" required>
    
        <input type="email" name="email" placeholder="Votre email" required>
    
        <input type="text" name="sujet" placeholder="Sujet du projet">
    
        <textarea name="description" placeholder="Décrivez votre projet..." rows="5"></textarea>
    
        <button type="submit">Envoyer</button>
    
    </form>
    <h1>Contact</h1>
    
    <p>Email :<a href="mailto:nianemamanaminata21@gmail">nianemamanaminata21@gmail.com</a></p>
    <p>Telephone:+221 70 506 38 07</p>
    <p><Address>Zac Mbao,cité Sagef</Address></p>
  
<footer>
    <div class="socials">
        <h2>Réseaux sociaux</h2>
        <a href="https://www.instagram.com/n.maaminata?igsh=aXNidTNzM2w1MDZu&utm_source=qr"><img
                src="images/logo insta.jpeg" alt="Instagram" width="50px"></a>
        <a href="https://wa.me/705063807"><img src="images/logo whatsapp.jpeg" alt="WhatsApp" width="50px"></a><br>
        <a href="https://www.tiktok.com/@so.mina1?_r=1&_t=ZS-95LHyIrwSag"><img src="images/logo tiktok.jpeg" alt="TikTok"
                width="50px"></a>
        <a href="https://github.com/dashboard" target="_blank"><img src="images/logo gitup.jpeg" alt="GitHub"
                width="50px"></a>
    
    </div>
</footer>
</main>
<?php require 'composants/pied-de-page.php'; ?>
</body>


</html>