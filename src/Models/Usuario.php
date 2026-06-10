<?php
class Usuario extends Model
{
    protected string $tabela = 'usuarios';

    public function porEmail(string $email): array|false
    {
        return $this->db->buscarUm("SELECT * FROM usuarios WHERE email = ?", [mb_strtolower(trim($email))]);
    }

    public function registrar(string $nome, string $email, string $senha): int|false
    {
        if ($this->porEmail($email)) return false;
        return $this->inserir([
            'nome'   => $nome,
            'email'  => mb_strtolower(trim($email)),
            'senha'  => password_hash($senha, PASSWORD_DEFAULT),
            'perfil' => 'usuario'
        ]);
    }

    public function autenticar(string $email, string $senha): array|false
    {
        $u = $this->porEmail($email);
        if (!$u || !password_verify($senha, $u['senha'])) return false;
        return $u;
    }

    public function iniciarSessao(array $u): void
    {
        session_regenerate_id(true);
        $_SESSION['usuario_id']     = $u['id'];
        $_SESSION['usuario_nome']   = $u['nome'];
        $_SESSION['usuario_perfil'] = $u['perfil'];
    }

    public function encerrarSessao(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], '', $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public function atendentes(): array
    {
        return $this->db->buscarTodos(
            "SELECT id, nome, perfil FROM usuarios WHERE perfil IN ('admin','suporte') ORDER BY nome"
        );
    }

    public static function idLogado(): int    { return (int)($_SESSION['usuario_id']     ?? 0); }
    public static function perfil(): string   { return $_SESSION['usuario_perfil'] ?? ''; }
    public static function isAdmin(): bool    { return in_array(self::perfil(), ['admin','suporte']); }
}
