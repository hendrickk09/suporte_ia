<?php
abstract class Controller
{
    protected function renderizar(string $view, array $dados = []): void
    {
        extract($dados);
        $path = __DIR__ . '/../../views/' . $view . '.php';
        if (!file_exists($path)) throw new \RuntimeException("View não encontrada: $view");
        require_once $path;
    }

    protected function redirecionar(string $url): void
    {
        header('Location: '.APP_URL.$url); exit;
    }

    protected function json(array $dados, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE); exit;
    }

    protected function exigirLogin(): void
    {
        if (!isset($_SESSION['usuario_id'])) $this->redirecionar('/login');
    }

    protected function isPost(): bool { return $_SERVER['REQUEST_METHOD'] === 'POST'; }

    protected function post(string $campo, string $pad = ''): string
    {
        return htmlspecialchars(trim($_POST[$campo] ?? $pad), ENT_QUOTES, 'UTF-8');
    }

    protected function flash(string $tipo, string $msg): void
    {
        $_SESSION['flash'] = ['tipo' => $tipo, 'mensagem' => $msg];
    }
}
