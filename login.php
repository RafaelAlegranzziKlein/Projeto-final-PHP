<?php
/**
 * login.php
 * Tabela usuarios: id, nome, email, senha, foto_perfil
 * Sem: tipo
 */

require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/conexao.php';

if (estaLogado()) {
    header("Location: index.php");
    exit;
}

$erro = '';
$emailDigitado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $emailDigitado = $email;

    if ($email === '' || $senha === '') {
        $erro = 'Preencha e-mail e senha.';
    } else {
        $stmt = $pdo->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id']   = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            header("Location: index.php");
            exit;
        } else {
            $erro = 'E-mail ou senha incorretos.';
        }
    }
}

$tituloPagina = 'Login';
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
  <div class="container" style="max-width: 480px; padding-block: var(--space-2xl);">

    <h1 style="font-family: var(--font-heading); margin-bottom: var(--space-lg);">Entrar</h1>

    <?php if ($erro): ?>
      <div role="alert" style="
        background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;
        padding: var(--space-sm) var(--space-md); border-radius: var(--radius-sm);
        margin-bottom: var(--space-md);">
        <?= limpar($erro) ?>
      </div>
    <?php endif; ?>

    <form action="login.php" method="post" novalidate>

      <div style="margin-bottom: var(--space-md);">
        <label for="email" style="display:block; margin-bottom: var(--space-xs); font-weight:600;">
          E-mail
        </label>
        <input
          type="email" id="email" name="email"
          placeholder="seu@email.com"
          value="<?= limpar($emailDigitado) ?>"
          required autocomplete="email"
          style="width:100%; padding: var(--space-sm) var(--space-md);
                 border: 1px solid var(--color-border); border-radius: var(--radius-sm);
                 background: var(--color-bg-alt);">
      </div>

      <div style="margin-bottom: var(--space-lg);">
        <label for="senha" style="display:block; margin-bottom: var(--space-xs); font-weight:600;">
          Senha
        </label>
        <input
          type="password" id="senha" name="senha"
          placeholder="Sua senha"
          required autocomplete="current-password"
          style="width:100%; padding: var(--space-sm) var(--space-md);
                 border: 1px solid var(--color-border); border-radius: var(--radius-sm);
                 background: var(--color-bg-alt);">
      </div>

      <button type="submit" class="btn btn--primary btn--block">Entrar</button>
    </form>

    <p style="margin-top: var(--space-lg); text-align:center; font-size: 0.9rem; color: var(--color-text-muted);">
      Ainda não tem conta?
      <a href="cadastro.php" style="color: var(--color-primary); font-weight:600;">Cadastre-se</a>
    </p>

  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>