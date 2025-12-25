<?php
session_start();
require_once 'includes/connexion.php';

// Vérifier si le client est connecté
if (!isset($_SESSION['client_logged_in']) || $_SESSION['client_logged_in'] !== true) {
    header('Location: login_client.php');
    exit;
}

require_once 'includes/header.php';

$client_data = $_SESSION['client_data'];
?>

<h2>Mon Compte Client</h2>

<div class="alert alert-success">
    Bienvenue, <?= htmlspecialchars($client_data['prenom']) ?> ! Vous êtes connecté en tant que client.
</div>

<h3>Vos Informations Personnelles</h3>
<table>
    <tr>
        <th>Nom :</th>
        <td><?= htmlspecialchars($client_data['nom']) ?></td>
    </tr>
    <tr>
        <th>Prénom :</th>
        <td><?= htmlspecialchars($client_data['prenom']) ?></td>
    </tr>
    <tr>
        <th>Email :</th>
        <td><?= htmlspecialchars($client_data['email']) ?></td>
    </tr>
</table>

<p>
    <a href="formations.php" class="btn">S'inscrire à une nouvelle formation</a>
    <a href="./logout.php" class="btn btn-danger">Déconnexion</a>
</p>

<?php
require_once 'includes/footer.php';
?>
