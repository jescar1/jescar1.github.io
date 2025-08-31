<?php
require_once __DIR__ . '/../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: /index.php'); exit;
}
if (!isset($_POST['csrf'], $_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  header('Location: /index.php?tab=register&type=error&msg=' . urlencode('CSRF inválido. Recarga e intenta de nuevo.')); exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$email  = strtolower(trim($_POST['email'] ?? ''));
$pass   = $_POST['password'] ?? '';

if ($nombre === '' || $email === '' || $pass === '') {
  header('Location: /index.php?tab=register&type=error&msg=' . urlencode('Completa todos los campos.')); exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  header('Location: /index.php?tab=register&type=error&msg=' . urlencode('Correo inválido.')); exit;
}
if (strlen($pass) < 6) {
  header('Location: /index.php?tab=register&type=error&msg=' . urlencode('La contraseña debe tener al menos 6 caracteres.')); exit;
}

try {
  $pdo = getPDO();
  $hash = password_hash($pass, PASSWORD_DEFAULT);
  $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)');
  $stmt->execute([$nombre, $email, $hash]);
  header('Location: /dashboard?type=success&msg=' . urlencode('Registro exitoso. Inicia sesión.'));
  exit;
} catch (PDOException $e) {
  header('Location: /index.php?tab=register&type=error&msg=' . urlencode('Ese correo ya está registrado.')); exit;
}
