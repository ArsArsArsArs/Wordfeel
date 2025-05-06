<?php
    namespace App;

    use DateTime;
    use PDO;
    use PDOException;
    use RuntimeException;

    class UserWord {
        public function __construct(public int $wordID, public int $userID, public string $languageCode, public string $word, public string $translation, public ?string $transcription, public ?string $description, private ?string $imageURL, public ?string $lastReviewed, public ?int $memorizationPercent, public string $createdAt) {}
    }

    class UserWordRepository {
        public int $lastWordAddedID;

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

        public function addWord(int $userID, string $languageCode, string $word, string $translation, string $transcription, string $description) {
            $stmt = $this->pdo->prepare("INSERT INTO UserWords (UserID, LanguageCode, Word, Translation, Transcription, Description) VALUES (:UserID, :LanguageCode, :Word, :Translation, :Transcription, :Description)");

            try {
                $wordAdded = $stmt->execute([
                    'UserID' => $userID,
                    'LanguageCode' => $languageCode,
                    'Word' => $word,
                    'Translation' => $translation,
                    'Transcription' => $transcription,
                    'Description' => $description
                ]);
                if (!$wordAdded) {
                    throw new RuntimeException("Failed to add a word");
                }
            } catch(PDOException $e) {
                throw $e;
            }

            $this->lastWordAddedID = (int)$this->pdo->lastInsertId();
        }
    }
?>