<?php
/**
 * editar_perfil.php
 * Permite que o usuário logado edite APENAS o seu próprio perfil.
 * Localizado em /admin/ mas acessível a qualquer usuário logado.
 */

require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/conexao.php';

exigirLogin(); // redireciona para login.php se não estiver logado

$usuario_id = (int) $_SESSION['usuario_id'];

// Busca dados atuais do usuário logado
$stmt = $pdo->prepare("SELECT id, nome, email, foto_perfil FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    // Sessão inválida
    session_destroy();
    header("Location: ../login.php");
    exit;
}

$erro    = '';
$sucesso = '';

// ── Processamento do formulário ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome  = trim($_POST['nome']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $senhaAtual   = $_POST['senha_atual']   ?? '';
    $novaSenha    = $_POST['nova_senha']    ?? '';
    $confirmSenha = $_POST['confirmar_nova_senha'] ?? '';

    // Validações básicas
    if ($nome === '' || $email === '') {
        $erro = 'Nome e e-mail são obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um e-mail válido.';
    } else {
        // Verifica se o e-mail já está em uso por OUTRO usuário
        $stmtEmail = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $stmtEmail->execute([$email, $usuario_id]);
        if ($stmtEmail->fetch()) {
            $erro = 'Este e-mail já está sendo usado por outra conta.';
        }
    }

    // Lida com alteração de senha (opcional)
    $novaSenhaHash = null;
    if ($erro === '' && $novaSenha !== '') {
        // Para trocar a senha é preciso informar a senha atual
        $stmtSenha = $pdo->prepare("SELECT senha FROM usuarios WHERE id = ?");
        $stmtSenha->execute([$usuario_id]);
        $row = $stmtSenha->fetch();

        if (!password_verify($senhaAtual, $row['senha'])) {
            $erro = 'Senha atual incorreta.';
        } elseif (strlen($novaSenha) < 6) {
            $erro = 'A nova senha deve ter pelo menos 6 caracteres.';
        } elseif ($novaSenha !== $confirmSenha) {
            $erro = 'As novas senhas não coincidem.';
        } else {
            $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        }
    }

    // Upload de foto de perfil (opcional)
    $nova_foto = $usuario['foto_perfil'];
    if ($erro === '' && isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $extensao          = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
        $formatos_permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extensao, $formatos_permitidos)) {
            $erro = 'Formato de imagem inválido. Use JPG, PNG, GIF ou WEBP.';
        } elseif ($_FILES['foto_perfil']['size'] > 2 * 1024 * 1024) {
            $erro = 'A imagem não pode ultrapassar 2 MB.';
        } else {
            $diretorio = __DIR__ . '/../assets/img/perfil/';
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0755, true);
            }
            $novo_nome = md5(uniqid('', true)) . '.' . $extensao;
            if (!move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $diretorio . $novo_nome)) {
                $erro = 'Falha ao salvar a imagem. Verifique as permissões da pasta.';
            } else {
                $nova_foto = $novo_nome;
            }
        }
    }

    // Salva no banco apenas se não há erros
    if ($erro === '') {
        if ($novaSenhaHash) {
            $stmtUpdate = $pdo->prepare(
                "UPDATE usuarios SET nome = ?, email = ?, senha = ?, foto_perfil = ? WHERE id = ?"
            );
            $stmtUpdate->execute([$nome, $email, $novaSenhaHash, $nova_foto, $usuario_id]);
        } else {
            $stmtUpdate = $pdo->prepare(
                "UPDATE usuarios SET nome = ?, email = ?, foto_perfil = ? WHERE id = ?"
            );
            $stmtUpdate->execute([$nome, $email, $nova_foto, $usuario_id]);
        }

        // Atualiza sessão com novo nome
        $_SESSION['usuario_nome'] = $nome;

        $sucesso = 'Perfil atualizado com sucesso!';

        // Recarrega dados atualizados
        $stmt2 = $pdo->prepare("SELECT id, nome, email, foto_perfil FROM usuarios WHERE id = ?");
        $stmt2->execute([$usuario_id]);
        $usuario = $stmt2->fetch();
    }
}

