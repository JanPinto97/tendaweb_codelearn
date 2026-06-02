<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';


$id = (int)$_GET['id'];

$stmt = $pdo->prepare("DELETE FROM productes WHERE id = ?");
$stmt->execute([$id]);

header('Location: index.php');
exit;