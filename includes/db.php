<?php
// ========================================================
// includes/db.php
// Configuració i connexió a la base de dades mitjançant PDO
// ========================================================

// Constants de configuració de la base de dades
define('DB_HOST', 'localhost');   // Servidor de la base de dades
define('DB_NAME', 'botiga');      // Nom de la base de dades
define('DB_USER', 'root');        // Usuari de la base de dades
define('DB_PASS', '');            // Contrasenya (buida per defecte a XAMPP)
define('DB_CHARSET', 'utf8mb4'); // Joc de caràcters (suporta emojis i accents)

// --------------------------------------------------------
// Funció getDB()
// Retorna una instància única de PDO (patró Singleton)
// La connexió es crea una sola vegada per petició HTTP
// --------------------------------------------------------
function getDB(): PDO {
    // Variable estàtica: es manté entre crides a la funció
    static $pdo = null;

    // Si encara no s'ha creat la connexió, la creem
    if ($pdo === null) {
        // Construïm el DSN (Data Source Name) amb les constants definides
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

        // Opcions de configuració de PDO
        $opcions = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Llança excepcions en cas d'error
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Retorna els resultats com array associatiu
            PDO::ATTR_EMULATE_PREPARES   => false,                   // Usa sentències preparades reals (més segur)
        ];

        try {
            // Intentem crear la connexió PDO
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $opcions);
        } catch (PDOException $e) {
            // Si falla la connexió, aturem l'execució i mostrem l'error
            die("Error de connexió: " . $e->getMessage());
        }
    }

    // Retornem la connexió existent (o la que acabem de crear)
    return $pdo;
}