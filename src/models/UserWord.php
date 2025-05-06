<?php
    namespace App;

    use DateTime;
    use PDO;

    class UserWord {
        public function __construct(public int $wordID, public int $userID, public string $languageCode, public string $word, public string $translation, public string $transcription, public string $description, private string $imageURL, public DateTime $lastReviewed, public int $memorizationPercent, public DateTime $createdAt) {}
    }

    class UserWordRepository {
        public function __construct(private PDO $pdo) {}

        public function getAllWordsByUserIDandLanguageCode(int $userID, string $languageCode): ?array {
            $stmt = $this->pdo->prepare("SELECT * FROM UserWords WHERE UserID = :userID AND LanguageCode = :languageCode ORDER BY CreatedAt DESC");
            $ok = $stmt->execute([
                'userID' => $userID,
                'languageCode' => $languageCode
            ]);

            if (!$ok) {
                return null;
            }

            $words = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($words) == 0) {
                return null;
            }

            $wordsFromClass = [];
            foreach ($words as $word) {
                array_push($wordsFromClass, new UserWord((int)$word['WordID'], (int)$word['UserID'], $word['LanguageCode'], $word['Word'], $word['Translation'], $word['Transcription'], $word['Description'], $word['ImageURL'], $word['LastReviewed'], $word['MemorizationPercent'], $word['CreatedAt']));
            }
            return $wordsFromClass;
        }
    }
?>