<?php
    namespace App;

    use PDO;
    use PDOException;

    class Database {
        private PDO $pdo;

        public function __construct(
            private string $host,
            private string $dbname,
            private string $charset,
            private string $username,
            private string $password
        ){
            $this->connect();
        }

        private function connect(): void {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            try {
                $this->pdo = new PDO($dsn, $this->username, $this->password);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                error_log($e);
                exit();
            }
        }

        public function getConnection(): PDO {
            return $this->pdo;
        }
    }
?>