$tituloPagina = 'Editar Perfil';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= limpar($tituloPagina) ?> — Folha Digital</title>
  <link rel="stylesheet" href="../assets/css/main.css">
  <style>
    .perfil-form { max-width: 560px; margin: 0 auto; }
    .perfil-avatar { display: flex; align-items: center; gap: 1.2rem; margin-bottom: 1.5rem; }
    .perfil-avatar img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover;
                         border: 3px solid var(--color-primary); }
    .form-group { margin-bottom: 1.2rem; }
    .form-group label { display: block; font-weight: 600; margin-bottom: .35rem; }
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="password"],
    .form-group input[type="file"] {
      width: 100%;
      padding: .55rem .9rem;
      border: 1px solid var(--color-border);
      border-radius: var(--radius-sm);
      background: var(--color-bg-alt);
      font-size: 1rem;
    }
    .form-divider { border: none; border-top: 1px solid var(--color-border); margin: 1.8rem 0; }
    .form-hint { font-size: .82rem; color: var(--color-text-muted); margin-top: .25rem; }
    .alert { padding: .75rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1.2rem; }
    .alert--error   { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
    .alert--success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
  </style>
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main>
  <div class="container" style="padding-block: var(--space-2xl);">

    <div class="perfil-form">

      <h1 style="font-family: var(--font-heading); margin-bottom: 1.5rem;">Editar Perfil</h1>

      <?php if ($erro): ?>
        <div class="alert alert--error" role="alert"><?= limpar($erro) ?></div>
      <?php endif; ?>

      <?php if ($sucesso): ?>
        <div class="alert alert--success" role="status"><?= limpar($sucesso) ?></div>
      <?php endif; ?>

      <!-- Avatar atual -->
      <div class="perfil-avatar">
        <?php
          $fotoSrc = !empty($usuario['foto_perfil'])
            ? '../assets/img/perfil/' . limpar($usuario['foto_perfil'])
            : '../assets/img/placeholder.jpg';
        ?>
        <img src="<?= $fotoSrc ?>" alt="Foto de perfil de <?= limpar($usuario['nome']) ?>">
        <div>
          <strong><?= limpar($usuario['nome']) ?></strong><br>
          <span style="font-size:.9rem; color: var(--color-text-muted);"><?= limpar($usuario['email']) ?></span>
        </div>
      </div>

      <form action="editar_perfil.php" method="post" enctype="multipart/form-data" novalidate>

        <!-- ── Dados pessoais ───────────────────────────────── -->
        <div class="form-group">
          <label for="nome">Nome completo</label>
          <input type="text" id="nome" name="nome"
                 value="<?= limpar($usuario['nome']) ?>"
                 required autocomplete="name">
        </div>

        <div class="form-group">
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email"
                 value="<?= limpar($usuario['email']) ?>"
                 required autocomplete="email">
        </div>

        <div class="form-group">
          <label for="foto_perfil">Foto de perfil</label>
          <input type="file" id="foto_perfil" name="foto_perfil"
                 accept="image/jpeg,image/png,image/gif,image/webp">
          <p class="form-hint">JPG, PNG, GIF ou WEBP. Máximo 2 MB. Deixe em branco para manter a atual.</p>
        </div>

        <hr class="form-divider">

        <!-- ── Alterar senha (opcional) ─────────────────────── -->
        <h2 style="font-size:1.1rem; margin-bottom: 1rem;">Alterar senha <span style="font-weight:400; font-size:.9rem; color:var(--color-text-muted);">(opcional)</span></h2>

        <div class="form-group">
          <label for="senha_atual">Senha atual</label>
          <input type="password" id="senha_atual" name="senha_atual"
                 autocomplete="current-password"
                 placeholder="Necessário apenas para trocar a senha">
        </div>

        <div class="form-group">
          <label for="nova_senha">Nova senha</label>
          <input type="password" id="nova_senha" name="nova_senha"
                 autocomplete="new-password" minlength="6"
                 placeholder="Mínimo 6 caracteres">
        </div>

        <div class="form-group">
          <label for="confirmar_nova_senha">Confirmar nova senha</label>
          <input type="password" id="confirmar_nova_senha" name="confirmar_nova_senha"
                 autocomplete="new-password" minlength="6"
                 placeholder="Repita a nova senha">
        </div>

        <div style="display:flex; gap:1rem; margin-top:1.5rem; flex-wrap:wrap;">
          <button type="submit" class="btn btn--primary">Salvar alterações</button>
          <a href="../index.php" class="btn">Cancelar</a>
        </div>

      </form>

    </div><!-- /.perfil-form -->

  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
