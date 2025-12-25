<?php
// Démarrage de la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// INITIALISATION DU CHEMIN
// Si la page qui appelle le header n'a pas défini $path, on le met vide.
if (!isset($path)) { $path = ""; }

// Vérification des rôles (On vérifie juste la session, on n'inclut pas de fichier externe ici)
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$is_client = isset($_SESSION['client_logged_in']) && $_SESSION['client_logged_in'] === true;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form'Campus</title>
    
    <link rel="icon" type="image/png" href="<?= $path ?>assets/img/logo.png">
    <link rel="stylesheet" href="<?= $path ?>assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    
    <header class="glass-header">
        <div class="header-container">
            
            <a href="<?= $path ?>index.php" class="brand-logo">
                <img src="<?= $path ?>assets/img/logo.png" alt="Logo">
                <div class="brand-text">
                    <span class="text-form">Form'</span><span class="text-campus">Campus</span>
                </div>
            </a>

           <nav class="nav-menu">
    <ul>
        <li><a href="<?= $path ?>index.php" class="nav-link">Accueil</a></li>

        <?php if (!$is_admin): ?>
            <li><a href="<?= $path ?>formations.php" class="nav-link">Formations</a></li>
        <?php endif; ?>
        
        <?php if ($is_admin): ?>
            <li><a href="<?= $path ?>admin/admin_formations.php" class="nav-link admin-active">Gérer Formations</a></li>
            <li><a href="<?= $path ?>admin/admin_inscriptions.php" class="nav-link admin-active">Gérer Inscriptions</a></li>
        <?php endif; ?>
    </ul>
</nav>

            <div class="auth-buttons">
                <?php if ($is_admin): ?>
                    <span class="user-name" style="color:#dc3545; font-weight:bold;">Admin</span>
                    <a href="<?= $path ?>logout.php" class="btn-pill" style="border:1px solid #ccc; background:white; color:red;">Déconnexion</a>
                
                <?php elseif ($is_client): ?>
                    <a href="<?= $path ?>logout.php" class="btn-pill" style="border:1px solid #ccc;">Déconnexion</a>
                
                <?php else: ?>
                    <a href="<?= $path ?>login.php" class="nav-link" style="font-size:0.8rem; color:#999; margin-right:10px;" title="Accès Admin">Admin 🔒</a>
                    
                    <span style="color:#ccc;">|</span>
                    
                    <a href="<?= $path ?>login_client.php" class="nav-link" style="margin-left:10px;">Connexion</a>
                    <a href="<?= $path ?>inscription_client.php" class="btn-pill btn-primary">Rejoindre</a>
                <?php endif; ?>
            </div>

        </div>
    </header>
    
    <div class="header-spacer" style="height: 90px;"></div>
    
    <main class="container">