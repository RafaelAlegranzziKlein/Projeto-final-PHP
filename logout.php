<?php
/**
 * logout.php — Encerra a sessão e redireciona para o início
 */
require_once __DIR__ . '/includes/funcoes.php';

$_SESSION = [];
session_destroy();

header("Location: index.php");
exit;