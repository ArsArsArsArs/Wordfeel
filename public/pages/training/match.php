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

    $fourWordsIndexes = array_rand($userWords, 4);
    $fourWords = [];
    for ($i = 0; $i < 4; $i++) {
        array_push($fourWords, $userWords[$fourWordsIndexes[$i]]);
    }
    $oneRightWord = $fourWords[array_rand($fourWords, 1)];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $answer = isset($_POST['answer']) ? $_POST['answer'] : '';
        if (!$answer) {
            redirect("/");
        }

        $answer = htmlspecialchars($answer);
        if (!is_numeric($answer)) {
            redirect("/");
        }

        $answer = $userWordR->getWord($user->id, (int)$answer);
        $word = $userWordR->getWord($user->id, (int)$_SESSION['match_word_id']);

        if ($answer->translation == $_SESSION['match_answer']) {
            try {
                $userWordR->updatePercent($user->id, $word->wordID, $word->memorizationPercent+4);
                $userStatsR->updateStats($user->id, $language->languageCode, 4);
            } catch(Exception $e) {
                error_log("Failed to update percent: {$e->getMessage()}");
            }
            $capitalizedWord = capitalizeFirstLetter($word->word);
            $matchRight = "Верно! <b>{$capitalizedWord}</b> переводится как <b>{$answer->translation}</b>";
        } else {
            try {
                $userWordR->updatePercent($user->id, $word->wordID, $word->memorizationPercent-4);
                $userStatsR->updateStats($user->id, $language->languageCode, -4);
            } catch(Exception $e) {
                error_log("Failed to update percent: {$e->getMessage()}");
            }
            $capitalizedWord = capitalizeFirstLetter($word->word);
            $matchMistake = "Ой! <b>{$capitalizedWord}</b> НЕ переводится как {$answer->translation}. Правильный ответ - <b>{$_SESSION['match_answer']}</b>";
        }
    }

    $_SESSION['match_word_id'] = $oneRightWord->wordID;
    $_SESSION['match_answer'] = $oneRightWord->translation;

    $title = "Поиск перевода";
    $pageName = "training/match";
?>
<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../../templates/header.php'; ?> 
    
    <main>
        <section class="match-title">
            <h1>Поиск перевода</h1>
            <a href="/personal?langdict=<?= $language->languageCode ?>" class="a-button">Выйти</a>
        </section>
        <?php if (!empty($matchRight)): ?>
            <section class="success-window no-margin">
                <p><?= getSvgIcon('thumb-up') . $matchRight ?></p>
            </section>
        <?php endif; ?>
        <?php if(!empty($matchMistake)): ?>
            <section class="error-window no-margin">
                <p><?= getSvgIcon('mood-sad') . $matchMistake ?></p>
            </section>
        <?php endif; ?>
        <section class="match-training">
            <div class="word-question">
                <p class="word"><?= $oneRightWord->word ?></p>
                <?php if (!empty($oneRightWord->transcription)): ?>
                    <p class="transcription"><?= $oneRightWord->transcription ?></p>
                <?php endif; ?>
            </div>
            <form action="/training/match" method="POST">
                <?php foreach($fourWords as $word): ?>
                    <div class="answer">
                        <input type="radio" id="answer<?= $word->wordID ?>" name="answer" value="<?= $word->wordID ?>" required>
                        <label for="answer<?= $word->wordID ?>"><?= capitalizeFirstLetter($word->translation) ?></label>
                    </div>
                <?php endforeach; ?>
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