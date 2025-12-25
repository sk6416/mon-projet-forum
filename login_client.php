<?php
require_once 'includes/connexion.php';
session_start();

$message = '';
$message_type = '';

// Déconnexion
if (isset($_GET['logout'])) {
    // Si un client est connecté, on le déconnecte
    if (isset($_SESSION['client_logged_in'])) {
        unset($_SESSION['client_logged_in']);
        unset($_SESSION['client_data']);
    }
    header('Location: login_client.php');
    exit;
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $message = "Veuillez saisir votre email et votre mot de passe.";
        $message_type = 'danger';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $client = $stmt->fetch();

            // Vérification du mot de passe haché
            if ($client && password_verify($password, $client['password'])) {
                // Connexion réussie
                $_SESSION['client_logged_in'] = true;
                $_SESSION['client_data'] = [
                    'id' => $client['id'],
                    'nom' => $client['nom'],
                    'prenom' => $client['prenom'],
                    'email' => $client['email']
                ];
                
                // Redirection vers l'espace client
                header('Location: mon_compte.php');
                exit;
            } else {
                $message = "Email ou mot de passe incorrect.";
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
    <title>Connexion Client - Form'Campus</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Form'Campus - Connexion Client</h1>
        </div>
    </header>
    <main class="container">

<h2>Connexion Client</h2>

<?php if ($message): ?>
    <div class="alert alert-<?= $message_type ?>"><?= $message ?></div>
<?php endif; ?>

<form method="POST" action="login_client.php">
    <div class="form-group">
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" name="login" class="btn-submit">Se connecter</button>

    <div class="form-footer">
        Pas encore inscrit ? <a href="inscription_client.php">Créez votre compte ici.</a>
    </div>


</form>

<p>Pas encore inscrit ? <a href="inscription_client.php">Créez votre compte ici</a>.</p>

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
