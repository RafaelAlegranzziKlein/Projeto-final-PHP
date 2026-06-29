<?php
/**
 * noticias.php
 * Colunas reais: id, titulo, noticia, data, autor (INT → JOIN usuarios), imagem
 * Sem: resumo, categoria, destaque, visualizacoes
 *
 * ?id=N       → exibe a notícia N
 * ?busca=X    → lista com filtro por título/texto
 * ?pagina=N   → paginação
 */

require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/conexao.php';

$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$busca  = trim($_GET['busca'] ?? '');
$pagina = isset($_GET['pagina']) ? max(1, (int) $_GET['pagina']) : 1;
$porPagina = 9;

/* ===========================================================
   MODO 1: VISUALIZAÇÃO DE UMA NOTÍCIA ESPECÍFICA (?id=)
   =========================================================== */
if ($id > 0) {

    $stmt = $pdo->prepare(
        "SELECT n.id, n.titulo, n.noticia, n.imagem, n.data, u.nome AS nome_autor
         FROM noticias n
         JOIN usuarios u ON u.id = n.autor
         WHERE n.id = ?"
    );
    $stmt->execute([$id]);
    $noticia = $stmt->fetch();

    if (!$noticia) {
        http_response_code(404);
        $tituloPagina = 'Notícia não encontrada';
    } else {
        $tituloPagina = $noticia['titulo'];

        // Notícias relacionadas: as 4 mais recentes, excluindo a atual
        $stmtRel = $pdo->prepare(
            "SELECT n.id, n.titulo, n.imagem, n.data, u.nome AS nome_autor
             FROM noticias n
             JOIN usuarios u ON u.id = n.autor
             WHERE n.id != ?
             ORDER BY n.data DESC
             LIMIT 4"
        );
        $stmtRel->execute([$id]);
        $relacionadas = $stmtRel->fetchAll();
    }
    ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= limpar($tituloPagina) ?> — Folha Digital</title>
  <link rel="stylesheet" href="./assets/css/main.css">
</head>
<body>

<?php include __DIR__ . '/includes/_header.php'; ?>

<main>
  <div class="main-content">
    <div class="container content-grid">

      <?php if (!$noticia): ?>

        <section>
          <h1 style="margin-bottom: var(--space-md);">Notícia não encontrada</h1>
          <p>A notícia que você procura não existe ou foi removida.</p>
          <a href="noticias.php" class="btn btn--primary" style="margin-top: var(--space-md);">
            Voltar para notícias
          </a>
        </section>

      <?php else: ?>

        <article>
          <h1 style="font-size: clamp(1.5rem, 4vw, 2.25rem); margin-bottom: var(--space-sm); line-height:1.25;">
            <?= limpar($noticia['titulo']) ?>
          </h1>

          <div class="card__meta" style="margin-bottom: var(--space-lg);">
            <span>Por <?= limpar($noticia['nome_autor']) ?></span>
            <span>&middot;</span>
            <span><?= date('d/m/Y \à\s H:i', strtotime($noticia['data'])) ?></span>
          </div>

          <?php if (!empty($noticia['imagem'])): ?>
            <img
              src="<?= limpar($noticia['imagem']) ?>"
              alt="<?= limpar($noticia['titulo']) ?>"
              style="width:100%; border-radius: var(--radius-lg); margin-bottom: var(--space-lg);">
          <?php endif; ?>

          <div style="font-size: 1.05rem; line-height: 1.85; color: var(--color-text);">
            <?= nl2br(limpar($noticia['noticia'])) ?>
          </div>

          <hr style="margin-block: var(--space-2xl); border: none; border-top: 1px solid var(--color-border);">

          <?php if (!empty($relacionadas)): ?>
            <div class="section-title">
              <h2>Veja também</h2>
            </div>
            <div class="news-grid">
              <?php foreach ($relacionadas as $rel): ?>
                <article class="card">
                  <div class="card__image">
                    <img
                      src="<?= limpar($rel['imagem'] ?? './assets/img/placeholder.jpg') ?>"
                      alt="<?= limpar($rel['titulo']) ?>">
                  </div>
                  <div class="card__body">
                    <h3 class="card__title">
                      <a href="noticias.php?id=<?= (int) $rel['id'] ?>"><?= limpar($rel['titulo']) ?></a>
                    </h3>
                    <div class="card__meta">
                      <span><?= limpar($rel['nome_autor']) ?></span>
                      <span>&middot;</span>
                      <span><?= date('d/m/Y', strtotime($rel['data'])) ?></span>
                    </div>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

        </article>

      <?php endif; ?>

      <aside class="sidebar">
        <div class="widget">
          <h3 class="widget__title">Notícias recentes</h3>
          <?php
            $stmtSide = $pdo->query("SELECT id, titulo FROM noticias ORDER BY data DESC LIMIT 5");
            $sideItems = $stmtSide->fetchAll();
            foreach ($sideItems as $i => $item):
          ?>
            <ol class="ranked-list">
              <li>
                <span class="number"><?= $i + 1 ?></span>
                <a href="noticias.php?id=<?= (int) $item['id'] ?>"><?= limpar($item['titulo']) ?></a>
              </li>
            </ol>
          <?php endforeach; ?>
        </div>
        <div class="ad-slot">Espaço publicitário</div>
      </aside>

    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/_footer.php'; ?>
</body>
</html>
<?php
    exit;
}

