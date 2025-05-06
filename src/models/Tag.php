<?php
    namespace App;

    use PDO;

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
                array_push($tagsFromClass, new Tag((int)$tags['TagID'], (int)$tags['UserID'], $tags['LanguageCode'], $tags['TagName']));
            }
            return $tagsFromClass;
        }
    }
?>