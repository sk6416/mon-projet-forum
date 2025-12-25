<?php
require_once 'includes/connexion.php';
session_start();

$message = '';
$message_type = '';

// Déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Si un client est connecté, le déconnecter avant de tenter la connexion admin
if (isset($_SESSION['client_logged_in'])) {
    unset($_SESSION['client_logged_in']);
    unset($_SESSION['client_data']);
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        $message = "Veuillez saisir votre identifiant et votre mot de passe.";
        $message_type = 'danger';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE login = :login");
            $stmt->execute([':login' => $login]);
            $user = $stmt->fetch();

            // Vérification du mot de passe
            // NOTE: Pour la démonstration, nous allons utiliser une vérification simple
            // En production, il faudrait utiliser password_verify() avec un hash généré par password_hash()
            // Le mot de passe pour 'admin' est 'admin'
            if ($user && $password === 'admin') {
                // Connexion réussie
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_login'] = $user['login'];
                
                // Redirection vers la page d'administration
                header('Location: admin/admin_formations.php');
                exit;
            } else {
                $message = "Identifiant ou mot de passe incorrect.";
                $message_type = 'danger';
            }
        } catch (PDOException $e) {
            $message = "Erreur de connexion : " . $e->getMessage();
            $message_type = 'danger';
        }
    }
}

// Inclure l'en-tête (sans la navigation pour la page de login)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - Form'Campus</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Form'Campus - Connexion Admin</h1>
        </div>
    </header>
    <main class="container">

<h2>Connexion Administrateur</h2>

<?php if ($message): ?>
    <div class="alert alert-<?= $message_type ?>"><?= $message ?></div>
<?php endif; ?>

<form method="POST" action="login.php">
    <div class="form-group">
        <label for="login">Identifiant :</label>
        <input type="text" id="login" name="login" required>
    </div>
    <div class="form-group">
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>
    </div>
   <button type="submit" name="login_admin" class="btn-submit">Connexion Admin</button>

    <div class="form-footer">
        <a href="../index.php">← Retour au site</a>
    </div>


</form>

<?php
// Inclure le pied de page
?>
    </main>
    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Form'Campus. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
