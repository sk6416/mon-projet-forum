<?php
// 1. D'abord la connexion
require_once 'includes/connexion.php';

// Assure-toi que la session est démarrée (si pas fait dans connexion.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------------
// 2. VERIFICATION DE SECURITE & RECUPERATION SESSION
// ---------------------------------------------------------
$client_infos = [];

// On essaie de trouver où sont rangées les infos du client
if (isset($_SESSION['client']) && is_array($_SESSION['client'])) {
    $client_infos = $_SESSION['client']; // Cas standard
} elseif (isset($_SESSION['client_data']) && is_array($_SESSION['client_data'])) {
    $client_infos = $_SESSION['client_data']; // Cas alternatif
}

// Si aucune info trouvée -> Redirection vers connexion
if (empty($client_infos)) {
    header('Location: inscription_client.php?auth=1');
    exit;
}

// ---------------------------------------------------------
// 3. INITIALISATION (PRE-REMPLISSAGE)
// ---------------------------------------------------------
// On récupère les infos de la session pour pré-remplir le formulaire
// Utilisation de l'opérateur ?? '' pour éviter les erreurs si une info manque
$nom = $client_infos['nom'] ?? '';
$prenom = $client_infos['prenom'] ?? '';
$email = $client_infos['email'] ?? '';
$tel = $client_infos['tel'] ?? ''; // Si tu l'as en session
$commentaire = '';
$selected_formation_id = (int)($_GET['formation_id'] ?? 0);

$message = '';
$message_type = '';

// ---------------------------------------------------------
// 4. TRAITEMENT DU FORMULAIRE (SI SOUMIS)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // On écrase les variables avec ce qui vient du formulaire (au cas où il corrige son tel ou nom)
    // Note : L'email est souvent bloqué en modification pour éviter les erreurs
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    // $email = trim($_POST['email'] ?? ''); // On peut garder celui de la session par sécurité
    $tel = trim($_POST['tel'] ?? '');
    $id_formation = (int)($_POST['formation'] ?? 0);
    $commentaire = trim($_POST['commentaire'] ?? '');

    // Validation PHP
    if (empty($nom) || empty($prenom) || empty($email) || $id_formation === 0) {
        $message = "Veuillez remplir tous les champs obligatoires.";
        $message_type = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Le format de l'adresse email est invalide.";
        $message_type = 'danger';
    } else {
        try {
            // Vérification doublon
            $sql_check = "SELECT COUNT(*) FROM inscriptions WHERE email = :email AND id_formation = :id_formation";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->execute([':email' => $email, ':id_formation' => $id_formation]);
            
            if ($stmt_check->fetchColumn() > 0) {
                $message = "Attention : Vous êtes déjà inscrit à cette formation !";
                $message_type = 'warning';
            } else {
                // Insertion
                $sql = "INSERT INTO inscriptions (nom, prenom, email, tel, id_formation, commentaire) 
                        VALUES (:nom, :prenom, :email, :tel, :id_formation, :commentaire)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nom' => $nom,
                    ':prenom' => $prenom,
                    ':email' => $email,
                    ':tel' => $tel,
                    ':id_formation' => $id_formation,
                    ':commentaire' => $commentaire
                ]);

                // REDIRECTION VALIDEE
                header('Location: formations.php?success=1');
                exit; 
            }

        } catch (PDOException $e) {
            $message = "Erreur technique : " . $e->getMessage();
            $message_type = 'danger';
        }
    }
}

// ---------------------------------------------------------
// 5. LISTE DES FORMATIONS (Pour le menu déroulant)
// ---------------------------------------------------------
try {
    $stmt = $pdo->query("SELECT id, titre FROM formations ORDER BY titre");
    $formations_list = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = "Erreur chargement formations : " . $e->getMessage();
    $formations_list = [];
}

require_once 'includes/header.php';
?>

<div class="container" style="padding: 40px 0; max-width: 800px;">
    
    <h2 style="text-align: center; margin-bottom: 30px;">Finaliser mon inscription</h2>
    <p style="text-align: center; color: #666;">
        Vous êtes connecté en tant que <strong><?= htmlspecialchars($email) ?></strong>
    </p>

    <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?>"><?= $message ?></div>
    <?php endif; ?>

    <div class="card" style="padding: 30px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <form id="form-inscription" method="POST" action="">
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" class="form-control" 
                       value="<?= htmlspecialchars($nom) ?>" required style="width: 100%; padding: 8px;">
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" class="form-control" 
                       value="<?= htmlspecialchars($prenom) ?>" required style="width: 100%; padding: 8px;">
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="email">Email * (Non modifiable)</label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="<?= htmlspecialchars($email) ?>" readonly 
                       style="width: 100%; padding: 8px; background-color: #e9ecef; cursor: not-allowed;">
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="tel">Téléphone</label>
                <input type="tel" id="tel" name="tel" class="form-control" 
                       value="<?= htmlspecialchars($tel) ?>" style="width: 100%; padding: 8px;">
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="formation">Formation choisie *</label>
                <select id="formation" name="formation" required style="width: 100%; padding: 8px;">
                    <option value="">-- Sélectionnez une formation --</option>
                    <?php foreach ($formations_list as $formation): ?>
                        <option value="<?= $formation['id'] ?>" 
                            <?= ($formation['id'] == $selected_formation_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($formation['titre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="commentaire">Commentaire / Motivation</label>
                <textarea id="commentaire" name="commentaire" rows="5" style="width: 100%; padding: 8px;"><?= htmlspecialchars($commentaire) ?></textarea>
            </div>
            
            <button type="submit" class="btn-submit" 
                    style="background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">
                Valider mon inscription
            </button>
            
            <div class="form-footer" style="margin-top: 20px;">
                <a href="formations.php" style="text-decoration: none; color: #007bff;">← Retour à la liste des formations</a>
            </div>
        </form>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>