<?php
/**
 * logout.php
 * Encerra a sessão do usuário
 */

require_once __DIR__ . '/funcoes.php';

$_SESSION = [];
session_destroy();

header("Location: index.php");
exit;
