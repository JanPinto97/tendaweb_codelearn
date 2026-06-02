<?php
// ========================================================
// admin/eliminar_producte.php
// Elimina un producte i torna al llistat
// ========================================================

require_once '../includes/db.php';
require_once '../includes/auth.php';

// Protegim la pàgina — només admins
protegirAdmin('../login.php');

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("DELETE FROM productes WHERE id = ?");
$stmt->execute([$id]);

header('Location: index.php');
exit;
