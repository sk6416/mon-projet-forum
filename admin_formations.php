<?php
// COMMENTAIRE : On inclut la vérification de sécurité. Si pas admin, on vire la personne.
require_once '../includes/admin_check.php';

// --- CORRECTION DU STYLE ---
// On dit au header : "Recule d'un dossier pour trouver le CSS"
$path = '../'; 
require_once '../includes/header.php'; 
// ---------------------------

// COMMENTAIRE : Initialisation des variables pour éviter les erreurs "undefined variable"
$message = '';
$message_type = '';

// COMMENTAIRE : On récupère l'action dans l'URL (ex: admin_formations.php?action=edit).
// Si rien n'est précisé, par défaut on met 'list' (afficher la liste).
$action = $_GET['action'] ?? 'list'; // list, add, edit, delete

// COMMENTAIRE : On récupère l'ID s'il existe dans l'URL (pour modifier ou supprimer).
$formation_id = (int)($_GET['id'] ?? 0);

// ---------------------------------------------------------
// 1. TRAITEMENT DES FORMULAIRES (QUAND ON CLIQUE SUR ENREGISTRER)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // COMMENTAIRE : On récupère les données envoyées par le formulaire
    $titre = trim($_POST['titre'] ?? '');
    $categorie = trim($_POST['categorie'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $duree = trim($_POST['duree'] ?? '');
    $prix = (float)($_POST['prix'] ?? 0);
    $post_id = (int)($_POST['id'] ?? 0); // L'ID caché dans le formulaire

    // COMMENTAIRE : Vérification simple (champs obligatoires)
    if (empty($titre) || empty($categorie) || $prix <= 0) {
        $message = "Erreur : Titre, Catégorie et Prix sont obligatoires.";
        $message_type = 'danger';
        // On reste sur le formulaire (ajout ou edit) pour corriger
        $action = ($post_id > 0) ? 'edit' : 'add';
    } else {
        try {
            // COMMENTAIRE : Cas 1 - C'est un AJOUT (action = add)
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO formations (titre, categorie, description, duree, prix) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$titre, $categorie, $description, $duree, $prix]);
                $message = "Formation ajoutée avec succès !"; 
                $message_type = 'success'; 
                $action = 'list'; // Une fois fini, on retourne à la liste
            
            // COMMENTAIRE : Cas 2 - C'est une MODIFICATION (action = edit et on a un ID)
            } elseif ($action === 'edit' && $post_id > 0) {
                $stmt = $pdo->prepare("UPDATE formations SET titre=?, categorie=?, description=?, duree=?, prix=? WHERE id=?");
                $stmt->execute([$titre, $categorie, $description, $duree, $prix, $post_id]);
                $message = "Formation modifiée avec succès !"; 
                $message_type = 'success'; 
                $action = 'list'; // Une fois fini, on retourne à la liste
            }
        } catch (PDOException $e) {
            $message = "Erreur SQL : " . $e->getMessage(); 
            $message_type = 'danger';
        }
    }
}

// ---------------------------------------------------------
// 2. SUPPRESSION (QUAND ON CLIQUE SUR LA POUBELLE)
// ---------------------------------------------------------
if ($action === 'delete' && $formation_id > 0) {
    try {
        // COMMENTAIRE : On supprime la ligne qui correspond à l'ID
        $pdo->prepare("DELETE FROM formations WHERE id = ?")->execute([$formation_id]);
        $message = "Formation supprimée."; 
        $message_type = 'success'; 
        $action = 'list'; // On rafraîchit la liste
    } catch (PDOException $e) {
        $message = "Erreur suppression : " . $e->getMessage();
        $message_type = 'danger';
        $action = 'list';
    }
}

// ---------------------------------------------------------
// 3. AFFICHAGE (PARTIE HTML)
// ---------------------------------------------------------

// COMMENTAIRE : Si on a un message (succès ou erreur), on l'affiche ici
if ($message): ?>
    <div style="padding: 15px; margin-bottom: 20px; border-radius: 10px; background: <?= $message_type === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $message_type === 'success' ? '#155724' : '#721c24' ?>;">
        <?= $message ?>
    </div>
<?php endif;

// --- MODE LISTE (Affichage du tableau) ---
if ($action === 'list'):
    // COMMENTAIRE : On va chercher toutes les formations dans la base de données
    $formations = $pdo->query("SELECT * FROM formations ORDER BY id DESC")->fetchAll();
