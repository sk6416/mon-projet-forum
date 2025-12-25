<?php
// --- 1. SÉCURITÉ ET CHEMINS ---
// COMMENTAIRE : On vérifie que l'utilisateur est bien un administrateur connecté.
require_once '../includes/admin_check.php'; 

// COMMENTAIRE : On définit le chemin vers la racine pour que le header trouve les styles CSS.
$path = '../'; 
require_once '../includes/header.php'; // Affiche le menu du haut

// --- 2. LOGIQUE PHP (Base de données) ---
$message = '';
$message_type = '';

// COMMENTAIRE : On regarde si l'utilisateur a choisi un filtre dans la liste déroulante (ex: ?filter_formation=3)
$filter_formation_id = (int)($_GET['filter_formation'] ?? 0);

// ---------------------------------------------------------
// TRAITEMENT : SUPPRESSION D'UNE INSCRIPTION
// ---------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $inscription_id = (int)$_GET['id'];
    try {
        // COMMENTAIRE : On supprime l'inscription correspondant à l'ID
        $stmt = $pdo->prepare("DELETE FROM inscriptions WHERE id = ?");
        $stmt->execute([$inscription_id]);
        
        // COMMENTAIRE : Une fois supprimé, on recharge la page proprement via JavaScript 
        // pour retirer les paramètres ?action=delete de l'URL.
        echo "<script>window.location.href='admin_inscriptions.php';</script>";
        exit;
    } catch (PDOException $e) {
        $message = "Erreur suppression : " . $e->getMessage();
        $message_type = 'danger';
    }
}

// ---------------------------------------------------------
// RÉCUPÉRATION DES DONNÉES (POUR LE FILTRE ET LA LISTE)
// ---------------------------------------------------------

// COMMENTAIRE : On récupère la liste des formations pour remplir le menu déroulant "Filtrer par Formation"
try {
    $stmt = $pdo->query("SELECT id, titre FROM formations ORDER BY titre");
    $formations_list = $stmt->fetchAll();
} catch (PDOException $e) { $formations_list = []; }

// COMMENTAIRE : Requête principale pour afficher le tableau des inscriptions.
// On fait une JOINTURE (JOIN) avec la table 'formations' pour récupérer le TITRE de la formation
// au lieu d'avoir juste un numéro (id_formation).
$sql = "SELECT i.*, f.titre AS formation_titre 
        FROM inscriptions i
        JOIN formations f ON i.id_formation = f.id";

$params = [];

// COMMENTAIRE : Si un filtre est actif, on ajoute une condition WHERE à la requête SQL
if ($filter_formation_id > 0) {
    $sql .= " WHERE i.id_formation = :id_formation";
    $params[':id_formation'] = $filter_formation_id;
}

// COMMENTAIRE : On trie par date d'inscription (les plus récentes en premier)
$sql .= " ORDER BY i.date_inscription DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $inscriptions = $stmt->fetchAll(); // Contient toutes les inscriptions trouvées
} catch (PDOException $e) { $inscriptions = []; }
?>

<div class="admin-content" style="padding-top: 30px; padding-bottom: 50px;">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 30px;">
        <h2 style="color: #2c3e50; font-weight: 700;">Gérer les Inscriptions</h2>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?>"><?= $message ?></div>
    <?php endif; ?>

    <form method="GET" action="admin_inscriptions.php" class="form-group" style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 30px;">
        <label for="filter_formation" style="font-weight: 600; margin-right: 15px; color:#555;">Filtrer par Formation :</label>
        
        <select id="filter_formation" name="filter_formation" onchange="this.form.submit()" style="padding: 10px; border-radius: 8px; border: 1px solid #ddd; min-width: 250px;">
            <option value="0">-- Voir toutes les inscriptions --</option>
            <?php foreach ($formations_list as $formation): ?>
                <option value="<?= $formation['id'] ?>" <?= ($formation['id'] == $filter_formation_id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($formation['titre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <div class="table-responsive">
        <table class="table" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-collapse: separate; border-spacing: 0;">
            <thead style="background: #2c3e50; color: white;">
                <tr>
                    <th style="padding:15px;">ID</th>
                    <th style="padding:15px;">Candidat</th>
                    <th style="padding:15px;">Contact</th>
                    <th style="padding:15px;">Formation</th>
                    <th style="padding:15px;">Date</th>
                    <th style="padding:15px; text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // COMMENTAIRE : Si le tableau $inscriptions est vide, on affiche un message "Vide"
                if (empty($inscriptions)): 
                ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding: 50px; color: #666;">
                            <img src="../assets/img/empty.png" alt="" style="max-height: 50px; opacity:0.5; margin-bottom:10px;"><br>
                            <em>Aucune inscription trouvée.</em>
                        </td>
                    </tr>
                <?php else: ?>
                    
                    <?php 
                    // COMMENTAIRE : Sinon, on boucle sur chaque inscription pour créer une ligne <tr>
                    foreach ($inscriptions as $inscription): 
                    ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding:15px;">#<?= $inscription['id'] ?></td>
                            
                            <td style="padding:15px;">
                                <div style="font-weight: 600; color:#333;"><?= htmlspecialchars($inscription['nom'] . ' ' . $inscription['prenom']) ?></div>
                                <?php if(!empty($inscription['commentaire'])): ?>
                                    <small style="color:#888; font-style:italic;">"<?= htmlspecialchars(substr($inscription['commentaire'], 0, 40)) ?>..."</small>
                                <?php endif; ?>
                            </td>
                            
                            <td style="padding:15px;">
                                <div style="font-size:0.9rem;">📧 <?= htmlspecialchars($inscription['email']) ?></div>
                                <div style="font-size:0.9rem; margin-top:4px;">📞 <?= htmlspecialchars($inscription['tel']) ?></div>
                            </td>
                            
                            <td style="padding:15px;">
                                <span style="background:#e3f2fd; color:#0d47a1; padding:5px 10px; border-radius:20px; font-size:0.85rem; font-weight:500;">
                                    <?= htmlspecialchars($inscription['formation_titre']) ?>
                                </span>
                            </td>
                            
                            <td style="padding:15px; color:#666;"><?= date('d/m/Y', strtotime($inscription['date_inscription'])) ?></td>
                            
                            <td style="padding:15px; text-align:center;">
                                <a href="?action=delete&id=<?= $inscription['id'] ?>" 
                                   class="btn-delete"
                                   style="background: #dc3545; color: white; padding: 8px 12px; border-radius: 8px; text-decoration:none; font-size: 0.9rem; transition:0.3s;"
                                   onclick="return confirm('Supprimer définitivement cette inscription ?');">
                                   Supprimer
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Fermeture propre du HTML
echo "</main></body></html>";
?>