/* ===========================================================
   MODO 2: LISTAGEM COM BUSCA E PAGINAÇÃO
   =========================================================== */

$where = '';
$parametros = [];

if ($busca !== '') {
    $where = 'WHERE n.titulo LIKE ? OR n.noticia LIKE ?';
    $parametros = ['%' . $busca . '%', '%' . $busca . '%'];
}

// Total para paginação
$stmtTotal = $pdo->prepare("SELECT COUNT(*) AS total FROM noticias n $where");
$stmtTotal->execute($parametros);
$totalNoticias = (int) $stmtTotal->fetch()['total'];
$totalPaginas  = max(1, (int) ceil($totalNoticias / $porPagina));
$pagina        = min($pagina, $totalPaginas);
$offset        = ($pagina - 1) * $porPagina;

// Notícias da página atual
$sql = "SELECT n.id, n.titulo, n.noticia, n.imagem, n.data, u.nome AS nome_autor
        FROM noticias n
        JOIN usuarios u ON u.id = n.autor
        $where
        ORDER BY n.data DESC
        LIMIT $porPagina OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);
$noticias = $stmt->fetchAll();

$tituloPagina = $busca !== '' ? 'Resultados para "' . $busca . '"' : 'Todas as notícias';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= limpar($tituloPagina) ?> — Folha Digital</title>
  <link rel="stylesheet" href="./assets/css/main.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main>
  <div class="main-content">
    <div class="container content-grid">

      <section>
        <div class="section-title">
          <h1 style="font-size:1.5rem; font-weight:800; text-transform:uppercase;">
            <?= limpar($tituloPagina) ?>
          </h1>
        </div>

        <form action="noticias.php" method="get"
              style="margin-bottom: var(--space-lg); display:flex; gap: var(--space-sm);">
          <input
            type="search"
            name="busca"
            placeholder="Buscar notícias..."
            value="<?= limpar($busca) ?>"
            style="flex:1; padding: var(--space-sm) var(--space-md); border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
          <button type="submit" class="btn btn--primary">Buscar</button>
        </form>

        <div class="news-grid">
          <?php if (empty($noticias)): ?>
            <p>Nenhuma notícia encontrada.</p>
          <?php else: ?>
            <?php foreach ($noticias as $noticia): ?>
              <article class="card">
                <div class="card__image">
                  <img
                    src="<?= limpar($noticia['imagem'] ?? './assets/img/placeholder.jpg') ?>"
                    alt="<?= limpar($noticia['titulo']) ?>">
                </div>
                <div class="card__body">
                  <h2 class="card__title">
                    <a href="noticias.php?id=<?= (int) $noticia['id'] ?>"><?= limpar($noticia['titulo']) ?></a>
                  </h2>
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

        <?php if ($totalPaginas > 1): ?>
          <nav class="pagination" aria-label="Paginação" style="gap: var(--space-xs);">
            <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
              <a href="noticias.php?pagina=<?= $p ?><?= $busca ? '&busca=' . urlencode($busca) : '' ?>"
                 class="btn <?= $p === $pagina ? 'btn--primary' : '' ?>"
                 style="margin: 0 2px; min-width: 40px;">
                <?= $p ?>
              </a>
            <?php endfor; ?>
          </nav>
        <?php endif; ?>
      </section>

      <aside class="sidebar">
        <div class="widget">
          <h3 class="widget__title">Notícias recentes</h3>
          <?php
            $stmtSide = $pdo->query("SELECT id, titulo FROM noticias ORDER BY data DESC LIMIT 5");
            $sideItems = $stmtSide->fetchAll();
            foreach ($sideItems as $i => $s):
          ?>
            <ol class="ranked-list">
              <li>
                <span class="number"><?= $i + 1 ?></span>
                <a href="noticias.php?id=<?= (int) $s['id'] ?>"><?= limpar($s['titulo']) ?></a>
              </li>
            </ol>
          <?php endforeach; ?>
        </div>
        <div class="ad-slot">Espaço publicitário</div>
      </aside>

    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>