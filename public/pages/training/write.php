<?php
    session_start();

    require __DIR__ . '/../../../src/functions.php';
    require_once __DIR__ . '/../../../src/Database.php';
    require_once __DIR__ . '/../../../src/models/User.php';
    require_once __DIR__ . '/../../../src/models/Language.php';
    require_once __DIR__ . '/../../../src/models/UserWord.php';
    require_once __DIR__ . '/../../../src/models/UserStats.php';

    use function App\capitalizeFirstLetter;
    use function App\redirect;
    use function App\customGetEnv;
    use function App\getSvgIcon;
    use App\Database;
    use App\UserRepository;
    use App\LanguageRepository;
    use App\UserWordRepository;
    use App\UserStatsRepository;

    $env = parse_ini_file(__DIR__ . '/../../../.env');

    $token = $_COOKIE['at'];
    if (!isset($token)) {
        redirect("/auth");
    }

    $db = new Database(customGetEnv("DB_HOST", $env), customGetEnv("DB_NAME", $env), customGetEnv("DB_CHARSET", $env), customGetEnv("DB_USERNAME", $env), customGetEnv("DB_PASSWORD", $env));
    $userR = new UserRepository($db->getConnection());
    $languageR = new LanguageRepository($db->getConnection());
    $userWordR = new UserWordRepository($db->getConnection());
    $userStatsR = new UserStatsRepository($db->getConnection());

    $user = $userR->getUserByToken($token);
    if (!$user) {
        redirect("/");
    }
    if (!isset($_GET['langdict']) && !isset($_POST['langdict'])) {
        redirect("/personal");
    }
    $langdict = isset($_GET['langdict']) ? $_GET['langdict'] : $_POST['langdict'];
    $language = $languageR->getLanguageByCode($langdict);
    if (!$language) {
        redirect("/personal");
    }

    if (!isset($_GET['tag']) && !isset($_POST['tag'])) {
        $userWords = $userWordR->getAllWordsByUserIDandLanguageCode($user->id, $language->languageCode);
    } else {
        $tag = isset($_GET['tag']) ? $_GET['tag'] : $_POST['tag'];
        if (is_numeric($tag)) {
            $userWords = $userWordR->getTaggedWordsByUserIDandLanguageCode($user->id, $language->languageCode, (int)$tag);
        } else {
            $userWords = $userWordR->getAllWordsByUserIDandLanguageCode($user->id, $language->languageCode);
        }
    }

    if (count($userWords) < 4) {
        redirect("/personal");
    }

    $oneRightWord = $userWords[array_rand($userWords, 1)];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $answer = isset($_POST['answer']) ? $_POST['answer'] : '';
        if (!$answer) {
            redirect("/");
        }

        $answer = htmlspecialchars($answer);

        $word = $userWordR->getWord($user->id, (int)$_SESSION['write_word_id']);

        if (mb_strtolower($_POST['answer']) == mb_strtolower($_SESSION['write_answer'])) {
            try {
                $userWordR->updatePercent($user->id, $word->wordID, $word->memorizationPercent+5);
                $userStatsR->updateStats($user->id, $language->languageCode, 5);
            } catch(Exception $e) {
                error_log("Failed to update percent: {$e->getMessage()}");
            }
            $capitalizedTranslation = capitalizeFirstLetter($word->translation);
            $writeRight = "Верно! <b>{$capitalizedTranslation}</b> переводится как <b>{$answer}</b>";
        } else {
            try {
                $userWordR->updatePercent($user->id, $word->wordID, $word->memorizationPercent-5);
                $userStatsR->updateStats($user->id, $language->languageCode, -5);
            } catch(Exception $e) {
                error_log("Failed to update percent: {$e->getMessage()}");
            }
            $capitalizedTranslation = capitalizeFirstLetter($word->translation);
            $writeMistake = "Ой! <b>{$capitalizedTranslation}</b> НЕ переводится как {$answer}. Правильный ответ - <b>{$_SESSION['write_answer']}</b>";
        }
    }

    $_SESSION['write_word_id'] = $oneRightWord->wordID;
    $_SESSION['write_answer'] = $oneRightWord->word;

    $title = "Написание слова";
    $pageName = "training/write";
?>
<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../../templates/header.php'; ?> 
    
    <main>
        <section class="write-title">
            <h1>Написание слова</h1>
            <a href="/personal?langdict=<?= $language->languageCode ?>" class="a-button">Выйти</a>
        </section>
        <?php if (!empty($writeRight)): ?>
            <section class="success-window no-margin">
                <p><?= getSvgIcon('thumb-up') . $writeRight ?></p>
            </section>
        <?php endif; ?>
        <?php if(!empty($writeMistake)): ?>
            <section class="error-window no-margin">
                <p><?= getSvgIcon('mood-sad') . $writeMistake ?></p>
            </section>
        <?php endif; ?>
        <section class="match-training">
            <div class="word-question">
                <p class="word"><?= $oneRightWord->translation ?></p>
            </div>
            <form action="/training/write" method="POST">
                <div class="answer">
                    <label for="textInputField">Введите это слово на изучаемом языке:</label>
                    <input type="text" name="answer" pattern=".*\S+.*" autocomplete="off" spellcheck="false" autocorrect="false" autocapitalize="false" autofocus required>
                </div>
                <input type="hidden" name="langdict" value="<?= $language->languageCode ?>">
                <?php if (is_numeric($tag)): ?>
                    <input type="hidden" name="tag" value="<?= $tag ?>">
                <?php endif; ?>
                <input type="submit" value="Ответить">
            </form>
        </section>
    </main>
</body>
</html>