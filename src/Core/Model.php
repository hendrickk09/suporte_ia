<?php
abstract class Model
{
    protected Database $db;
    protected string $tabela;
    protected string $pk = 'id';

    public function __construct() { $this->db = Database::getInstance(); }

    public function todos(string $ordem = 'id DESC'): array
    {
        return $this->db->buscarTodos("SELECT * FROM {$this->tabela} ORDER BY $ordem");
    }

    public function porId(int $id): array|false
    {
        return $this->db->buscarUm("SELECT * FROM {$this->tabela} WHERE {$this->pk} = ?", [$id]);
    }

    public function inserir(array $dados): int
    {
        $cols = implode(', ', array_keys($dados));
        $phs  = implode(', ', array_fill(0, count($dados), '?'));
        $this->db->executar("INSERT INTO {$this->tabela} ($cols) VALUES ($phs)", array_values($dados));
        return (int) $this->db->ultimoId();
    }

    public function atualizar(int $id, array $dados): bool
    {
        $set    = implode(' = ?, ', array_keys($dados)) . ' = ?';
        $vals   = array_values($dados);
        $vals[] = $id;
        return $this->db->executar("UPDATE {$this->tabela} SET $set WHERE {$this->pk} = ?", $vals)->rowCount() > 0;
    }

    public function contar(): int
    {
        return (int) ($this->db->buscarUm("SELECT COUNT(*) as t FROM {$this->tabela}")['t'] ?? 0);
    }
}
