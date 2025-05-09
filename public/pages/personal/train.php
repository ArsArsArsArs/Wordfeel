<?php
    require __DIR__ . '/../../../src/functions.php';
    require_once __DIR__ . '/../../../src/Database.php';
    require_once __DIR__ . '/../../../src/models/User.php';
    require_once __DIR__ . '/../../../src/models/Language.php';
    require_once __DIR__ . '/../../../src/models/Tag.php';
    require_once __DIR__ . '/../../../src/models/UserWord.php';

    use function App\redirect;
    use function App\customGetEnv;
    use App\Database;
    use App\UserRepository;
    use App\LanguageRepository;
    use App\TagRepository;
    use App\UserWordRepository;

    $env = parse_ini_file(__DIR__ . '/../../../.env');

    $token = $_COOKIE['at'];
    if (!isset($token)) {
        redirect("/auth");
    }

    $db = new Database(customGetEnv("DB_HOST", $env), customGetEnv("DB_NAME", $env), customGetEnv("DB_CHARSET", $env), customGetEnv("DB_USERNAME", $env), customGetEnv("DB_PASSWORD", $env));
    $userR = new UserRepository($db->getConnection());
    $languageR = new LanguageRepository($db->getConnection());
    $tagR = new TagRepository($db->getConnection());
    $userWordR = new UserWordRepository($db->getConnection());

    $user = $userR->getUserByToken($token);
    if (!$user) {
        redirect("/");
    }
    if (!isset($_GET['langdict'])) {
        redirect("/personal");
    }
    $language = $languageR->getLanguageByCode($_GET['langdict']);
    if (!$language) {
        redirect("/personal");
    }
    $allUserLangTags = $tagR->getAllTagsByUserIDandLanguageCode($user->id, $language->languageCode);
    
    if (isset($_GET['tag']) && is_numeric($_GET['tag'])) {
        $userWords = $userWordR->getTaggedWordsByUserIDandLanguageCode($user->id, $language->languageCode, (int)$_GET['tag']);
    } else {
        $userWords = $userWordR->getAllWordsByUserIDandLanguageCode($user->id, $language->languageCode);
    }

    $title = "Тренировка";
    $pageName = "personal/train";
?>
<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../../templates/header.php'; ?>
    
    <main>
        <section class="train-title no-margin">
            <h1>Тренировка</h1>
            <a href="/personal?langdict=<?= $_GET['langdict'] ?>" class="a-button">Назад</a>
        </section>
        <section class="train-tag no-margin">
            <label for="tagselect">Тег</label>
            <select id="tagselect">
                <option value=""></option>
                <?php foreach($allUserLangTags as $tag): ?>
                    <option value="<?= $tag->tagID ?>" <?php if ($_GET['tag'] == $tag->tagID): ?> selected <?php endif; ?>><?= $tag->tagName ?></option>
                <?php endforeach; ?>
            </select>
        </section>
        <section class="trainings-list">
            <?php if (!$userWords || count($userWords) < 4): ?>
                <p>В коллекции<?php if (!empty($_GET['tag'])) { echo ' (учитывая тег)'; } ?> должно быть как минимум 4 слова, чтобы тренироваться</p>
            <?php else: ?>
                <a href="/training/match?langdict=<?= $language->languageCode ?><?php if (isset($_GET['tag'])) { echo "&tag={$_GET['tag']}"; }?>" class="a-button">Поиск перевода</a>
                <a href="/training/matchTranslate?langdict=<?= $language->languageCode ?><?php if (isset($_GET['tag'])) { echo "&tag={$_GET['tag']}"; }?>" class="a-button">Поиск слова</a>
                <a href="/training/write?langdict=<?= $language->languageCode ?><?php if (isset($_GET['tag'])) { echo "&tag={$_GET['tag']}"; }?>" class="a-button">Написание слова</a>
            <?php endif; ?>
        </section>
    </main>

    <script src="/assets/scripts/pages/personal/train.js"></script>
</body>
</html>