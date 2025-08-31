<?php
// includes/db.php
// Provee getPDO() y $conn (mysqli) para compatibilidad

function getPDO() {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $host = 'sql212.infinityfree.com';
    $db   = 'if0_39744808_transporte';
    $user = 'if0_39744808';
    $pass = 'K6Zr4dtVhB2fuIt';
    $dsn  = "mysql:host={$host};dbname={$db};charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        error_log("DB PDO connect error: " . $e->getMessage());
        // Muestra mensaje mínimo (puedes comentar en producción)
        die("Error de conexión a la base de datos. Revisa error_log.");
    }

    return $pdo;
}

// También creamos $conn (mysqli) por si otros scripts lo esperan:
if (!isset($GLOBALS['conn']) || !($GLOBALS['conn'] instanceof mysqli)) {
    $mysqli = new mysqli('sql212.infinityfree.com', 'if0_39744808', 'K6Zr4dtVhB2fuIt', 'if0_39744808_transporte');
    if ($mysqli->connect_error) {
        error_log("MySQLi connect error: " . $mysqli->connect_error);
        // No morir aquí porque getPDO() ya cubre la conexión PDO.
    } else {
        $mysqli->set_charset("utf8mb4");
        $GLOBALS['conn'] = $mysqli;
    }
}
