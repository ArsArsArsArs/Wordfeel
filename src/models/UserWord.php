<?php
    namespace App;

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
            $stmt = $this->pdo->prepare("SELECT * FROM UserWords WHERE UserID = :UserID AND LanguageCode = :LanguageCode ORDER BY CreatedAt DESC");
            $ok = $stmt->execute([
                'UserID' => $userID,
                'LanguageCode' => $languageCode
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

        public function getTaggedWordsByUserIDandLanguageCode(int $userID, string $languageCode, int $tagID): ?array {
            $stmt = $this->pdo->prepare("SELECT * FROM UserWords uw JOIN WordTags wt ON uw.WordID = wt.WordID WHERE uw.UserID = :UserID AND uw.LanguageCode = :LanguageCode AND wt.TagID = :TagID ORDER BY uw.CreatedAt DESC");
            $ok = $stmt->execute([
                'UserID' => $userID,
                'LanguageCode' => $languageCode,
                'TagID' => $tagID
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

        public function deleteWord(int $userID, int $wordID) {
            $stmt = $this->pdo->prepare("DELETE FROM UserWords WHERE UserID = :UserID AND WordID = :WordID");

            try {
                $wordDeleted = $stmt->execute([
                    'UserID' => $userID,
                    'WordID' => $wordID
                ]);
                if (!$wordDeleted) {
                    throw new RuntimeException("Failed to delete a word");
                }
            } catch(PDOException $e) {
                throw $e;
            }
        }

        public function getWord(int $userID, int $wordID): ?UserWord {
            $stmt = $this->pdo->prepare("SELECT * FROM UserWords WHERE UserID = :UserID AND WordID = :WordID");
            $ok = $stmt->execute([
                'UserID' => $userID,
                'WordID' => $wordID
            ]);
            if (!$ok) {
                return null;
            }

            $word = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$word) {
                return null;
            }

            return new UserWord((int)$word['WordID'], (int)$word['UserID'], $word['LanguageCode'], $word['Word'], $word['Translation'], $word['Transcription'], $word['Description'], $word['ImageURL'], $word['LastReviewed'], (int)$word['MemorizationPercent'], $word['CreatedAt']);
        }

        public function searchWords(int $userID, string $request): ?array {
            $stmt = $this->pdo->prepare("SELECT * FROM UserWords WHERE UserID = :UserID AND Word LIKE :Request");
            $ok = $stmt->execute([
                'UserID' => $userID,
                'Request' => $request . "%"
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
                array_push($wordsFromClass, new UserWord((int)$word['WordID'], (int)$word['UserID'], $word['LanguageCode'], $word['Word'], $word['Translation'], $word['Transcription'], $word['Description'], $word['ImageURL'], $word['LastReviewed'], (int)$word['MemorizationPercent'], $word['CreatedAt']));
            }
            return $wordsFromClass;
        }

        public function updatePercent(int $userID, int $wordID, int $memorizationPercent) {
            $stmt = $this->pdo->prepare("UPDATE UserWords SET MemorizationPercent = :MemorizationPercent, LastReviewed = :LastReviewed WHERE UserID = :UserID AND WordID = :WordID");
            $currentDate = gmdate('Y-m-d H:i:s');

            try {
                $procentUpdated = $stmt->execute([
                    'MemorizationPercent' => $memorizationPercent,
                    'LastReviewed' => $currentDate,
                    'UserID' => $userID,
                    'WordID' => $wordID
                ]);
                if (!$procentUpdated) {
                    throw new RuntimeException("Failed to update percent");
                }
            } catch(PDOException $e) {
                throw $e;
            }
        }
    }
?>