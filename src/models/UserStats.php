<?php
    namespace App;

    use PDO;
    use PDOException;

    class UserStats {
        public function __construct(public int $userID, public string $languageCode, public int $wordsDone, public int $percentGained, public string $date) {}
    }

    class UserStatsRepository {
        public function __construct(private PDO $pdo) {}

        public function getStats(int $userID, string $languageCode, string $date): ?UserStats {
            $stmt = $this->pdo->prepare("SELECT * FROM UserStats WHERE UserID = :UserID AND Date = :Date AND LanguageCode = :LanguageCode LIMIT 1");
            $ok = $stmt->execute([
                'UserID' => $userID,
                'Date' => $date,
                'LanguageCode' => $languageCode
            ]);
            if (!$ok) {
                return null;
            }

            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$stats) {
                return null;
            }
            return new UserStats((int)$stats['UserID'],  $stats['LanguageCode'], (int)$stats['WordsDone'], (int)$stats['PercentGained'], $stats['Date']);
        }

        public function getRangeOfStats(int $userID, string $languageCode, string $startDate, string $endDate): ?array {
            $stmt = $this->pdo->prepare("SELECT * FROM UserStats WHERE UserID = :UserID AND LanguageCode = :LanguageCode AND Date BETWEEN :StartDate AND :EndDate");
            $ok = $stmt->execute([
                'UserID' => $userID,
                'LanguageCode' => $languageCode,
                'StartDate' => $startDate,
                'EndDate' => $endDate
            ]);
            if (!$ok) {
                return null;
            }

            $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($stats) == 0) {
                return null;
            }
            $statsFromClass = [];
            foreach ($stats as $oneStats) {
                array_push($statsFromClass, new UserStats((int)$oneStats['UserID'], $oneStats['LanguageCode'], (int)$oneStats['WordsDone'], (int)$oneStats['PercentGained'], $oneStats['Date']));
            }
            return $statsFromClass;
        }

        public function updateStats(int $userID, string $languageCode, int $percentGained) {
            $currentDate = gmdate('Y-m-d');
            $stmt = $this->pdo->prepare("SELECT * FROM UserStats WHERE UserID = :UserID AND Date = :Date AND LanguageCode = :LanguageCode LIMIT 1");
            
            $ok = $stmt->execute([
                'UserID' => $userID,
                'Date' => $currentDate,
                'LanguageCode' => $languageCode
            ]);
            if (!$ok) {
                $this->insertStats($userID, $languageCode, $percentGained, $currentDate);
                return;
            }

            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$stats) {
                $this->insertStats($userID, $languageCode, $percentGained, $currentDate);
                return;
            }

            $statsFromClass = new UserStats((int)$stats['UserID'], $stats['LanguageCode'], (int)$stats['WordsDone'], (int)$stats['PercentGained'], (int)$stats['Date']);

            $stmt = $this->pdo->prepare("UPDATE UserStats SET WordsDone = :WordsDone, PercentGained = :PercentGained WHERE UserID = :UserID AND Date = :Date AND LanguageCode = :LanguageCode");
            try {
                $stmt->execute([
                    'WordsDone' => $statsFromClass->wordsDone+1,
                    'PercentGained' => $statsFromClass->percentGained+$percentGained,
                    'UserID' => $statsFromClass->userID,
                    'Date' => $currentDate,
                    'LanguageCode' => $languageCode
                ]);
            } catch(PDOException $e) {
                error_log("Failed to update stats: {$e->getMessage()}");
            }
        }

        private function insertStats(int $userID, string $languageCode, int $percentGained, string $currentDate) {
            $stmt = $this->pdo->prepare("INSERT INTO UserStats(UserID, WordsDone, PercentGained, Date, LanguageCode) VALUES(:UserID, :WordsDone, :PercentGained, :Date, :LanguageCode)");
            try {
                $stmt->execute([
                    'UserID' => $userID,
                    'WordsDone' => 1,
                    'PercentGained' => $percentGained,
                    'Date' => $currentDate,
                    'LanguageCode' => $languageCode
                ]);
            } catch(PDOException $e) {
                error_log("Failed to insert stats: {$e->getMessage()}");
            }
        }
    }
?>