<?php
    session_start();

    require_once __DIR__ . '/../../../src/functions.php';
    require_once __DIR__ . '/../../../src/Database.php';
    require_once __DIR__ . '/../../../src/models/User.php';
    require_once __DIR__ . '/../../../src/models/Language.php';
    require_once __DIR__ . '/../../../src/models/WordPackages.php';

    use function App\customGetEnv;
    use function App\redirect;
    use function App\getSvgIcon;
    use App\Database;
    use App\UserRepository;
    use App\LanguageRepository;
    use App\WordPackageRepository;

    $env = parse_ini_file(__DIR__ . '/../../../.env');

    if (!isset($_COOKIE['at'])) {
        redirect('/auth');
    }
    $token = $_COOKIE['at'];

    $db = new Database(customGetEnv("DB_HOST", $env), customGetEnv("DB_NAME", $env), customGetEnv("DB_CHARSET", $env), customGetEnv("DB_USERNAME", $env), customGetEnv("DB_PASSWORD", $env));
    $userR = new UserRepository($db->getConnection());
    $languageR = new LanguageRepository($db->getConnection());
    $wordPackageR = new WordPackageRepository($db->getConnection());

    $user = $userR->getUserByToken($token);
    if (!$user) {
        redirect("/");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $langdict = isset($_POST['langdict']) ? $_POST['langdict'] : '';
        if (empty($langdict)) {
            $_SESSION['wp_error'] = 'Язык должен быть указан';
            redirect('/words-package');
        }
        $lang = $languageR->getLanguageByCode($langdict);
        if (!$lang) {
            $_SESSION['wp_error'] = 'Язык должен быть указан корректно';
            redirect('/words-package');
        }

        $packageName = isset($_POST['package_name']) ? $_POST['package_name'] : '';
        if (empty($packageName)) {
            $_SESSION['wp_error'] = 'Название набора должно быть введено';
            redirect('/words-package');
        }
        $packageName = htmlspecialchars($packageName);
        if (mb_strlen($packageName) > 100) {
            $_SESSION['wp_error'] = 'Укоротите Ваше название набора, пожалуйста';
            redirect('/words-package');
        }

        $wordsForPackage = [];

        for ($i = 0; $i < count($_POST['word']); $i++) {
            $currentWordNumber = $i + 1;

            $word = isset($_POST['word'][$i]) ? $_POST['word'][$i] : '';
            if (empty($word)) {
                $_SESSION['wp_error'] = "Слово № {$currentWordNumber} должно быть введено";
                redirect('/words-package');
            }
            if (!preg_match('/^[\p{L}0-9]([\p{L}0-9 ])*[\p{L}0-9]$/u', $word)) {
                $_SESSION['wp_error'] = "Слово № {$currentWordNumber} должно состоять из букв и цифр";
                redirect('/words-package');
            }
            if (mb_strlen($word) > 100) {
                $_SESSION['wp_error'] = "Слово № {$currentWordNumber} не должно быть больше 100 символов";
                redirect('/words-package');
            }
            $word = mb_strtolower($word);

            $translation = isset($_POST['translation'][$i]) ? $_POST['translation'][$i] : '';
            if (empty($translation)) {
                $_SESSION['wp_error'] = "Перевод № {$currentWordNumber} должен быть введён";
                redirect('/words-package');
            }
            if (!preg_match('/^[\p{L}0-9]([\p{L}0-9 ])*[\p{L}0-9]$/u', $translation)) {
                $_SESSION['wp_error'] = "Перевод № {$currentWordNumber} должен состоять из букв и цифр";
                redirect('/words-package');
            }
            if (mb_strlen($translation) > 100) {
                $_SESSION['wp_error'] = "Перевод № {$currentWordNumber} не должен быть больше 100 символов";
                redirect('/words-package');
            }
            $translation = mb_strtolower($translation);

            $transcription = isset($_POST['transcription'][$i]) ? htmlspecialchars($_POST['transcription'][$i]) : '';

            $description = isset($_POST['description'][$i]) ? htmlspecialchars($_POST['description'][$i]) : '';

            $wordsForPackage[] = [
                'word' => $word,
                'translation' => $translation,
                'transcription' => $transcription,
                'description' => $description
            ];
        }

        if (count($wordsForPackage) === 0) {
            $_SESSION['wp_error'] = 'В наборе должно быть хотя бы одно слово';
            redirect('/words-package');
        }

        try {
            $wordPackageID = $wordPackageR->createPackage($packageName, $lang->languageCode, $wordsForPackage);
        } catch(Exception $e) {
            $_SESSION['wp_error'] = 'При работе с базой данных произошла ошибка. Пожалуйста, попробуйте ещё раз';
            error_log("Failed to create word package: {$e->getMessage()}");
            redirect('/words-package');
        }

        $_SESSION['wp_success'] = "Набор <b>{$packageName}</b> (язык: <b>{$lang->languageName}</b>) создан! Скопируйте ссылку. Любой, кто перейдёт по ней, сможет добавить этот набор слов";
        $_SESSION['wp_link'] = "https://{$_SERVER['HTTP_HOST']}/words-package/add?id={$wordPackageID}";
    }

    $allLanguages = $languageR->getAllLanguages();

    $title = "Создать набор слов";
    $pageName = "words-package";

    $wpError = array_key_exists("wp_error", $_SESSION) ? $_SESSION['wp_error'] : '';
    $wpSuccess = array_key_exists("wp_success", $_SESSION) ? $_SESSION['wp_success'] : '';
    $wpLink = array_key_exists("wp_link", $_SESSION) ? $_SESSION['wp_link'] : '';
    unset($_SESSION['wp_error']);
    unset($_SESSION['wp_success']);
    unset($_SESSION['wp_link']);
