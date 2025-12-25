<?php
session_start();
// On vide la session
$_SESSION = array();
// On détruit la session
session_destroy();
// On redirige vers l'accueil
header("Location: index.php");
exit;
?>