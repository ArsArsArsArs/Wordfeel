<?php
    namespace App;

    require_once __DIR__ . '/../functions.php';

    use PDO;
    use PDOException;
    use RuntimeException;

    class User {
        public function __construct(public int $id, public string $username, public string $at) {}
    }

    class UserRepository {
        public function __construct(private PDO $pdo) {}

        public function createUser(string $username, string $plainPassword): ?User {
            $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

            try {
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
            } catch(PDOException $e) {
                throw $e;
            }
        }

        public function loginUser(string $username, string $plainPassword): ?User {
            try {
                $stmt = $this->pdo->prepare("SELECT UserID, Password FROM Users WHERE Username = :Username");
                $stmt->execute([
                    'Username' => $username,
                ]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user || !password_verify($plainPassword, $user['Password'])) {
                    throw new RuntimeException("Invalid username or password");
                }

                $stmt = $this->pdo->prepare("SELECT Token FROM Tokens WHERE UserID = :UserID");
                $stmt->execute([
                    'UserID' => (int)$user['UserID'],
                ]);
                $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$tokenData) {
                    throw new RuntimeException("No access token found for user {$user['UserID']}");
                }

                return new User((int)$user['UserID'], $username, $tokenData['Token']);
            } catch(PDOException $e) {
                throw $e;
            }
        }

        public function getUserByToken(string $at): ?User {
            $stmt = $this->pdo->prepare("SELECT * FROM Users u JOIN Tokens t ON u.UserID = t.UserID WHERE t.Token = :Token");
            $ok = $stmt->execute([
                'Token' => $at
            ]);
            if (!$ok) {
                return null;
            }

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return new User((int)$user['UserID'], $user['Username'], $at);
        }

        public function getUserByID(int $userIDtoGet, int $userIDexecuting): ?User {
            $stmt = $this->pdo->prepare("SELECT * FROM Users WHERE UserID = :UserID LIMIT 1");
            $ok = $stmt->execute([
                'UserID' => $userIDtoGet
            ]);
            if (!$ok) {
                return null;
            }

            $userToGet = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$userToGet) {
                return null;
            }

            $userObject = new User((int)$userToGet['UserID'], $userToGet['Username'], '');
            if ($userObject->id == $userIDexecuting) {
                return $userObject;
            }

            $stmt = $this->pdo->prepare("SELECT * FROM UserManaging WHERE UserID = :UserIDexecuting AND ManagedUserID = :UserIDtoGet");
            $ok = $stmt->execute([
                'UserIDexecuting' => $userIDexecuting,
                'UserIDtoGet' => $userObject->id
            ]);
            if (!$ok) {
                return null;
            }

            $userManagingEntry = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$userManagingEntry) {
                return null;
            }         

            return $userObject;
        }

        public function getUserByUsername(string $usernameToGet, int $userIDexecuting): ?User {
            $stmt = $this->pdo->prepare("SELECT * FROM Users WHERE Username = :Username LIMIT 1");
            $ok = $stmt->execute([
                'Username' => $usernameToGet
            ]);
            if (!$ok) {
                return null;
            }

            $userToGet = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$userToGet) {
                return null;
            }

            $userObject = new User((int)$userToGet['UserID'], $userToGet['Username'], '');
            if ($userObject->id == $userIDexecuting) {
                return $userObject;
            }

            $stmt = $this->pdo->prepare("SELECT * FROM UserManaging WHERE UserID = :UserIDexecuting AND ManagedUserID = :UserIDtoGet");
            $ok = $stmt->execute([
                'UserIDexecuting' => $userIDexecuting,
                'UserIDtoGet' => $userObject->id
            ]);
            if (!$ok) {
                return null;
            }

            $userManagingEntry = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$userManagingEntry) {
                return null;
            }         

            return $userObject;
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