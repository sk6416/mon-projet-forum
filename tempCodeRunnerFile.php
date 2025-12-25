<?php
require_once 'includes/connexion.php';
require_once 'includes/header.php';

// Récupérer toutes les formations
try {
    $stmt = $pdo->query("SELECT * FROM formations ORDER BY categorie, titre");
    $formations = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Erreur lors de la récupération des formations : " . $e->getMessage();
    $formations = [];
}
?>

<h2>Nos Formations</h2>

<?php if (isset($error_message)): ?>
    <div class="alert alert-danger"><?= $error_message ?></div>
<?php endif; ?>

<?php if (empty($formations)): ?>
    <p>Aucune formation n'est disponible pour le moment.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Catégorie</th>
                <th>Durée</th>
                <th>Prix</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($formations as $formation): ?>
                <tr>
                    <td><?= htmlspecialchars($formation['titre']) ?></td>
                    <td><?= htmlspecialchars($formation['categorie']) ?></td>
                    <td><?= htmlspecialchars($formation['duree']) ?></td>
                    <td><?= number_format($formation['prix'], 2, ',', ' ') ?> €</td>
                    <td>
                        <a href="inscription.php?formation_id=<?= $formation['id'] ?>" class="btn">S'inscrire</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php
require_once 'includes/footer.php';
?>