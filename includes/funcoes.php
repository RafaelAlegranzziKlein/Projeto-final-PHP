<?php
/**
 * funcoes.php
 * Funções auxiliares: sessão, validação e controle de acesso por papel
 */

session_start();

/**
 * Verifica se há um usuário logado
 */
function estaLogado(): bool {
    return isset($_SESSION['usuario_id']);
}

/**
 * Retorna o tipo do usuário logado ('usuario' ou 'jornalista')
 */
function tipoUsuarioLogado(): ?string {
    return $_SESSION['usuario_tipo'] ?? null;
}

/**
 * Verifica se o usuário logado é jornalista
 */
function ehJornalista(): bool {
    return estaLogado() && tipoUsuarioLogado() === 'jornalista';
}

/**
 * Bloqueia o acesso à página caso o usuário não esteja logado
 */
function exigirLogin(): void {
    if (!estaLogado()) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Bloqueia o acesso à página caso o usuário não seja jornalista
 */
function exigirJornalista(): void {
    exigirLogin();
    if (!ehJornalista()) {
        header("Location: index.php?erro=acesso_negado");
        exit;
    }
}

/**
 * Sanitiza uma string de entrada
 */
function limpar(string $valor): string {
    return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
}

/**
 * Valida formato de e-mail
 */
function emailValido(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Gera um resumo (trecho) de um texto
 */
function resumirTexto(string $texto, int $limite = 150): string {
    $texto = strip_tags($texto);
    if (mb_strlen($texto) <= $limite) {
        return $texto;
    }
    return mb_substr($texto, 0, $limite) . '...';
}

/**
 * Formata data para exibição em pt-BR
 */
function formatarData(string $dataHora): string {
    $timestamp = strtotime($dataHora);
    return date('d/m/Y \à\s H:i', $timestamp);
}

/**
 * Verifica se o usuário logado é o autor de uma notícia
 */
function ehAutorDaNoticia(PDO $pdo, int $noticiaId): bool {
    if (!estaLogado()) {
        return false;
    }
    $stmt = $pdo->prepare("SELECT autor FROM noticias WHERE id = ?");
    $stmt->execute([$noticiaId]);
    $noticia = $stmt->fetch();

    if (!$noticia) {
        return false;
    }
    return (int)$noticia['autor'] === (int)$_SESSION['usuario_id'];
}
