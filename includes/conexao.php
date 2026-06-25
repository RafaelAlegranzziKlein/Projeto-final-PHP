<?php
/**
 * conexao.php
 * Conexão com o banco de dados MySQL (XAMPP/WAMP)
 */

$host = "localhost";
$dbname = "portal_noticias";
$usuario_db = "root";
$senha_db = ""; // No XAMPP/WAMP o padrão geralmente é vazio

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $usuario_db,
        $senha_db,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
