<?php
// 1. Démarrage de session (toujours au tout début)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Inclusions nécessaires
require_once 'includes/connexion.php';
require_once 'includes/header.php';

// 3. Récupération de toutes les formations
try {
    $stmt = $pdo->query("SELECT * FROM formations ORDER BY categorie, titre");
    $formations = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Erreur lors de la récupération des formations : " . $e->getMessage();
    $formations = [];
}

// ---------------------------------------------------------
// 4. LOGIQUE UTILISATEUR ET INSCRIPTIONS
// ---------------------------------------------------------
$mes_inscriptions = []; // Liste des IDs des formations où le client est déjà inscrit
$est_connecte = false;  // État de connexion

// Vérifier si le client est connecté
if (isset($_SESSION['client_logged_in']) && $_SESSION['client_logged_in'] === true) {
    $est_connecte = true;

    // Récupérer l'email (ou l'ID) stocké en session
    $email_client = $_SESSION['client_data']['email'] ?? ''; 

    // Si on a l'email, on cherche ses inscriptions existantes
    if (!empty($email_client)) {
        try {
            $stmt_inscript = $pdo->prepare("SELECT id_formation FROM inscriptions WHERE email = ?");
            $stmt_inscript->execute([$email_client]);
            
            // On stocke les IDs dans un tableau simple (ex: [1, 5, 8])
            $mes_inscriptions = $stmt_inscript->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            // Erreur silencieuse ou log
        }
    }
}
// ---------------------------------------------------------
?>

<div class="container" style="margin-top: 30px;">
    <h2>Nos Formations</h2>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>

    <?php if (empty($formations)): ?>
        <p>Aucune formation n'est disponible pour le moment.</p>
    <?php else: ?>
        
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f8f9fa; text-align: left;">
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Durée</th>
                    <th>Prix</th>
                    <th style="text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($formations as $formation): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($formation['titre']) ?></strong></td>
                        <td><?= htmlspecialchars($formation['categorie']) ?></td>
                        <td><?= htmlspecialchars($formation['duree']) ?></td>
                        <td><?= number_format($formation['prix'], 2, ',', ' ') ?> DH</td>
                        
                        <td style="text-align: center;">
                            <?php 
                            // --- CONDITION 1 : EST-IL DÉJÀ INSCRIT ? ---
                            if (in_array($formation['id'], $mes_inscriptions)): 
                            ?>
                                <span style="color: green; font-weight: bold;">
                                    ✔ Déjà inscrit
                                </span>

                            <?php else: ?>
                                
                                <?php 
                                // --- CONDITION 2 : EST-IL CONNECTÉ ? ---
                                if ($est_connecte): 
                                ?>
                                    <a href="inscription.php?formation_id=<?= $formation['id'] ?>" 
                                       style="display: inline-block; background-color: #007bff; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px;">
                                       S'inscrire au cours
                                    </a>

                                <?php else: ?>
                                    
                                    <a href="inscription_client.php" 
                                       style="display: inline-block; background-color: #ffc107; color: black; padding: 8px 12px; text-decoration: none; border-radius: 4px;">
                                       Se connecter pour s'inscrire
                                    </a>

                                <?php endif; ?>

                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
require_once 'includes/footer.php';
?>