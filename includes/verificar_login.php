<?php
/**
 * verifica_login.php
 * Inclua este arquivo no topo de páginas que exigem autenticação.
 * Para páginas exclusivas de jornalista, use verifica_jornalista.php
 */

require_once __DIR__ . '/funcoes.php';

exigirLogin();
