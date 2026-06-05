<?php
$page_courante=basename($_SERVER['PHP_SELF']);
?>
<header>
    <nav>
       <a href="accueil.php" <?= $page_courante === 'accueil.php' ? 'class="actif"' : '' ?>>Accueil</a>
        <?php if (isset($_SESSION['utilisateur_id'])): ?>
            <a href="mes-articles.php">Mes articles</a>
            <a href="profil.php">Profil</a>
            <a href="deconnexion.php">Déconnexion</a>
        <?php else: ?>
            <a href="inscription.php">Inscription</a>
            <a href="connexion.php">Connexion</a>
        <?php endif; ?>
    </nav>
</header>