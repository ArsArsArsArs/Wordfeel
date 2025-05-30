<?php
    require_once __DIR__ . '/../../../src/functions.php';
    require_once __DIR__ . '/../../../src/Database.php';
    require_once __DIR__ . '/../../../src/models/User.php';
    require_once __DIR__ . '/../../../src/models/Language.php';
    require_once __DIR__ . '/../../../src/models/WordPackages.php';
    require_once __DIR__ . '/../../../src/models/UserWord.php';
    require_once __DIR__ . '/../../../src/models/Tag.php';

    use function App\customGetEnv;
    use function App\redirect;
    use App\Database;
    use App\UserRepository;
    use App\LanguageRepository;
    use App\WordPackageRepository;
    use App\UserWordRepository;
    use App\TagRepository;

    $env = parse_ini_file(__DIR__ . '/../../../.env');

    if (!isset($_COOKIE['at'])) {
        redirect('/auth');
    }
    $token = $_COOKIE['at'];

    $db = new Database(customGetEnv("DB_HOST", $env), customGetEnv("DB_NAME", $env), customGetEnv("DB_CHARSET", $env), customGetEnv("DB_USERNAME", $env), customGetEnv("DB_PASSWORD", $env));
    $userR = new UserRepository($db->getConnection());
    $languageR = new LanguageRepository($db->getConnection());
    $wordPackageR = new WordPackageRepository($db->getConnection());
    $userWordR = new UserWordRepository($db->getConnection());
    $tagR = new TagRepository($db->getConnection());

    $user = $userR->getUserByToken($token);
    if (!$user) {
        redirect("/");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if (empty($id)) {
            redirect("/");
        }
        
        $wordPackage = $wordPackageR->getPackage((int)$id);

        $packageWords = $wordPackageR->getPackageWords((int)$id);
        if (!$packageWords) {
            redirect("/");
        }

        $langCode = $packageWords[0]['LanguageCode'];

        $tagCreated = false;
        $i = 1;
        do {
            $name = $wordPackage->name;
            if ($i !== 1) {
                $name .= " ({$i})";
            }
            try {
                $tagR->createTag($user->id, $langCode, "{$name}");
            } catch(Exception $e) {
                $i++;
                continue;
            }
            $tagCreated = true;
        } while (!$tagCreated);

        $wordsAdded = 0;
        foreach ($packageWords as $packageWord) {
            try {
                $wordID = $userWordR->addWord($user->id, $langCode, $packageWord['Word'], $packageWord['Translation'], $packageWord['Transcription'], $packageWord['Description']);
                $tagR->assignTags($user->id, $langCode, $wordID, [$name]);
                $wordsAdded++;
            } catch (Exception $e) {
                continue;
            }
        }

        if ($wordsAdded === 0) {
            $tagR->deleteTag($user->id, $langCode, $name);
        }

        redirect("/personal?langdict={$langCode}");
    }

    if (!isset($_GET['id'])) {
        redirect('/words-package');
    }

    $wordPackage = $wordPackageR->getPackage($_GET['id']);
    if (!$wordPackage) {
        redirect('/words-package');
    }

    $packageWords = $wordPackageR->getPackageWords($_GET['id']);
    if (!$packageWords || count($packageWords) === 0) {
        redirect('/words-package');
    }

    $langCode = $packageWords[0]['LanguageCode'];
    $language = $languageR->getLanguageByCode($langCode);

    $title = "Добавить набор слов | {$wordPackage->name}";
    $pageName = "words-package/add";

    $metaTitle = $wordPackage->name;
    $metaDescription = "Добавьте этот набор слов в Wordfeel, чтобы тренироваться и запомнить всё!";
?>
<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../../templates/header.php'; ?>
    
    <main>
        <section class="no-margin">
            <article class="offer">
                <hgroup>
                    <h1>Добавить набор слов</h1>
                    <p>После подтверждения слова ниже появятся в Вашем словаре</p>
                </hgroup>
                <h2><?= $wordPackage->name ?></h2>
                <form action="/words-package/add" method="POST">
                    <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
                    <input type="submit" value="Добавить">
                </form>
                <br>
                <h3>Список слов (<?= $language->languageName ?>)</h3>
                <div class="words-list">
                    <?php foreach($packageWords as $packageWord): ?>
                        <p><?= $packageWord['Word'] ?></p>
                    <?php endforeach; ?>
                </div>
            </article>
        </section>
        <section>
            <h1>О Wordfeel</h1>
            <p>Сервис позволяет любому желающему создать свой словарик по мере изучения языков, чтобы надёжно хранить выученные слова и иметь возможность потренировать их с помощью встроенных режимов в любое время. Удобная статистика успеваемости поможет укрепить свою дисциплину.</p>
        </section>
    </main>
</body>
</html>