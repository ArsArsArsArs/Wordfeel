<?php
    namespace App;

    use PDO;
    use PDOException;
    use RuntimeException;

    class WordPackage {
        public function __construct(public int $wordPackageID, public string $name) {}
    }

    class WordPackageRepository {
        public function __construct(private PDO $pdo) {}

        public function createPackage(string $packageName, string $languageCode, array $words): int {
            $stmt = $this->pdo->prepare("INSERT INTO WordPackages (Name) VALUES (:Name)");

            try {
                $packageCreated = $stmt->execute([
                    'Name' => $packageName
                ]);
                if (!$packageCreated) {
                    throw new RuntimeException("Failed to create a package");
                }

                $lastPackageAddedID = (int)$this->pdo->lastInsertId();

                $stmt = $this->pdo->prepare("INSERT INTO PackageWords (LanguageCode, Word, Translation, Transcription, Description, WordPackageID) VALUES (:LanguageCode, :Word, :Translation, :Transcription, :Description, :WordPackageID)");

                foreach ($words as $word) {
                    $stmt->execute([
                        'LanguageCode' => $languageCode,
                        'Word' => $word['word'],
                        'Translation' => $word['translation'],
                        'Transcription' => $word['transcription'],
                        'Description' => $word['description'],
                        'WordPackageID' => $lastPackageAddedID    
                    ]);
                }
            } catch(PDOException $e) {
                throw $e;
            }

            return $lastPackageAddedID;
        }

        public function getPackage(int $wordPackageID): ?WordPackage {
            $stmt = $this->pdo->prepare("SELECT * FROM WordPackages WHERE WordPackageID = :WordPackageID LIMIT 1");

            $ok = $stmt->execute([
                'WordPackageID' => $wordPackageID
            ]);
            if (!$ok) {
                return null;
            }

            $wordPackage = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$wordPackage) {
                return null;
            }

            return new WordPackage((int)$wordPackage['WordPackageID'], $wordPackage['Name']);
        }

        public function getPackageWords(int $wordPackageID): ?array {
            $stmt = $this->pdo->prepare("SELECT * FROM PackageWords WHERE WordPackageID = :WordPackageID");

            $ok = $stmt->execute([
                'WordPackageID' => $wordPackageID
            ]);
            if (!$ok) {
                return null;
            }

            $packageWords = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$packageWords) {
                return null;
            }

            return $packageWords;
        }
    }
?>