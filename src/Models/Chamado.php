<?php
class Chamado extends Model
{
    protected string $tabela = 'chamados';

    public function todosComUsuario(?int $uid = null): array
    {
        $sql = "SELECT c.*, u.nome AS usuario_nome, a.nome AS atendente_nome
                FROM chamados c
                INNER JOIN usuarios u ON c.usuario_id = u.id
                LEFT  JOIN usuarios a ON c.atendente_id = a.id";
        $params = [];
        if ($uid) { $sql .= " WHERE c.usuario_id = ?"; $params[] = $uid; }
        $sql .= " ORDER BY c.criado_em DESC";
        return $this->db->buscarTodos($sql, $params);
    }

    public function detalhe(int $id): array|false
    {
        return $this->db->buscarUm(
            "SELECT c.*, u.nome AS usuario_nome, a.nome AS atendente_nome
             FROM chamados c
             INNER JOIN usuarios u ON c.usuario_id = u.id
             LEFT  JOIN usuarios a ON c.atendente_id = a.id
             WHERE c.id = ?", [$id]
        );
    }

    public function comentarios(int $chamadoId): array
    {
        return $this->db->buscarTodos(
            "SELECT cm.*, u.nome AS usuario_nome FROM comentarios cm
             INNER JOIN usuarios u ON cm.usuario_id = u.id
             WHERE cm.chamado_id = ? ORDER BY cm.criado_em ASC", [$chamadoId]
        );
    }

    public function addComentario(int $cid, int $uid, string $texto): void
    {
        $this->db->executar("INSERT INTO comentarios (chamado_id, usuario_id, texto) VALUES (?,?,?)", [$cid, $uid, $texto]);
    }

    public function salvarIA(int $id, string $cat, string $prio, string $analise, string $sugestao): bool
    {
        return $this->atualizar($id, ['categoria'=>$cat,'prioridade'=>$prio,'ia_analise'=>$analise,'ia_sugestao'=>$sugestao]);
    }

    public function contarPorStatus(): array
    {
        $rows = $this->db->buscarTodos("SELECT status, COUNT(*) as t FROM chamados GROUP BY status");
        $c    = ['aberto'=>0,'em_andamento'=>0,'resolvido'=>0,'fechado'=>0];
        foreach ($rows as $r) $c[$r['status']] = (int)$r['t'];
        return $c;
    }
}