?>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2>Liste des Formations</h2>
        <a href="?action=add" class="btn-pill btn-primary" style="text-decoration:none; background:#007bff; color:white; padding:10px 20px;">+ Ajouter une formation</a>
    </div>

    <?php if (empty($formations)): ?>
        <p>Aucune formation pour le moment.</p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse; background:white; border-radius:10px; overflow:hidden; box-shadow:0 5px 15px rgba(0,0,0,0.05);">
            <thead style="background:#f1f3f5;">
                <tr>
                    <th style="padding:15px; text-align:left;">ID</th>
                    <th style="padding:15px; text-align:left;">Titre</th>
                    <th style="padding:15px; text-align:left;">Catégorie</th>
                    <th style="padding:15px; text-align:left;">Prix</th>
                    <th style="padding:15px; text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // COMMENTAIRE : On boucle sur chaque résultat pour créer une ligne <tr>
                foreach ($formations as $f): 
                ?>
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:15px;"><?= $f['id'] ?></td>
                        <td style="padding:15px;"><strong><?= htmlspecialchars($f['titre']) ?></strong></td>
                        <td style="padding:15px;"><?= htmlspecialchars($f['categorie']) ?></td>
                        <td style="padding:15px; color:#007bff; font-weight:bold;"><?= number_format($f['prix'], 2) ?> DH</td>
                        <td style="padding:15px; text-align:center; white-space:nowrap;">
                            <a href="?action=edit&id=<?= $f['id'] ?>" 
                               style="background:#ffc107; color:#333; padding:6px 12px; border-radius:20px; text-decoration:none; font-size:0.85rem; margin-right:5px; display:inline-block;">
                               ✏️ Modifier
                            </a>
                            <a href="?action=delete&id=<?= $f['id'] ?>" 
                               style="background:#dc3545; color:white; padding:6px 12px; border-radius:20px; text-decoration:none; font-size:0.85rem; display:inline-block;" 
                               onclick="return confirm('Supprimer définitivement ?');">
                               🗑️ Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

<?php 
// --- MODE FORMULAIRE (AJOUT ou EDITION) ---
// Si l'action est 'add' ou 'edit', on affiche le formulaire au lieu du tableau
elseif ($action === 'add' || $action === 'edit'): 
    
    // COMMENTAIRE : On prépare un tableau vide par défaut (pour l'ajout)
    $f = ['titre'=>'','categorie'=>'','description'=>'','duree'=>'','prix'=>0,'id'=>0];
    
    // COMMENTAIRE : Si c'est une MODIFICATION, on va chercher les infos de la formation dans la BDD pour pré-remplir les champs
    if ($action === 'edit' && $formation_id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM formations WHERE id=?"); 
        $stmt->execute([$formation_id]); 
        $f = $stmt->fetch();
    }
?>
    <h2><?= $action === 'edit' ? 'Modifier' : 'Ajouter' ?> une formation</h2>
    
    <div style="background:white; padding:30px; border-radius:15px; box-shadow:0 5px 20px rgba(0,0,0,0.05); max-width:600px; margin:0 auto;">
        <form method="POST" action="?action=<?= $action ?>">
            <input type="hidden" name="id" value="<?= $f['id'] ?>">
            
            <div style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; margin-bottom:5px;">Titre *</label>
                <input type="text" name="titre" value="<?= htmlspecialchars($f['titre']) ?>" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
            </div>
            
            <div style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; margin-bottom:5px;">Catégorie *</label>
                <input type="text" name="categorie" value="<?= htmlspecialchars($f['categorie']) ?>" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
            </div>
            
            <div style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; margin-bottom:5px;">Durée</label>
                <input type="text" name="duree" value="<?= htmlspecialchars($f['duree']) ?>" placeholder="Ex: 3 mois" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
            </div>
            
            <div style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; margin-bottom:5px;">Prix (DH) *</label>
                <input type="number" step="0.01" name="prix" value="<?= htmlspecialchars($f['prix']) ?>" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
            </div>
            
            <div style="margin-bottom:20px;">
                <label style="display:block; font-weight:600; margin-bottom:5px;">Description</label>
                <textarea name="description" rows="5" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-family:sans-serif;"><?= htmlspecialchars($f['description']) ?></textarea>
            </div>
            
            <button type="submit" style="background:#007bff; color:white; border:none; padding:12px 25px; border-radius:25px; font-size:1rem; cursor:pointer;">Enregistrer</button>
            <a href="admin_formations.php" style="color:#666; text-decoration:none; margin-left:15px;">Annuler</a>
        </form>
    </div>

<?php endif; ?>

</main> </body>
</html>