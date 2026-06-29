<?php
session_start();
require_once '../includes/conexao.php';
require_once '../includes/verificar_login.php';

$usuario_id = $_SESSION['usuario_id'];

// --- CORREÇÃO DA BUSCA (PDO) ---
$stmt = $pdo->prepare("SELECT nome, email, foto_perfil FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(); // Com PDO, o fetch() já retorna o array direto

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $foto_atual = $usuario['foto_perfil'];
    $nova_foto = $foto_atual;

    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
        $extensao = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
        $formatos_permitidos = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($extensao, $formatos_permitidos)) {
            $novo_nome_foto = md5(time()) . '.' . $extensao;
            $diretorio_destino = '../assets/img/perfil/';

            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $diretorio_destino . $novo_nome_foto)) {
                $nova_foto = $novo_nome_foto;
            }
        } else {
            $erro = "Formato de imagem inválido.";
        }
    }

    // --- CORREÇÃO DO UPDATE (PDO) ---
    if (!isset($erro)) {
        $stmt_update = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, foto_perfil = ? WHERE id = ?");
        
        if ($stmt_update->execute([$nome, $email, $nova_foto, $usuario_id])) {
            $mensagem = "Perfil atualizado com sucesso!";
            $_SESSION['nome'] = $nome; 
            $usuario['nome'] = $nome;
            $usuario['email'] = $email;
            $usuario['foto_perfil'] = $nova_foto;
        } else {
            $erro = "Erro ao atualizar o perfil.";
        }
    }
}
?>