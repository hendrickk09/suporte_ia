<?php

$erros = [];
$avisos = [];

if (PHP_VERSION_ID < 80100) {
    $erros[] = 'PHP 8.1 ou superior e necessario. Versao atual: ' . PHP_VERSION;
}

foreach (['curl', 'json', 'mbstring', 'pdo', 'pdo_mysql', 'session'] as $extensao) {
    if (!extension_loaded($extensao)) {
        $erros[] = "Extensao PHP ausente: {$extensao}";
    }
}

$sessionPath = trim((string) ini_get('session.save_path'));
if ($sessionPath !== '') {
    $sessionPath = preg_replace('/^\d+;/', '', $sessionPath) ?? $sessionPath;
    if (!is_dir($sessionPath) || !is_writable($sessionPath)) {
        $avisos[] = "Diretorio de sessoes inexistente ou sem permissao de escrita: {$sessionPath}";
    }
}

$configLocal = __DIR__ . '/../config/config.local.php';
if (!is_file($configLocal)) {
    $avisos[] = 'config/config.local.php nao existe. Copie config/config.example.php antes de executar.';
} else {
    $config = require $configLocal;
    if (!is_array($config)) {
        $erros[] = 'config/config.local.php deve retornar um array.';
    } else {
        foreach (['DB_HOST', 'DB_NAME', 'DB_USER', 'APP_URL'] as $chave) {
            if (trim((string) ($config[$chave] ?? '')) === '') {
                $erros[] = "Configuracao obrigatoria ausente: {$chave}";
            }
        }

        $appUrl = (string) ($config['APP_URL'] ?? '');
        if ($appUrl !== '' && filter_var($appUrl, FILTER_VALIDATE_URL) === false) {
            $erros[] = 'APP_URL deve ser uma URL valida.';
        }

        $geminiKey = trim((string) ($config['GEMINI_API_KEY'] ?? ''));
        if ($geminiKey === '' || in_array($geminiKey, ['SUA_CHAVE_AQUI', 'COLE_SUA_CHAVE_AQUI'], true)) {
            $avisos[] = 'GEMINI_API_KEY nao foi configurada. O sistema funcionara com classificacao manual/fallback.';
        }
    }
}

echo 'PHP: ' . PHP_VERSION . PHP_EOL;
echo $erros ? 'Requisitos: FALHA' . PHP_EOL : 'Requisitos: OK' . PHP_EOL;

foreach ($erros as $erro) {
    echo "[ERRO] {$erro}" . PHP_EOL;
}
foreach ($avisos as $aviso) {
    echo "[AVISO] {$aviso}" . PHP_EOL;
}

exit($erros ? 1 : 0);
