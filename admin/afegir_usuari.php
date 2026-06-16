<?php
// ========================================================
// admin/afegir_usuari.php
// Formulari per afegir un usuari des del panell d'admin
// ========================================================

require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/funcions.php';

// Protegim la pàgina - només admins
protegirAdmin('../login.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom      = $_POST['nom'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $rol      = $_POST['rol'] ?? 'client';

    $resultat = crearUsuari($pdo, $nom, $email, $password, $rol);

    if (!$resultat['ok']) {
        $error = $resultat['error'];
    } else {
        header('Location: afegir_usuari.php?ok=1');
        exit;
    }
}

$base = '../';
require_once '../includes/header.php';
?>

<?php if (isset($_GET['ok']) && $_GET['ok'] === '1'): ?>
    <p class="avis-compra">Usuari creat correctament.</p>
<?php endif; ?>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<div class="admin-barra">
    <h2>Afegir usuari</h2>
    <a href="index.php" class="boto">Tornar</a>
</div>

<form method="POST" action="afegir_usuari.php" class="formulari-admin">
    <label for="nom">Nom</label>
    <input type="text" id="nom" name="nom" required
           value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required
           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

    <label for="password">Contrasenya</label>
    <input type="password" id="password" name="password" required>

    <label for="rol">Rol</label>
    <select id="rol" name="rol">
        <option value="client" <?= (($_POST['rol'] ?? 'client') === 'client') ? 'selected' : '' ?>>
            Client
        </option>
        <option value="admin" <?= (($_POST['rol'] ?? '') === 'admin') ? 'selected' : '' ?>>
            Administrador
        </option>
    </select>

    <button type="submit" class="boto boto-nou">Afegir usuari</button>
</form>

<?php require_once '../includes/footer.php'; ?>
