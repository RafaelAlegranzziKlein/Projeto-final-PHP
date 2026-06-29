<?php
/**
 * cadastro.php
 * Tabela usuarios: id, nome, email, senha, foto_perfil
 * Sem: tipo, criado_em
 */

require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/conexao.php';

if (estaLogado()) {
    header("Location: index.php");
    exit;
}

$erro = '';
$sucesso = '';
$nomeDigitado  = '';
$emailDigitado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome           = trim($_POST['nome'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $senha          = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    $nomeDigitado  = $nome;
    $emailDigitado = $email;

    if ($nome === '' || $email === '' || $senha === '' || $confirmarSenha === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um e-mail válido.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($senha !== $confirmarSenha) {
        $erro = 'As senhas não coincidem.';
    } else {
        $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmtCheck->execute([$email]);

        if ($stmtCheck->fetch()) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            // Insere apenas as colunas que existem na tabela
            $stmtInsert = $pdo->prepare(
                "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)"
            );
            $stmtInsert->execute([$nome, $email, $senhaHash]);

            $sucesso = 'Cadastro realizado com sucesso! Você já pode entrar.';
            $nomeDigitado  = '';
            $emailDigitado = '';
        }
    }
}

$tituloPagina = 'Cadastro';
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

    <h1 style="font-family: var(--font-heading); margin-bottom: var(--space-lg);">Criar conta</h1>

    <?php if ($erro): ?>
      <div role="alert" style="
        background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;
        padding: var(--space-sm) var(--space-md); border-radius: var(--radius-sm);
        margin-bottom: var(--space-md);">
        <?= limpar($erro) ?>
      </div>
    <?php endif; ?>

    <?php if ($sucesso): ?>
      <div role="status" style="
        background: #dcfce7; color: #166534; border: 1px solid #86efac;
        padding: var(--space-sm) var(--space-md); border-radius: var(--radius-sm);
        margin-bottom: var(--space-md);">
        <?= limpar($sucesso) ?>
        <a href="login.php" style="font-weight:700; color: #166534;">Entrar agora</a>
      </div>
    <?php endif; ?>

    <?php if (!$sucesso): ?>
    <form action="cadastro.php" method="post" novalidate>

      <div style="margin-bottom: var(--space-md);">
        <label for="nome" style="display:block; margin-bottom: var(--space-xs); font-weight:600;">
          Nome completo
        </label>
        <input
          type="text" id="nome" name="nome"
          placeholder="Seu nome"
          value="<?= limpar($nomeDigitado) ?>"
          required autocomplete="name"
          style="width:100%; padding: var(--space-sm) var(--space-md);
                 border: 1px solid var(--color-border); border-radius: var(--radius-sm);
                 background: var(--color-bg-alt);">
      </div>

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

      <div style="margin-bottom: var(--space-md);">
        <label for="senha" style="display:block; margin-bottom: var(--space-xs); font-weight:600;">
          Senha
        </label>
        <input
          type="password" id="senha" name="senha"
          placeholder="Mínimo 6 caracteres"
          required minlength="6" autocomplete="new-password"
          style="width:100%; padding: var(--space-sm) var(--space-md);
                 border: 1px solid var(--color-border); border-radius: var(--radius-sm);
                 background: var(--color-bg-alt);">
      </div>

      <div style="margin-bottom: var(--space-lg);">
        <label for="confirmar_senha" style="display:block; margin-bottom: var(--space-xs); font-weight:600;">
          Confirmar senha
        </label>
        <input
          type="password" id="confirmar_senha" name="confirmar_senha"
          placeholder="Repita a senha"
          required minlength="6" autocomplete="new-password"
          style="width:100%; padding: var(--space-sm) var(--space-md);
                 border: 1px solid var(--color-border); border-radius: var(--radius-sm);
                 background: var(--color-bg-alt);">
      </div>

      <button type="submit" class="btn btn--primary btn--block">Cadastrar</button>
    </form>
    <?php endif; ?>

    <p style="margin-top: var(--space-lg); text-align:center; font-size: 0.9rem; color: var(--color-text-muted);">
      Já tem conta?
      <a href="login.php" style="color: var(--color-primary); font-weight:600;">Entrar</a>
    </p>

  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>