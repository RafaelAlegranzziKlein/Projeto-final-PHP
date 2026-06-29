<?php
/**
 * _header.php
 * Cabeçalho do site (topbar, logo, navegação, ações do usuário)
 * Espera que $pdo, funcoes.php já tenham sido incluídos pela página chamadora.
 */

$logado = estaLogado();
$nomeUsuario = $_SESSION['usuario_nome'] ?? '';
$paginaAtual = basename($_SERVER['SCRIPT_NAME']);
?>
<div class="topbar">
  <div class="container">
    <span class="topbar__date"><?= limpar(ucfirst(strftime('%A, %d de %B de %Y', time()) ?: date('d/m/Y'))) ?></span>
    <div class="topbar__links">
      <?php if ($logado): ?>
        <span>Olá, <?= limpar($nomeUsuario) ?></span>
        <a href="logout.php">Sair</a>
      <?php else: ?>
        <a href="login.php">Entrar</a>
        <a href="cadastro.php">Cadastre-se</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<header class="header">
  <div class="container header__inner">
    <a href="index.php" class="logo">Folha<span>Digital</span></a>



    <div class="header__actions">
      <button class="icon-btn" aria-label="Buscar">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      </button>

      <?php if ($logado): ?>
        <a href="dashboard.php" class="icon-btn" aria-label="Minha conta">
          <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        </a>
      <?php else: ?>
        <a href="login.php" class="icon-btn" aria-label="Entrar">
          <svg viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
        </a>
      <?php endif; ?>

      <button class="menu-toggle" aria-label="Abrir menu" aria-expanded="false" id="menuToggle">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<nav class="nav--mobile" id="navMobile">
  <ul class="nav__list">
    <li><a href="index.php" class="nav__link">Início</a></li>

    <li><a href="noticias.php" class="nav__link">Notícias</a></li>
    <?php if ($logado): ?>
      <li><a href="logout.php" class="nav__link">Sair</a></li>
    <?php else: ?>
      <li><a href="login.php" class="nav__link">Entrar</a></li>
      <li><a href="cadastro.php" class="nav__link">Cadastre-se</a></li>
    <?php endif; ?>
  </ul>
</nav>

<div class="breaking">
  <div class="container">
    <span class="breaking__label">Urgente</span>
    <div class="breaking__ticker">
      <p>Acompanhe as últimas notícias do Brasil e do mundo, atualizadas a cada minuto aqui na Folha Digital.</p>
    </div>
  </div>
</div>