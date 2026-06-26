<?php
/**
 * login.php
 * Formulário de login com verificação de credenciais
 */

require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/conexao.php';

if (estaLogado()) {
    header("Location: dashboard.php");
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = 'Preencha e-mail e senha.';
    } else {
        $stmt = $pdo->prepare("SELECT id, nome, senha, tipo FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];
            header("Location: dashboard.php");
            exit;
        } else {
            $erro = 'E-mail ou senha incorretos.';
        }
    }
}

$tituloPagina = 'Login';
require __DIR__ . '/cabecalho.php';
?>

<h1 class="titulo-pagina">Entrar</h1>

<?php if ($erro): ?>
    <div class="alerta alerta-erro"><?= limpar($erro) ?></div>
<?php endif; ?>



<?php require __DIR__ . '/rodape.php'; ?>
