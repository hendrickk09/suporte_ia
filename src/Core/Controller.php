<?php
abstract class Controller
{
    protected function renderizar(string $view, array $dados = []): void
    {
        $dados['csrfToken'] = $this->csrfToken();
        $dados['csrfCampo'] = $this->csrfCampo();
        extract($dados, EXTR_SKIP);
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
        header('Cache-Control: no-store');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE); exit;
    }

    protected function exigirLogin(): void
    {
        if (!isset($_SESSION['usuario_id'])) $this->redirecionar('/login');
    }

    protected function exigirAdmin(): void
    {
        $this->exigirLogin();
        if (!in_array($_SESSION['usuario_perfil'] ?? '', ['admin', 'suporte'])) {
            $this->redirecionar('/meus-chamados');
        }
    }

    protected function isPost(): bool { return $_SERVER['REQUEST_METHOD'] === 'POST'; }

    protected function post(string $campo, string $pad = ''): string
    {
        $valor = $_POST[$campo] ?? $pad;
        return is_string($valor) ? trim($valor) : $pad;
    }

    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function csrfCampo(): string
    {
        return '<input type="hidden" name="_csrf" value="'
            . htmlspecialchars($this->csrfToken(), ENT_QUOTES, 'UTF-8')
            . '">';
    }

    protected function exigirCsrf(?string $token = null): void
    {
        $recebido = $token ?? $this->post('_csrf');
        $esperado = $_SESSION['csrf_token'] ?? '';

        // Comparação em tempo constante evita vazamento gradual do token.
        if (!$esperado || !$recebido || !hash_equals($esperado, $recebido)) {
            http_response_code(403);
            exit('Requisicao invalida. Atualize a pagina e tente novamente.');
        }
    }

    protected function flash(string $tipo, string $msg): void
    {
        $_SESSION['flash'] = ['tipo' => $tipo, 'mensagem' => $msg];
    }

    protected function perfil(): string { return $_SESSION['usuario_perfil'] ?? ''; }
    protected function uid(): int { return (int)($_SESSION['usuario_id'] ?? 0); }
    protected function isAdmin(): bool { return in_array($this->perfil(), ['admin', 'suporte']); }
}