?>
<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../../templates/header.php'; ?>
    
    <main>
        <?php if(!empty($wpError)): ?>
            <section class="error-window no-margin">
                <p><?= getSvgIcon('cross-rounded') . $wpError ?></p>
            </section>
        <?php elseif (!empty($wpSuccess)): ?>
            <section class="success-window no-margin">
                <p><?= getSvgIcon("file-check") . $wpSuccess ?></p>
                <button id="copyWp" data-link="<?= $wpLink ?>" class="small">Скопировать</button>
            </section>
        <?php endif; ?>
        <section class="no-margin">
            <hgroup>
                <h1>Создать набор слов</h1>
                <p>Будет создана ссылка, перейдя по которой пользователи смогут добавить нижеуказанные слова в один клик</p>
            </hgroup>
            <form action="/words-package" method="POST">
                <label for="a_langdict">Язык</label>
                <select id="a_langdict" name="langdict" required>
                    <?php foreach ($allLanguages as $language): ?>
                        <option value="<?= $language->languageCode ?>" <?php if ($_GET['langdict'] == $language->languageCode): ?> selected <?php endif; ?>><?= $language->languageName ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="a_packageName">Название набора</label>
                <input id="a_packageName" type="text" name="package_name" maxlength="100" pattern=".*\S+.*" autofocus required>
                <fieldset>
                    <legend>Слова в наборе</legend>
                    <div class="package-words" id="packageWords">
                        <div class="package-word">
                            <fieldset>
                                <legend>№ 1</legend>
                                <label for="a_word">Слово</label>
                                <input id="a_word" type="text" name="word[]" maxlength="100" pattern=".*\S+.*" autocomplete="off" required>
                                <label for="a_translation">Перевод</label>
                                <input id="a_translation" type="text" name="translation[]" maxlength="100" pattern=".*\S+.*" autocomplete="off" required>
                                <label for="a_transcription">Транскрипция</label>
                                <input id="a_transcription" type="text" name="transcription[]" maxlength="100" pattern=".*\S+.*" autocomplete="off">
                                <label for="a_description">Описание</label>
                                <textarea id="a_description" name="description[]" rows="3" maxlength="1000" autocomplete="off"></textarea>
                            </fieldset>
                        </div>
                    </div>
                    <div class="add-word-button-container">
                        <button type="button" id="addWord">Добавить слово</button>
                    </div>
                </fieldset>
                <input type="submit" value="Создать">
            </form>
        </section>
    </main>

    <script src="/assets/scripts/pages/words-package/index.js"></script>

    <template id="wordTemplate">
        <div class="package-word">
            <fieldset>
                <legend></legend>
                <label>Слово</label>
                <input type="text" name="word[]" maxlength="100" pattern=".*\S+.*" autocomplete="off" required>
                <label>Перевод</label>
                <input type="text" name="translation[]" maxlength="100" pattern=".*\S+.*" autocomplete="off" required>
                <label>Транскрипция</label>
                <input type="text" name="transcription[]" maxlength="100" pattern=".*\S+.*" autocomplete="off">
                <label>Описание</label>
                <textarea name="description[]" rows="3" maxlength="1000" autocomplete="off"></textarea>
                <button type="button" class="small remove-word-button">Удалить слово</button>
            </fieldset>
        </div>
    </template>
</body>
</html>