<?php
/**
 * usuarios.php
 * Listagem de usuários do sistema.
 * Acesso restrito a jornalistas (papel administrativo).
 */

require_once __DIR__ . '/include/funcoes.php';
require_once __DIR__ . '/include/conexao.php';

if (!estaLogado()) {
    header("Location: login.php");
    exit;
}

if (!ehJornalista()) {
    header("Location: dashboard.php");
    exit;
}

$busca = trim($_GET['busca'] ?? '');

if ($busca !== '') {
    $stmt = $pdo->prepare("
        SELECT id, nome, email, tipo
        FROM usuarios
        WHERE nome LIKE ? OR email LIKE ?
        ORDER BY nome ASC
    ");
    $termo = '%' . $busca . '%';
    $stmt->execute([$termo, $termo]);
} else {
    $stmt = $pdo->query("
        SELECT id, nome, email, tipo
        FROM usuarios
        ORDER BY nome ASC
    ");
}

$usuarios = $stmt->fetchAll();

$tituloPagina = 'Usuários';
require __DIR__ . '/cabecalho.php';
?>

<h1 class="titulo-pagina">Usuários</h1>

<form class="form-busca" method="get" action="usuarios.php" style="margin-bottom:1.5rem;">
    <input type="text" name="busca" placeholder="Buscar por nome ou e-mail"
           value="<?= limpar($busca) ?>">
    <button type="submit" class="btn btn-secundario">Buscar</button>
</form>

<?php if (empty($usuarios)): ?>
    <p>Nenhum usuário encontrado.</p>
<?php else: ?>
    <table class="tabela-dashboard">
        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Tipo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= limpar($u['nome']) ?></td>
                    <td><?= limpar($u['email']) ?></td>
                    <td><?= $u['tipo'] === 'jornalista' ? 'Jornalista' : 'Usuário' ?></td>
                    <td>
                        <a href="editar_usuario.php?id=<?= (int)$u['id'] ?>">Editar</a> |
                        <a href="excluir_usuario.php?id=<?= (int)$u['id'] ?>"
                           onclick="return confirm('Tem certeza que deseja excluir este usuário? Essa ação não pode ser desfeita.');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/rodape.php'; ?>