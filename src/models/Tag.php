<?php
    namespace App;

    use PDO;
use PDOException;
use RuntimeException;

    class Tag {
        public function __construct(public int $tagID, public int $userID, public string $languageCode, public string $tagName) {}
    }

    class TagRepository {
        public function __construct(private PDO $pdo) {}

        public function getAllTagsByUserIDandLanguageCode(int $userID, string $languageCode): ?array {
            $stmt = $this->pdo->prepare("SELECT * FROM Tags WHERE UserID = :userID AND LanguageCode = :languageCode");
            $ok = $stmt->execute([
                "userID" => $userID,
                "languageCode" => $languageCode,
            ]);
            if (!$ok) {
                return null;
            } 

            $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($tags) === 0) {
                return null;
            }

            $tagsFromClass = [];
            foreach ($tags as $tag) {
                array_push($tagsFromClass, new Tag((int)$tag['TagID'], (int)$tag['UserID'], $tag['LanguageCode'], $tag['TagName']));
            }
            return $tagsFromClass;
        }

        public function createTag(int $userID, string $languageCode, string $tagName) {
            $stmt = $this->pdo->prepare("INSERT INTO Tags (UserID, LanguageCode, TagName) VALUES (:UserID, :LanguageCode, :TagName)");

            try {
                $tagCreated = $stmt->execute([
                    'UserID' => $userID,
                    'LanguageCode' => $languageCode,
                    'TagName' => $tagName
                ]);
                if (!$tagCreated) {
                    throw new RuntimeException("Failed to create a tag");
                }
            } catch (PDOException $e) {
                throw $e;
            }
        }

        public function deleteTag(int $userID, string $languageCode, string $tagName) {
            $stmt = $this->pdo->prepare("DELETE FROM Tags WHERE UserID = :UserID AND LanguageCode = :LanguageCode AND TagName = :TagName");

            try {
                $tagDeleted = $stmt->execute([
                    'UserID' => $userID,
                    'LanguageCode' => $languageCode,
                    'TagName' => $tagName
                ]);
                if (!$tagDeleted) {
                    throw new RuntimeException("Failed to delete a tag");
                }
            } catch (PDOException $e) {
                throw $e;
            }
        }

        public function assignTags(int $userID, string $languageCode, int $wordID, ?array $tagNames) {
            if (!$tagNames) {
                return;
            }
            foreach ($tagNames as $tagName) {
                $tag = $this->getTag($userID, $languageCode, $tagName);
                if (!$tag) {
                    continue;
                }

                $stmt = $this->pdo->prepare("INSERT INTO WordTags (WordID, TagID) VALUES (:WordID, :TagID)");
                try {
                    $stmt->execute([
                        'WordID' => $wordID,
                        'TagID' => $tag->tagID
                    ]);
                } catch(PDOException $e) {
                    error_log("Failed to create word-tag pair: {$e->getMessage()}");
                }
            }
        }

        private function getTag(int $userID, string $languageCode, string $tagName): ?Tag {
            $stmt = $this->pdo->prepare("SELECT * FROM Tags WHERE UserID = :UserID AND LanguageCode = :LanguageCode AND TagName = :TagName");
            $ok = $stmt->execute([
                'UserID' => $userID,
                'LanguageCode' => $languageCode,
                'TagName' => $tagName
            ]);
            if (!$ok) {
                return null;
            }

            $tag = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$tag) {
                return null;
            }
            return new Tag((int)$tag['TagID'], (int)$tag['UserID'], $tag['LanguageCode'], $tag['TagName']);
        }
    }
?>