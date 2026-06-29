<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit;
}

// Verifica se o tipo de usuário é 'jornalista' ou 'admin'
if ($_SESSION['tipo_usuario'] !== 'jornalista' && $_SESSION['tipo_usuario'] !== 'admin') {
    echo "<script>alert('Acesso negado. Apenas jornalistas podem acessar esta página.'); window.location.href='dashboard.php';</script>";
    exit;
}
?>