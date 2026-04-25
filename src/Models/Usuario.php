<?php
class Usuario extends Model
{
    protected string $tabela = 'usuarios';

    public function porEmail(string $email): array|false
    {
        return $this->db->buscarUm("SELECT * FROM usuarios WHERE email = ?", [$email]);
    }

    public function registrar(string $nome, string $email, string $senha): int|false
    {
        if ($this->porEmail($email)) return false;
        return $this->inserir(['nome'=>$nome,'email'=>$email,'senha'=>password_hash($senha, PASSWORD_BCRYPT),'perfil'=>'usuario']);
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
        $_SESSION['usuario_id']    = $u['id'];
        $_SESSION['usuario_nome']  = $u['nome'];
        $_SESSION['usuario_perfil']= $u['perfil'];
    }

    public function encerrarSessao(): void { $_SESSION = []; session_destroy(); }

    public static function idLogado(): int  { return (int)($_SESSION['usuario_id'] ?? 0); }
    public static function perfil(): string { return $_SESSION['usuario_perfil'] ?? ''; }
}
