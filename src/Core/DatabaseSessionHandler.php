<?php

class DatabaseSessionHandler implements SessionHandlerInterface
{
    private Database $db;

    public function open(string $path, string $name): bool
    {
        $this->db = Database::getInstance();
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string
    {
        $row = $this->db->buscarUm(
            'SELECT payload FROM sessoes WHERE id = ?',
            [$id]
        );

        return is_array($row) ? (string) $row['payload'] : '';
    }

    public function write(string $id, string $data): bool
    {
        $this->db->executar(
            'INSERT INTO sessoes (id, payload, ultimo_acesso)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE payload = VALUES(payload), ultimo_acesso = VALUES(ultimo_acesso)',
            [$id, $data, time()]
        );

        return true;
    }

    public function destroy(string $id): bool
    {
        $this->db->executar('DELETE FROM sessoes WHERE id = ?', [$id]);
        return true;
    }

    public function gc(int $max_lifetime): int|false
    {
        $limite = time() - $max_lifetime;
        return $this->db
            ->executar('DELETE FROM sessoes WHERE ultimo_acesso < ?', [$limite])
            ->rowCount();
    }
}
