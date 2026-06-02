<?php
// ========================================================
// includes/db.php
// Connexió a la base de dades
// ========================================================

// Dades de connexió
$host     = 'localhost';
$nom_bd   = 'botiga';
$usuari   = 'root';
$password = '1234'; // Contraseña per defecte es buida

// Creem la connexió PDO utilitzant les variables de configuració
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$nom_bd;charset=utf8mb4",
        $usuari,
        $password
    );

    // Mostrem els errors com excepcions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Els resultats es retornen com arrays associatius
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si falla la connexió, aturem l'execució
    die("Error de connexió: " . $e->getMessage());
}