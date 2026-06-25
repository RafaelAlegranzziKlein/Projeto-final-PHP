<?php
/**
 * dashboard.php
 * Painel do usuário logado.
 * - Jornalista: vê e gerencia apenas suas próprias notícias
 * - Usuário comum: vê seus dados de conta e seus comentários
 */

require_once __DIR__ . '/../includes/verificar_login.php';
require_once __DIR__ . '/../includes/conexao.php';

$minhasNoticias = [];
if (ehJornalista()) {
    $stmt = $pdo->prepare("
        SELECT id, titulo, data
        FROM noticias
        WHERE autor = ?
        ORDER BY data DESC
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $minhasNoticias = $stmt->fetchAll();
}

$meusComentarios = [];
if (!ehJornalista()) {
    $stmt = $pdo->prepare("
        SELECT c.comentario, c.data, n.titulo, n.id AS noticia_id
        FROM comentarios c
        JOIN noticias n ON n.id = c.noticia_id
        WHERE c.usuario_id = ?
        ORDER BY c.data DESC
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $meusComentarios = $stmt->fetchAll();
}

$tituloPagina = 'Minha Conta';
require __DIR__ . '/cabecalho.php';
?>

<h1 class="titulo-pagina">Minha Conta</h1>

<p>
    Olá, <strong><?= limpar($_SESSION['usuario_nome']) ?></strong>!
    Você está logado como <strong><?= ehJornalista() ? 'Jornalista' : 'Usuário' ?></strong>.
</p>

<div class="acoes-noticia" style="margin-bottom:2rem;">
    <a class="btn btn-secundario" href="editar_usuario.php">Editar minha conta</a>
    <a class="btn btn-perigo" href="excluir_usuario.php"
       onclick="return confirm('Tem certeza que deseja excluir sua conta? Essa ação não pode ser desfeita.');">Excluir minha conta</a>
</div>

<?php if (ehJornalista()): ?>
    <h2 class="titulo-pagina">Minhas Notícias</h2>
    <a class="btn btn-primario" href="nova_noticia.php" style="margin-bottom:1.2rem; display:inline-block;">+ Nova Notícia</a>

    <?php if (empty($minhasNoticias)): ?>
        <p>Você ainda não publicou nenhuma notícia.</p>
    <?php else: ?>
        <table class="tabela-dashboard">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($minhasNoticias as $n): ?>
                    <tr>
                        <td><a href="noticia.php?id=<?= (int)$n['id'] ?>"><?= limpar($n['titulo']) ?></a></td>
                        <td><?= formatarData($n['data']) ?></td>
                        <td>
                            <a href="editar_noticia.php?id=<?= (int)$n['id'] ?>">Editar</a> |
                            <a href="excluir_noticia.php?id=<?= (int)$n['id'] ?>"
                               onclick="return confirm('Excluir esta notícia?');">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

<?php else: ?>
    <h2 class="titulo-pagina">Meus Comentários</h2>

    <?php if (empty($meusComentarios)): ?>
        <p>Você ainda não fez nenhum comentário.</p>
    <?php else: ?>
        <?php foreach ($meusComentarios as $c): ?>
            <div class="comentario">
                <a href="noticia.php?id=<?= (int)$c['noticia_id'] ?>#comentarios"><strong><?= limpar($c['titulo']) ?></strong></a>
                <span class="data-comentario"><?= formatarData($c['data']) ?></span>
                <p><?= nl2br(limpar($c['comentario'])) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/rodape.php'; ?>
