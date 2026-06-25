<?php
/**
 * editar_usuario.php
 * Edição de dados da conta.
 * - Sem parâmetro "id": usuário edita a própria conta.
 * - Com parâmetro "id" (apenas jornalista): edita a conta de outro usuário.
 * Por segurança, exige a senha atual para confirmar as alterações.
 */

require_once __DIR__ . '/include/funcoes.php';
require_once __DIR__ . '/include/conexao.php';

if (!estaLogado()) {
    header("Location: login.php");
    exit;
}

$idAlvo = isset($_GET['id']) ? (int)$_GET['id'] : (int)$_SESSION['usuario_id'];
$editandoOutro = $idAlvo !== (int)$_SESSION['usuario_id'];

// Apenas jornalistas podem editar a conta de outra pessoa
if ($editandoOutro && !ehJornalista()) {
    header("Location: dashboard.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id, nome, email, senha, tipo FROM usuarios WHERE id = ?");
$stmt->execute([$idAlvo]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header("Location: " . ($editandoOutro ? 'usuarios.php' : 'dashboard.php'));
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome       = trim($_POST['nome'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $senhaAtual = $_POST['senha_atual'] ?? '';
    $novaSenha  = $_POST['nova_senha'] ?? '';

    if ($nome === '' || $email === '') {
        $erro = 'Preencha nome e e-mail.';
    } elseif ($senhaAtual === '') {
        $erro = 'Informe a senha atual para confirmar as alterações.';
    } elseif (!password_verify($senhaAtual, $usuario['senha'])) {
        $erro = 'Senha atual incorreta.';
    } else {
        if ($novaSenha !== '') {
            $hashNovaSenha = password_hash($novaSenha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?");
            $stmt->execute([$nome, $email, $hashNovaSenha, $idAlvo]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
            $stmt->execute([$nome, $email, $idAlvo]);
        }

        if (!$editandoOutro) {
            $_SESSION['usuario_nome'] = $nome;
        }

        $usuario['nome'] = $nome;
        $usuario['email'] = $email;
        $sucesso = 'Dados atualizados com sucesso.';
    }
}

$tituloPagina = 'Editar Usuário';
require __DIR__ . '/cabecalho.php';
?>

<h1 class="titulo-pagina"><?= $editandoOutro ? 'Editar Usuário' : 'Editar Minha Conta' ?></h1>

<?php if ($erro): ?>
    <div class="alerta alerta-erro"><?= limpar($erro) ?></div>
<?php endif; ?>

<?php if ($sucesso): ?>
    <div class="alerta alerta-sucesso"><?= limpar($sucesso) ?></div>
<?php endif; ?>

<form class="form-conta" method="post" action="editar_usuario.php<?= $editandoOutro ? '?id=' . (int)$idAlvo : '' ?>">
    <div class="campo">
        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" value="<?= limpar($usuario['nome']) ?>" required>
    </div>

    <div class="campo">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" value="<?= limpar($usuario['email']) ?>" required>
    </div>

    <div class="campo">
        <label for="nova_senha">Nova senha <small>(deixe em branco para manter a atual)</small></label>
        <input type="password" id="nova_senha" name="nova_senha" autocomplete="new-password">
    </div>

    <div class="campo">
        <label for="senha_atual">Senha atual <small>(obrigatória para confirmar)</small></label>
        <input type="password" id="senha_atual" name="senha_atual" autocomplete="current-password" required>
    </div>

    <div class="acoes-noticia">
        <button type="submit" class="btn btn-primario">Salvar alterações</button>
        <a class="btn btn-secundario" href="<?= $editandoOutro ? 'usuarios.php' : 'dashboard.php' ?>">Cancelar</a>
    </div>
</form>

<?php require __DIR__ . '/rodape.php'; ?>