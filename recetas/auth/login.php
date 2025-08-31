<?php
// auth/login.php
require_once __DIR__ . '/../includes/db.php';
session_start();

// 1) Validación método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

// 2) CSRF (si lo usas)
if (!isset($_POST['csrf'], $_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    header('Location: /index.php?type=error&msg=' . urlencode('CSRF inválido.'));
    exit;
}

// 3) Inputs
$email = strtolower(trim($_POST['email'] ?? ''));
$pass  = $_POST['password'] ?? '';

if ($email === '' || $pass === '') {
    header('Location: /index.php?type=error&msg=' . urlencode('Ingresa correo y contraseña.'));
    exit;
}

// 4) Obtener usuario (intenta PDO, si falla usa mysqli)
$user = false;
try {
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
} catch (Throwable $e) {
    // Fallback a mysqli si existe $conn
    if (isset($GLOBALS['conn']) && $GLOBALS['conn'] instanceof mysqli) {
        $stmt = $GLOBALS['conn']->prepare('SELECT * FROM usuarios WHERE email = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $res = $stmt->get_result();
            $user = $res->fetch_assoc();
        }
    } else {
        error_log("Login DB error: " . $e->getMessage());
        header('Location: /index.php?type=error&msg=' . urlencode('Error interno.'));
        exit;
    }
}

// 5) Verificación de contraseña
if ($user) {
    $dbPass = $user['password'];

    // 1) si la contraseña en BD está hasheada correctamente:
    if (password_verify($pass, $dbPass)) {
        $ok = true;
    } 
    // 2) fallback: si la BD tiene contraseñas en texto plano (solo temporal para depuración)
    elseif ($pass === $dbPass) {
        $ok = true;
    } else {
        $ok = false;
    }

    if ($ok) {
        // Registro de sesión — guardamos AMBAS claves para compatibilidad:
        session_regenerate_id(true);
        $_SESSION['uid'] = $user['id'];            // tu código antiguo usa 'uid'
        $_SESSION['user_id'] = $user['id'];       // nuestros nuevos módulos usan 'user_id'
        $_SESSION['uname'] = $user['nombre'] ?? ($user['username'] ?? '');
        $_SESSION['username'] = $_SESSION['uname'];

        // Redirigir al dashboard
        header('Location: /dashboard.php');
        exit;
    }
}

// Si llegamos aquí: credenciales inválidas
header('Location: /index.php?type=error&msg=' . urlencode('Credenciales inválidas.'));
exit;
