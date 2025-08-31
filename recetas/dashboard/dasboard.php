<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Mi Cocina</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Bienvenido a Mi Cocina</h1>
        <nav>
            <a href="recipes.php">🍲 Ver Recetas</a> |
            <a href="shopping_list.php">🛒 Lista de Compras</a> |
            <a href="logout.php">🚪 Cerrar Sesión</a>
        </nav>
    </div>
</body>
</html>