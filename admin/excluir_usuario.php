<?php
/**
 * excluir_usuario.php
 * Exclusão de conta.
 * - Sem parâmetro "id": usuário exclui a própria conta.
 * - Com parâmetro "id" (apenas jornalista): exclui a conta de outro usuário.
 * Exige a senha atual para confirmar a exclusão.
 */

require_once __DIR__ . '/include/funcoes.php';
require_once __DIR__ . '/include/conexao.php';

if (!estaLogado()) {
    header("Location: login.php");
    exit;
}

$idAlvo = isset($_GET['id']) ? (int)$_GET['id'] : (int)$_SESSION['usuario_id'];
$excluindoOutro = $idAlvo !== (int)$_SESSION['usuario_id'];

// Apenas jornalistas podem excluir a conta de outra pessoa
if ($excluindoOutro && !ehJornalista()) {
    header("Location: dashboard.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id, nome, email, senha FROM usuarios WHERE id = ?");
$stmt->execute([$idAlvo]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header("Location: " . ($excluindoOutro ? 'usuarios.php' : 'dashboard.php'));
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senhaAtual = $_POST['senha_atual'] ?? '';

    if ($senhaAtual === '') {
        $erro = 'Informe a senha atual para confirmar a exclusão.';
    } elseif (!password_verify($senhaAtual, $usuario['senha'])) {
        $erro = 'Senha atual incorreta.';
    } else {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$idAlvo]);

        if ($excluindoOutro) {
            header("Location: usuarios.php");
            exit;
        } else {
            session_destroy();
            header("Location: login.php");
            exit;
        }
    }
}

$tituloPagina = 'Excluir Usuário';
require __DIR__ . '/cabecalho.php';
?>

<h1 class="titulo-pagina"><?= $excluindoOutro ? 'Excluir Usuário' : 'Excluir Minha Conta' ?></h1>

<?php if ($erro): ?>
    <div class="alerta alerta-erro"><?= limpar($erro) ?></div>
<?php endif; ?>

<div class="alerta alerta-erro">
    <?php if ($excluindoOutro): ?>
        Você está prestes a excluir a conta de <strong><?= limpar($usuario['nome']) ?></strong>
        (<?= limpar($usuario['email']) ?>). Essa ação não pode ser desfeita.
    <?php else: ?>
        Você está prestes a excluir a sua conta. Essa ação não pode ser desfeita e
        todos os seus dados serão removidos permanentemente.
    <?php endif; ?>
</div>

<form class="form-conta" method="post" action="excluir_usuario.php<?= $excluindoOutro ? '?id=' . (int)$idAlvo : '' ?>">
    <div class="campo">
        <label for="senha_atual">Digite sua senha para confirmar</label>
        <input type="password" id="senha_atual" name="senha_atual" autocomplete="current-password" required>
    </div>

    <div class="acoes-noticia">
        <button type="submit" class="btn btn-perigo"
                onclick="return confirm('Tem certeza? Essa ação é definitiva.');">Confirmar exclusão</button>
        <a class="btn btn-secundario" href="<?= $excluindoOutro ? 'usuarios.php' : 'dashboard.php' ?>">Cancelar</a>
    </div>
</form>

<?php require __DIR__ . '/rodape.php'; ?>