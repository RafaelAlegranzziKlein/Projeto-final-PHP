<?php
/**
 * noticias.php
 * Colunas reais: id, titulo, noticia, data, autor (INT → JOIN usuarios), imagem
 * Funcionalidades: visualização, listagem, busca, paginação, COMENTÁRIOS e LIKES
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

        // ── LIKES ──────────────────────────────────────────────
        // Conta total de likes
        $stmtLikes = $pdo->prepare("SELECT COUNT(*) AS total FROM likes_noticia WHERE noticia_id = ?");
        $stmtLikes->execute([$id]);
        $totalLikes = (int) $stmtLikes->fetch()['total'];

        // Verifica se o usuário logado já curtiu
        $usuarioJaCurtiu = false;
        if (estaLogado()) {
            $stmtMeuLike = $pdo->prepare(
                "SELECT id FROM likes_noticia WHERE noticia_id = ? AND usuario_id = ?"
            );
            $stmtMeuLike->execute([$id, $_SESSION['usuario_id']]);
            $usuarioJaCurtiu = (bool) $stmtMeuLike->fetch();
        }

        // Processa ação de curtir / descurtir (POST com token)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_like'])) {
            if (!estaLogado()) {
                header("Location: login.php?redirect=noticias.php?id=$id");
                exit;
            }
            $uid = (int) $_SESSION['usuario_id'];
            if ($usuarioJaCurtiu) {
                // Remove o like
                $stmtDel = $pdo->prepare("DELETE FROM likes_noticia WHERE noticia_id = ? AND usuario_id = ?");
                $stmtDel->execute([$id, $uid]);
            } else {
                // Insere o like (IGNORE evita duplicata se houver race condition)
                $stmtIns = $pdo->prepare("INSERT IGNORE INTO likes_noticia (noticia_id, usuario_id) VALUES (?, ?)");
                $stmtIns->execute([$id, $uid]);
            }
            header("Location: noticias.php?id=$id#likes");
            exit;
        }

        // ── COMENTÁRIOS ────────────────────────────────────────
        // Processa novo comentário (POST)
        $erroComentario = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_comentario'])) {
            if (!estaLogado()) {
                header("Location: login.php?redirect=noticias.php?id=$id");
                exit;
            }
            $texto = trim($_POST['texto_comentario'] ?? '');
            if ($texto === '') {
                $erroComentario = 'O comentário não pode estar vazio.';
            } elseif (mb_strlen($texto) > 1000) {
                $erroComentario = 'O comentário pode ter no máximo 1000 caracteres.';
            } else {
                $stmtCom = $pdo->prepare(
                    "INSERT INTO comentarios (noticia_id, usuario_id, texto) VALUES (?, ?, ?)"
                );
                $stmtCom->execute([$id, (int) $_SESSION['usuario_id'], $texto]);
                header("Location: noticias.php?id=$id#comentarios");
                exit;
            }
        }

        // Carrega comentários (com nome do autor)
        $stmtComs = $pdo->prepare(
            "SELECT c.id, c.texto, c.data, u.nome AS nome_autor, u.foto_perfil
             FROM comentarios c
             JOIN usuarios u ON u.id = c.usuario_id
             WHERE c.noticia_id = ?
             ORDER BY c.data ASC"
        );
        $stmtComs->execute([$id]);
        $comentarios = $stmtComs->fetchAll();
    }
    ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= limpar($tituloPagina) ?> — Folha Digital</title>
  <link rel="stylesheet" href="./assets/css/main.css">
  <style>
    /* ── Likes ── */
    .like-area { display:flex; align-items:center; gap:.75rem; margin-bottom:2rem; }
    .btn-like {
      display:inline-flex; align-items:center; gap:.4rem;
      padding:.45rem 1.1rem; border-radius:2rem;
      border: 2px solid var(--color-primary);
      background: transparent; cursor:pointer;
      font-size:.95rem; font-weight:600;
      color: var(--color-primary);
      transition: background .2s, color .2s;
    }
    .btn-like:hover, .btn-like.curtido { background: var(--color-primary); color:#fff; }
    .btn-like svg { width:18px; height:18px; stroke:currentColor; fill:none; stroke-width:2; }
    .like-count { font-weight:700; font-size:1rem; }

    /* ── Comentários ── */
    .comments-section { margin-top:2.5rem; }
    .comments-section h2 { font-size:1.3rem; margin-bottom:1.2rem; }
    .comment-card {
      display:flex; gap:.9rem;
      padding:.9rem 0;
      border-bottom:1px solid var(--color-border);
    }
    .comment-card:last-child { border-bottom:none; }
    .comment-avatar {
      width:40px; height:40px; border-radius:50%; object-fit:cover;
      flex-shrink:0; background:#ddd;
    }
    .comment-body { flex:1; }
    .comment-author { font-weight:700; font-size:.9rem; }
    .comment-date   { font-size:.8rem; color:var(--color-text-muted); margin-left:.5rem; }
    .comment-text   { margin-top:.3rem; font-size:.95rem; line-height:1.6; }
    .comment-form textarea {
      width:100%; min-height:90px;
      padding:.6rem .9rem;
      border:1px solid var(--color-border);
      border-radius:var(--radius-sm);
      background:var(--color-bg-alt);
      font-size:.95rem; resize:vertical;
    }
    .comment-form .btn { margin-top:.5rem; }
    .alert-inline { padding:.6rem .9rem; border-radius:var(--radius-sm); margin-bottom:.8rem;
                    background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; font-size:.9rem; }
    .login-prompt { background:var(--color-bg-alt); padding:1rem;
                    border-radius:var(--radius-sm); font-size:.95rem; margin-top:.8rem; }
  </style>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

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

          <!-- ── ÁREA DE LIKES ──────────────────────────────── -->
          <div id="likes" style="margin-top:2rem;">
            <form action="noticias.php?id=<?= $id ?>#likes" method="post">
              <input type="hidden" name="acao_like" value="1">
              <div class="like-area">
                <?php if (estaLogado()): ?>
                  <button type="submit"
                          class="btn-like <?= $usuarioJaCurtiu ? 'curtido' : '' ?>"
                          title="<?= $usuarioJaCurtiu ? 'Remover curtida' : 'Curtir esta notícia' ?>">
                    <svg viewBox="0 0 24 24">
                      <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"
                            <?= $usuarioJaCurtiu ? 'fill="currentColor"' : '' ?>></path>
                    </svg>
                    <?= $usuarioJaCurtiu ? 'Curtido' : 'Curtir' ?>
                  </button>
                <?php else: ?>
                  <a href="login.php?redirect=noticias.php?id=<?= $id ?>" class="btn-like">
                    <svg viewBox="0 0 24 24">
                      <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                    Curtir
                  </a>
                <?php endif; ?>
                <span class="like-count">
                  <?= $totalLikes ?> <?= $totalLikes === 1 ? 'curtida' : 'curtidas' ?>
                </span>
              </div>
            </form>
          </div>

          <!-- ── SEÇÃO DE COMENTÁRIOS ───────────────────────── -->
          <div id="comentarios" class="comments-section">
            <h2>Comentários (<?= count($comentarios) ?>)</h2>

            <?php if (empty($comentarios)): ?>
              <p style="color:var(--color-text-muted); font-size:.95rem;">
                Seja o primeiro a comentar!
              </p>
            <?php else: ?>
              <?php foreach ($comentarios as $com): ?>
                <div class="comment-card">
                  <?php
                    $avatarSrc = !empty($com['foto_perfil'])
                      ? 'assets/img/perfil/' . limpar($com['foto_perfil'])
                      : 'assets/img/placeholder.jpg';
                  ?>
                  <img class="comment-avatar"
                       src="<?= $avatarSrc ?>"
                       alt="<?= limpar($com['nome_autor']) ?>">
                  <div class="comment-body">
                    <span class="comment-author"><?= limpar($com['nome_autor']) ?></span>
                    <span class="comment-date"><?= date('d/m/Y H:i', strtotime($com['data'])) ?></span>
                    <p class="comment-text"><?= nl2br(limpar($com['texto'])) ?></p>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>

            <!-- Formulário de novo comentário -->
            <div style="margin-top:1.5rem;">
              <h3 style="font-size:1.05rem; margin-bottom:.8rem;">Deixe seu comentário</h3>

              <?php if (estaLogado()): ?>
                <?php if ($erroComentario): ?>
                  <div class="alert-inline" role="alert"><?= limpar($erroComentario) ?></div>
                <?php endif; ?>
                <form action="noticias.php?id=<?= $id ?>#comentarios" method="post" class="comment-form">
                  <input type="hidden" name="acao_comentario" value="1">
                  <textarea name="texto_comentario"
                            placeholder="Escreva seu comentário aqui..."
                            maxlength="1000"
                            required><?= limpar($_POST['texto_comentario'] ?? '') ?></textarea>
                  <button type="submit" class="btn btn--primary">Publicar comentário</button>
                </form>
              <?php else: ?>
                <div class="login-prompt">
                  <a href="login.php?redirect=noticias.php?id=<?= $id ?>">Entre</a> ou
                  <a href="cadastro.php">crie uma conta</a> para comentar.
                </div>
              <?php endif; ?>
            </div>
          </div>
          <!-- /comentarios -->

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

<?php include __DIR__ . '/includes/footer.php'; ?>
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

// Notícias da página atual (com contagem de likes e comentários via subquery)
$sql = "SELECT n.id, n.titulo, n.noticia, n.imagem, n.data, u.nome AS nome_autor,
               (SELECT COUNT(*) FROM likes_noticia l WHERE l.noticia_id = n.id) AS total_likes,
               (SELECT COUNT(*) FROM comentarios c   WHERE c.noticia_id  = n.id) AS total_comentarios
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
                    <span>&middot;</span>
                    <span>♥ <?= (int) $noticia['total_likes'] ?></span>
                    <span>&middot;</span>
                    <span>💬 <?= (int) $noticia['total_comentarios'] ?></span>
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
