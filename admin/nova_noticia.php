<?php
require_once '../includes/conexao.php';
require_once '../includes/verificar_jornalista.php'; // Bloqueia quem não é jornalista

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $conteudo = $_POST['conteudo'];
    $autor_id = $_SESSION['usuario_id'];
    
    // Processamento de imagem da notícia (opcional)
    $imagem_noticia = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $novo_nome = uniqid() . '.' . $extensao;
        $diretorio = '../assets/img/noticias/';
        
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $diretorio . $novo_nome)) {
            $imagem_noticia = $novo_nome;
        }
    }

    // Usando PDO corretamente para inserir os dados
    $stmt = $pdo->prepare("INSERT INTO noticias (titulo, conteudo, autor_id, imagem) VALUES (?, ?, ?, ?)");
    
    // O array obedece a ordem dos "?" na query acima
    if ($stmt->execute([$titulo, $conteudo, $autor_id, $imagem_noticia])) {
        $mensagem = "Notícia criada com sucesso!";
    } else {
        $erro = "Erro ao criar notícia.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Notícia</title>
    <link rel="stylesheet" href="../assets/css/base.css">
</head>
<body>
    <h2>Criar Nova Notícia</h2>
    <?php if(isset($mensagem)) echo "<p style='color:green;'>$mensagem</p>"; ?>
    <?php if(isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>

    <form action="nova_noticia.php" method="POST" enctype="multipart/form-data">
        <label>Título:</label><br>
        <input type="text" name="titulo" required><br><br>

        <label>Conteúdo:</label><br>
        <textarea name="conteudo" rows="10" required></textarea><br><br>

        <label>Imagem da Notícia:</label><br>
        <input type="file" name="imagem" accept="image/*"><br><br>

        <button type="submit">Publicar Notícia</button>
    </form>
</body>
</html>