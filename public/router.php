<?php

$caminho = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$raizPublica = realpath(__DIR__);
$arquivo = realpath(__DIR__ . DIRECTORY_SEPARATOR . ltrim($caminho, '/'));

if (
    $caminho !== '/'
    && $raizPublica !== false
    && $arquivo !== false
    && str_starts_with($arquivo, $raizPublica . DIRECTORY_SEPARATOR)
    && is_file($arquivo)
) {
    return false;
}

require __DIR__ . '/index.php';
