<?php
session_start();
require_once '../includes/connexion.php'; // Connexion à la base de données

// Vérifier si l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Rediriger vers la page de connexion si non connecté
    header('Location: ../login.php');
    exit;
}

// Fonction pour inclure l'en-tête et le pied de page dans l'admin
function include_admin_header($title) {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title ?> - Form'Campus Admin</title>
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>
    <body>
        <header>
            <div class="container">
                <h1>Form'Campus - Administration</h1>
                <nav>
                    <ul>
                        <li><a href="admin_formations.php">Formations</a></li>
                        <li><a href="admin_inscriptions.php">Inscriptions</a></li>
                        
                        <li><a href="../logout.php">Déconnexion (<?= $_SESSION['admin_login'] ?>)</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        <main class="container">
    <?php
}

function include_admin_footer() {
    ?>
        </main>
        <footer>
            <div class="container">
                <p>&copy; <?php echo date("Y"); ?> Form'Campus. Administration.</p>
            </div>
        </footer>
        <script src="../assets/js/script.js"></script>
    </body>
    </html>
    <?php
}
?>
