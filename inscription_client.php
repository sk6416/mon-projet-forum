<?php
// 1. D'abord la connexion
require_once 'includes/connexion.php';

// Initialisation des variables
$nom = $prenom = $email = ''; // On initialise pour éviter les erreurs "undefined variable" dans le HTML
$message = '';
$message_type = '';

// ---------------------------------------------------------
// TRAITEMENT DU FORMULAIRE (AVANT LE HTML)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validation
    if (empty($nom) || empty($prenom) || empty($email) || empty($password) || empty($password_confirm)) {
        $message = "Veuillez remplir tous les champs.";
        $message_type = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Le format de l'adresse email est invalide.";
        $message_type = 'danger';
    } elseif ($password !== $password_confirm) {
        $message = "Les mots de passe ne correspondent pas.";
        $message_type = 'danger';
    } else {
        try {
            // Vérifier si l'email existe déjà
            $stmt = $pdo->prepare("SELECT id FROM clients WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $message = "Cet email est déjà utilisé. Veuillez vous connecter.";
                $message_type = 'danger';
            } else {
                // Hacher le mot de passe
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insertion
                $sql = "INSERT INTO clients (nom, prenom, email, password) VALUES (:nom, :prenom, :email, :password)";
                $stmt = $pdo->prepare($sql);
                
                $stmt->execute([
                    ':nom' => $nom,
                    ':prenom' => $prenom,
                    ':email' => $email,
                    ':password' => $hashed_password
                ]);

                // --- REDIRECTION VERS LA PAGE DE CONNEXION ---
                // On ajoute ?registered=1 pour afficher un message sur l'autre page
                header('Location: login_client.php?registered=1');
                exit; // On arrête le script ici
            }
        } catch (PDOException $e) {
            $message = "Erreur lors de l'inscription : " . $e->getMessage();
            $message_type = 'danger';
        }
    }
}

// ---------------------------------------------------------
// AFFICHAGE (Maintenant on inclut le header)
// ---------------------------------------------------------
require_once 'includes/header.php';



// Message spécial si on vient d'une tentative d'inscription sans compte
if (isset($_GET['auth']) && $_GET['auth'] == 1): ?>
    <div style="background-color: #fff3cd; color: #856404; padding: 15px; margin: 20px auto; max-width:600px; text-align: center; border: 1px solid #ffeeba; border-radius: 5px;">
        ⚠️ <strong>Compte requis</strong><br>
        Vous devez créer un compte (ou vous connecter) pour vous inscrire à nos formations.
    </div>
<?php endif; ?>
?>

<div class="container" style="max-width: 600px; padding-top: 40px;">

    <h2 style="text-align:center; margin-bottom: 30px;">Inscription (Nouveau Client)</h2>

    <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?>"><?= $message ?></div>
    <?php endif; ?>

    <div class="card" style="padding: 30px;">
        <form method="POST" action="inscription_client.php">
            <div class="form-group">
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($prenom) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email * (Servira d'identifiant)</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe *</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirmer le mot de passe *</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            
            <button type="submit" name="register" class="btn-submit">S'inscrire maintenant</button>

            <div class="form-footer">
                Déjà un compte ? <a href="login_client.php">Connectez-vous ici.</a>
            </div>
        </form>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>