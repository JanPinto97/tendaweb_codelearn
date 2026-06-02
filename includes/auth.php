<?php
// ========================================================
// includes/auth.php
// Funcions d'autenticació i control d'accés
// ========================================================

// Iniciem la sessió si no està iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --------------------------------------------------------
// Comprova si hi ha un usuari autenticat
// --------------------------------------------------------
function estaAutenticat(): bool {
    return isset($_SESSION['usuari_id']);
}

// --------------------------------------------------------
// Comprova si l'usuari autenticat és administrador
// --------------------------------------------------------
function esAdmin(): bool {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

// --------------------------------------------------------
// Protegeix una pàgina d'admin
// Si no és admin, redirigeix al login indicat
// --------------------------------------------------------
function protegirAdmin(string $login_url = 'login.php'): void {
    if (!esAdmin()) {
        header("Location: $login_url");
        exit;
    }
}

// --------------------------------------------------------
// Protegeix una pàgina de client autenticat
// Si no està autenticat, redirigeix al login indicat
// --------------------------------------------------------
function protegirClient(string $login_url = 'login.php'): void {
    if (!estaAutenticat()) {
        header("Location: $login_url");
        exit;
    }
}
