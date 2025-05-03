<?php
    namespace App;

    require_once __DIR__ . '/../functions.php';

    use PDO;
use RuntimeException;

    class UserRepository {
        public function __construct(private PDO $pdo) {}

        public function createUser(string $username, string $plainPassword): ?User {
            $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

            $stmt = $this->pdo->prepare("INSERT INTO Users (Username, Password) VALUES (:Username, :Password)");

            $userCreated = $stmt->execute([
                'Username' => $username,
                'Password' => $hashedPassword
            ]);
            if (!$userCreated) {
                throw new RuntimeException("Failed to create a user");
            }

            $userId = (int)$this->pdo->lastInsertId();
            $at = $this->obtainAccessToken($userId);

            return new User($userId, $username, $at);
        }

        private function obtainAccessToken(int $userID): string {
            do {
                $generatedToken = bin2hex(random_bytes(32));
            } while (!$this->isTokenUnique($generatedToken));

            $stmt = $this->pdo->prepare("INSERT INTO Tokens (Token, UserID) VALUES (:Token, :UserID)");
            $tokenCreated = $stmt->execute([
                "Token" => $generatedToken,
                "UserID" => $userID
            ]);
            if (!$tokenCreated) {
                throw new RuntimeException("Failed to assert a token");
            }

            return $generatedToken;
        }

        private function isTokenUnique(string $token): bool {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Tokens WHERE Token = :Token");
            $stmt->execute([
                'Token' => $token,
            ]);

            return (int)$stmt->fetchColumn() === 0;
        }
    }
?>