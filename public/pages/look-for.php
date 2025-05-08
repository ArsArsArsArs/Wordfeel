<?php
    require __DIR__ . '/../../src/functions.php';
    require_once __DIR__ . '/../../src/Database.php';
    require_once __DIR__ . '/../../src/models/User.php';
    require_once __DIR__ . '/../../src/models/Language.php';
    require_once __DIR__ . '/../../src/models/UserWord.php';

    use function App\redirect;
    use function App\customGetEnv;
    use App\Database;
    use App\UserRepository;
    use App\LanguageRepository;
    use App\UserWordRepository;

    $env = parse_ini_file(__DIR__ . '/../../.env');

    $token = $_COOKIE['at'];
    if (!isset($token)) {
        redirect("/auth");
    }

    $db = new Database(customGetEnv("DB_HOST", $env), customGetEnv("DB_NAME", $env), customGetEnv("DB_CHARSET", $env), customGetEnv("DB_USERNAME", $env), customGetEnv("DB_PASSWORD", $env));
    $userR = new UserRepository($db->getConnection());
    $languageR = new LanguageRepository($db->getConnection());
    $userWordR = new UserWordRepository($db->getConnection());

    $user = $userR->getUserByToken($token);
    if (!$user) {
        redirect("/");
    }
    
    $request = $_GET['word'];
    if (!isset($request)) {
        return("/");
    }

    $words = $userWordR->searchWords($user->id, $request);

    $title = "Поиск | {$request}";
    $pageName = "look-for";
?>

<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../templates/header.php'; ?>

    <main>
        <section class="no-margin">
            <div class="lookfor-title">
                <h1>Поиск</h1>
                <a href="/" class="a-button">Назад</a>
            </div>
            <?php if (!$words): ?>
                <p>По запросу <b><?= htmlspecialchars($_GET['word']) ?></b> ничего не найдено</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($words as $word): ?>
                        <?php $language = $languageR->getLanguageByCode($word->languageCode); ?>
                        <li><a href="/personal/word?langdict=<?= $word->languageCode ?>&id=<?= $word->wordID ?>" target="_blank"><?= $word->word ?></a> — <?= $word->translation ?> <i>(<?= $language->languageName ?>)</i></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif ?>
        </section>
    </main>
</body>

</html>