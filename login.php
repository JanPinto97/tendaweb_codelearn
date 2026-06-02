<?php
// ========================================================
// login.php
// Formulari d'inici de sessió
// ========================================================

require_once 'includes/db.php';
require_once 'includes/auth.php';

$error = '';

// Processem el formulari quan s'envia per POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recollim i netejem les dades del formulari
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Comprovem que els camps no estiguin buits
    if (empty($email) || empty($password)) {
        $error = 'Omple tots els camps.';
    } else {
        // Busquem l'usuari a la BD per email
        $stmt = $pdo->prepare("SELECT * FROM usuaris WHERE email = ?");
        $stmt->execute([$email]);
        $usuari = $stmt->fetch();

        // Comprovem que existeix i que la contrasenya és correcta
        if ($usuari && password_verify($password, $usuari['password'])) {

            // Guardem les dades de l'usuari a la sessió
            $_SESSION['usuari_id'] = $usuari['id'];
            $_SESSION['nom']       = $usuari['nom'];
            $_SESSION['rol']       = $usuari['rol'];

            // Redirigim segons el rol
            if ($usuari['rol'] === 'admin') {
                header('Location: admin/index.php');
            } else {
                header('Location: index.php');
            }
            exit;

        } else {
            // Email o contrasenya incorrectes
            $error = 'Email o contrasenya incorrectes.';
        }
    }
}
$base  = './';
$titol = 'Iniciar sessió';
require_once 'includes/header.php';
?>

<div class="formulari-container">
    <h1>Iniciar sessió</h1>

    <!-- Mostrem l'error si n'hi ha -->
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <label for="password">Contrasenya</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Entrar</button>
    </form>

    <p>No tens compte? <a href="registre.php">Registra't</a></p>
</div>
<?php
include "includes/footer.php";
?>