<?php
/**
 * verifica_jornalista.php
 * Inclua no topo de páginas exclusivas para jornalistas
 * (ex.: nova_noticia.php, editar_noticia.php, excluir_noticia.php)
 */

require_once __DIR__ . '/funcoes.php';

exigirJornalista();
