<?php

require_once __DIR__ . '/../config/config.php';

$service = new GeminiService();
$method = new ReflectionMethod($service, 'minimizarDados');
$method->setAccessible(true);

$entrada = 'Email usuario@empresa.com telefone (11) 99999-9999 senha: Segredo123 IP 192.168.1.45';
$saida = $method->invoke($service, $entrada, 2000);

$falhas = [];
foreach (['usuario@empresa.com', '99999-9999', 'Segredo123', '192.168.1.45'] as $sensivel) {
    if (str_contains($saida, $sensivel)) {
        $falhas[] = "Dado sensivel nao mascarado: {$sensivel}";
    }
}

if ($falhas) {
    fwrite(STDERR, implode(PHP_EOL, $falhas) . PHP_EOL);
    exit(1);
}

echo "Security smoke test: OK" . PHP_EOL;
