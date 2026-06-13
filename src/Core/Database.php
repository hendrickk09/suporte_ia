<?php
class Database
{
    private static ?Database $instancia = null;
    private PDO $conexao;

    private function __construct()
    {
        $dsn = "mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME.";charset=".DB_CHARSET;
        try {
            $this->conexao = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // Evita gravar DSN, SQL ou credenciais completas nos logs.
            error_log('[Database] Falha de conexao. Codigo: ' . $e->getCode());
            die('Falha na conexão com o banco de dados.');
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instancia === null) self::$instancia = new self();
        return self::$instancia;
    }

    public function executar(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->conexao->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function buscarTodos(string $sql, array $params = []): array
    {
        return $this->executar($sql, $params)->fetchAll();
    }

    public function buscarUm(string $sql, array $params = []): array|false
    {
        return $this->executar($sql, $params)->fetch();
    }

    public function ultimoId(): string { return $this->conexao->lastInsertId(); }

    private function __clone() {}
    public function __wakeup(): void { throw new \Exception("Singleton não pode ser deserializado."); }
}
