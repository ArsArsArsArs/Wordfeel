<?php
    require __DIR__ . '/../../../src/functions.php';
    require_once __DIR__ . '/../../../src/Database.php';
    require_once __DIR__ . '/../../../src/models/User.php';
    require_once __DIR__ . '/../../../src/models/Language.php';
    require_once __DIR__ . '/../../../src/models/UserWord.php';

    use function App\capitalizeFirstLetter;
    use function App\redirect;
    use function App\customGetEnv;
    use App\Database;
    use App\UserRepository;
    use App\LanguageRepository;
    use App\UserWordRepository;

    $env = parse_ini_file(__DIR__ . '/../../../.env');

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
    if (isset($_GET['for']) && is_numeric($_GET['for'])) {
        $user = $userR->getUserByID((int)$_GET['for'], $user->id);
        if (!$user) {
            redirect("/");
        }
    }

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

    if (!isset($_GET['id'])) {
        redirect("/personal");
    }
    $word = $userWordR->getWord($user->id, $_GET['id']);
    if (!$word) {
        redirect("/personal");
    }

    $title = capitalizeFirstLetter($word->word);
    $pageName = "personal/word"
?>
<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../../templates/header.php'; ?>
    <main>
        <?php if (isset($_GET['for']) && is_numeric($_GET['for'])): ?>
            <section class="caution">
                <p>Вы смотрите словарь пользователя <i><?= $user->username ?></i></p>
            </section>
        <?php endif ?>
        <article>
            <p><i>Язык: <?= $language->languageName ?></i></p>
            <section class="word-and-translation">
                <div>
                    <h1>Слово:</h1>
                    <p><?= capitalizeFirstLetter($word->word) ?></p>
                </div>
                <div>
                    <h1>Перевод:</h1>
                    <p><?= capitalizeFirstLetter($word->translation) ?></p>
                </div>
            </section>
            <hr>
            <section class="description-and-transcription">
                <?php if (!empty($word->description)): ?>
                    <div>
                        <h1>Описание:</h1>
                        <p><?= $word->description ?></p>
                    </div>
                <?php endif; ?>
                <?php if (!empty($word->transcription)): ?>
                    <div>
                        <h1>Транскрипция:</h1>
                        <p class="transcription"><?= $word->transcription ?></p>
                    </div>
                <?php endif; ?>
            </section>
            <section class="miscellaneous">
                <ul>
                    <li><b>Процент запоминания:</b> <?php $memorizationPercent = isset($word->memorizationPercent) ? $word->memorizationPercent : 0; echo $memorizationPercent; ?></li>
                    <li><b>Время добавления (UTC):</b> <?= $word->createdAt ?></li>
                    <li><b>Повторено последний раз (UTC):</b> <?php $lastReviewed = isset($word->lastReviewed) ? $word->lastReviewed : 'никогда'; echo $lastReviewed; ?></li>
                </ul>
            </section>
        </article>
    </main>
</body>
</html>