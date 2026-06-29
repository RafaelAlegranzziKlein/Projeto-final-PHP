<?php
/**
 * index.php
 * Página inicial — usa exatamente as colunas do banco: id, titulo, noticia, data, autor (INT → JOIN usuarios), imagem
 */

require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/conexao.php';

$tituloPagina = 'Folha Digital — Portal de Notícias';

// Notícia principal (a mais recente) — faz JOIN para pegar o nome do autor
$stmtDestaque = $pdo->query(
    "SELECT n.id, n.titulo, n.noticia, n.imagem, n.data, u.nome AS nome_autor
     FROM noticias n
     JOIN usuarios u ON u.id = n.autor
     ORDER BY n.data DESC
     LIMIT 1"
);
$destaquePrincipal = $stmtDestaque->fetch();

// Notícias secundárias do hero (as 4 seguintes)
$idDestaque = $destaquePrincipal['id'] ?? 0;
$stmtSecundarias = $pdo->prepare(
    "SELECT n.id, n.titulo, n.imagem, n.data
     FROM noticias n
     WHERE n.id != ?
     ORDER BY n.data DESC
     LIMIT 4"
);
$stmtSecundarias->execute([$idDestaque]);
$destaquesSecundarios = $stmtSecundarias->fetchAll();

// Grid de notícias recentes para o feed
$stmtRecentes = $pdo->query(
    "SELECT n.id, n.titulo, n.noticia, n.imagem, n.data, u.nome AS nome_autor
     FROM noticias n
     JOIN usuarios u ON u.id = n.autor
     ORDER BY n.data DESC
     LIMIT 9"
);
$noticiasRecentes = $stmtRecentes->fetchAll();

// Sidebar: 5 notícias recentes (sem visualizacoes — não existe na tabela)
$stmtMaisLidas = $pdo->query(
    "SELECT id, titulo FROM noticias ORDER BY data DESC LIMIT 5"
);
$maisLidas = $stmtMaisLidas->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= limpar($tituloPagina) ?></title>
  <meta name="description" content="Folha Digital — as principais notícias do Brasil e do mundo.">
  <link rel="stylesheet" href="./assets/css/main.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main>

  <!-- HERO -->
  <section class="hero">
    <div class="container hero__grid">

      <?php if ($destaquePrincipal): ?>
        <a href="noticias.php?id=<?= (int) $destaquePrincipal['id'] ?>" class="hero__main">
          <img
            src="<?= limpar($destaquePrincipal['imagem'] ?? './assets/img/placeholder.jpg') ?>"
            alt="<?= limpar($destaquePrincipal['titulo']) ?>">
          <div class="hero__overlay">
            <span class="badge">Destaque</span>
            <h1 class="hero__title"><?= limpar($destaquePrincipal['titulo']) ?></h1>
            <p class="hero__excerpt">
              <?= limpar(mb_substr(strip_tags($destaquePrincipal['noticia']), 0, 180)) ?>...
            </p>
            <div class="hero__meta">
              <?= limpar($destaquePrincipal['nome_autor']) ?> ·
              <?= date('d/m/Y', strtotime($destaquePrincipal['data'])) ?>
            </div>
          </div>
        </a>
      <?php endif; ?>

      <div class="hero__side">
        <?php foreach ($destaquesSecundarios as $item): ?>
          <a href="noticias.php?id=<?= (int) $item['id'] ?>" class="hero__side-item">
            <img
              src="<?= limpar($item['imagem'] ?? './assets/img/placeholder.jpg') ?>"
              alt="<?= limpar($item['titulo']) ?>">
            <div>
              <h3><?= limpar($item['titulo']) ?></h3>
              <span class="meta"><?= date('d/m/Y', strtotime($item['data'])) ?></span>
            </div>
          </a>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

  <!-- FEED PRINCIPAL -->
  <div class="main-content">
    <div class="container content-grid">

      <section>
        <div class="section-title">
          <h2>Últimas notícias</h2>
          <a href="noticias.php">Ver todas</a>
        </div>

        <div class="news-grid">
          <?php if (empty($noticiasRecentes)): ?>
            <p>Nenhuma notícia cadastrada ainda.</p>
          <?php else: ?>
            <?php foreach ($noticiasRecentes as $noticia): ?>
              <article class="card">
                <div class="card__image">
                  <img
                    src="<?= limpar($noticia['imagem'] ?? './assets/img/placeholder.jpg') ?>"
                    alt="<?= limpar($noticia['titulo']) ?>">
                </div>
                <div class="card__body">
                  <h3 class="card__title">
                    <a href="noticias.php?id=<?= (int) $noticia['id'] ?>"><?= limpar($noticia['titulo']) ?></a>
                  </h3>
                  <p class="card__excerpt">
                    <?= limpar(mb_substr(strip_tags($noticia['noticia']), 0, 120)) ?>...
                  </p>
                  <div class="card__meta">
                    <span><?= limpar($noticia['nome_autor']) ?></span>
                    <span>&middot;</span>
                    <span><?= date('d/m/Y', strtotime($noticia['data'])) ?></span>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div class="pagination">
          <a href="noticias.php" class="btn btn--primary">Ver mais notícias</a>
        </div>
      </section>

      <aside class="sidebar">
        <div class="widget">
          <h3 class="widget__title">Notícias recentes</h3>
          <ol class="ranked-list">
            <?php foreach ($maisLidas as $i => $item): ?>
              <li>
                <span class="number"><?= $i + 1 ?></span>
                <a href="noticias.php?id=<?= (int) $item['id'] ?>"><?= limpar($item['titulo']) ?></a>
              </li>
            <?php endforeach; ?>
          </ol>
        </div>

        <div class="widget newsletter">
          <h3 class="widget__title">Newsletter</h3>
          <p>Receba as principais notícias do dia direto no seu e-mail.</p>
          <form action="#" method="post">
            <input type="email" name="newsletter_email" placeholder="Seu e-mail" required>
            <button type="submit" class="btn btn--accent btn--block">Inscrever-se</button>
          </form>
        </div>

        <div class="ad-slot">Espaço publicitário</div>
      </aside>

    </div>
  </div>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>