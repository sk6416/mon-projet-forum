<?php
// Configuration pour la V2
$host = '127.0.0.1';
$port = '3307';          // Port XAMPP (vérifie sur ton panneau si c'est toujours 3307 ou 3306)
$dbname = 'formcampus'; // <--- ICI : On cible la nouvelle base de données vue sur ton image
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // echo "Connecté à FormCampus V2 !"; 
} catch (PDOException $e) {
    die("Erreur de connexion V2 : " . $e->getMessage());
}
?>