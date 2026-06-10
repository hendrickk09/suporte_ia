<?php
class Chamado extends Model
{
    protected string $tabela = 'chamados';

    public function todosComUsuario(?int $uid = null): array
    {
        $sql    = "SELECT c.*, u.nome AS usuario_nome, a.nome AS atendente_nome
                   FROM chamados c
                   INNER JOIN usuarios u ON c.usuario_id = u.id
                   LEFT  JOIN usuarios a ON c.atendente_id = a.id";
        $params = [];
        if ($uid) { $sql .= " WHERE c.usuario_id = ?"; $params[] = $uid; }
        $sql .= " ORDER BY c.criado_em DESC";
        return $this->db->buscarTodos($sql, $params);
    }

    public function buscarPainel(array $filtros, int $limite, int $offset): array
    {
        [$where, $params] = $this->montarFiltros($filtros);
        $sql = "SELECT c.*, u.nome AS usuario_nome, a.nome AS atendente_nome
                FROM chamados c
                INNER JOIN usuarios u ON c.usuario_id = u.id
                LEFT JOIN usuarios a ON c.atendente_id = a.id
                {$where}
                ORDER BY c.criado_em DESC
                LIMIT {$limite} OFFSET {$offset}";
        return $this->db->buscarTodos($sql, $params);
    }

    public function contarPainel(array $filtros): int
    {
        [$where, $params] = $this->montarFiltros($filtros);
        $row = $this->db->buscarUm(
            "SELECT COUNT(*) AS total
             FROM chamados c
             INNER JOIN usuarios u ON c.usuario_id = u.id
             {$where}",
            $params
        );
        return (int) ($row['total'] ?? 0);
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

    public function comentarios(int $id): array
    {
        return $this->db->buscarTodos(
            "SELECT cm.*, u.nome AS usuario_nome, u.perfil AS usuario_perfil
             FROM comentarios cm
             INNER JOIN usuarios u ON cm.usuario_id = u.id
             WHERE cm.chamado_id = ? ORDER BY cm.criado_em ASC", [$id]
        );
    }

    public function addComentario(int $cid, int $uid, string $texto): void
    {
        $this->db->executar(
            "INSERT INTO comentarios (chamado_id, usuario_id, texto) VALUES (?,?,?)",
            [$cid, $uid, $texto]
        );
    }

    public function registrarHistorico(int $chamadoId, int $usuarioId, ?string $anterior, string $novo): void
    {
        $this->db->executar(
            "INSERT INTO historico_status (chamado_id, usuario_id, status_anterior, status_novo)
             VALUES (?,?,?,?)",
            [$chamadoId, $usuarioId, $anterior, $novo]
        );
    }

    public function historicoStatus(int $chamadoId): array
    {
        return $this->db->buscarTodos(
            "SELECT h.*, u.nome AS usuario_nome
             FROM historico_status h
             INNER JOIN usuarios u ON h.usuario_id = u.id
             WHERE h.chamado_id = ?
             ORDER BY h.criado_em DESC",
            [$chamadoId]
        );
    }

    public function salvarIA(int $id, string $cat, string $prio, string $analise, string $sugestao): bool
    {
        return $this->atualizar($id, [
            'categoria'  => $cat,
            'prioridade' => $prio,
            'ia_analise' => $analise,
            'ia_sugestao'=> $sugestao,
        ]);
    }

    public function contarPorStatus(): array
    {
        $rows = $this->db->buscarTodos("SELECT status, COUNT(*) as t FROM chamados GROUP BY status");
        $c    = ['aberto' => 0, 'em_andamento' => 0, 'resolvido' => 0, 'fechado' => 0];
        foreach ($rows as $r) $c[$r['status']] = (int)$r['t'];
        return $c;
    }

    public function contarPorUsuario(int $uid): array
    {
        $rows = $this->db->buscarTodos(
            "SELECT status, COUNT(*) as t FROM chamados WHERE usuario_id = ? GROUP BY status", [$uid]
        );
        $c = ['aberto' => 0, 'em_andamento' => 0, 'resolvido' => 0, 'fechado' => 0];
        foreach ($rows as $r) $c[$r['status']] = (int)$r['t'];
        return $c;
    }

    public function contarPorPrioridade(): array
    {
        $rows = $this->db->buscarTodos("SELECT prioridade, COUNT(*) AS t FROM chamados GROUP BY prioridade");
        $contagem = ['critica' => 0, 'alta' => 0, 'media' => 0, 'baixa' => 0];
        foreach ($rows as $row) {
            if (isset($contagem[$row['prioridade']])) {
                $contagem[$row['prioridade']] = (int) $row['t'];
            }
        }
        return $contagem;
    }

    private function montarFiltros(array $filtros): array
    {
        $condicoes = [];
        $params = [];

        if (!empty($filtros['busca'])) {
            $condicoes[] = '(c.titulo LIKE ? OR c.descricao LIKE ? OR u.nome LIKE ?)';
            $termo = '%' . $filtros['busca'] . '%';
            array_push($params, $termo, $termo, $termo);
        }
        if (!empty($filtros['status'])) {
            $condicoes[] = 'c.status = ?';
            $params[] = $filtros['status'];
        }
        if (!empty($filtros['prioridade'])) {
            $condicoes[] = 'c.prioridade = ?';
            $params[] = $filtros['prioridade'];
        }
        if (!empty($filtros['categoria'])) {
            $condicoes[] = 'c.categoria = ?';
            $params[] = $filtros['categoria'];
        }

        return [$condicoes ? 'WHERE ' . implode(' AND ', $condicoes) : '', $params];
    }
}
