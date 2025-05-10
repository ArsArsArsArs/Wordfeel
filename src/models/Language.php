<?php
    namespace App;

    use PDO;

    class Language {
        public function __construct(public string $languageCode, public string $languageName, public string $countryCode) {}
    }

    class LanguageRepository {
        public function __construct(private PDO $pdo) {}

        public function getAllLanguages(): ?array {
            $stmt = $this->pdo->query("SELECT * FROM Languages ORDER BY LanguageName ASC");
            if (!$stmt) {
                return null;
            }

            $langs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $langsFromClass = [];
            foreach ($langs as $lang) {
                array_push($langsFromClass, new Language($lang['LanguageCode'], $lang['LanguageName'], $lang['CountryCode']));
            }
            return $langsFromClass;
        }

        public function getAllLanguagesByUserId(int $userID): ?array {
            $stmt = $this->pdo->prepare("SELECT DISTINCT l.* FROM Languages l JOIN UserWords uw ON l.LanguageCode = uw.LanguageCode WHERE uw.UserID = :userID ORDER BY l.LanguageName ASC");
            $ok = $stmt->execute([
                'userID' => $userID
            ]);
            if (!$ok) {
                return null;
            }

            $langs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $langsFromClass = [];
            foreach ($langs as $lang) {
                array_push($langsFromClass, new Language($lang['LanguageCode'], $lang['LanguageName'], $lang['CountryCode']));
            }
            return $langsFromClass;
        }

        public function getLanguageByCode(string $languageCode): ?Language {
            $stmt = $this->pdo->prepare("SELECT * FROM Languages WHERE LanguageCode = :languageCode LIMIT 1");
            $ok = $stmt->execute([
                'languageCode' => $languageCode,
            ]);
            if (!$ok) {
                return null;
            }

            $language = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$language) {
                return null;
            }
            return new Language($language['LanguageCode'], $language['LanguageName'], $language['CountryCode']);
        }
    }
?>