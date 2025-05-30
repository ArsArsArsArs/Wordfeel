<?php
    session_start();

    require __DIR__ . '/../../../src/functions.php';
    require_once __DIR__ . '/../../../src/Database.php';
    require_once __DIR__ . '/../../../src/models/User.php';
    require_once __DIR__ . '/../../../src/models/Language.php';
    require_once __DIR__ . '/../../../src/models/Tag.php';
    require_once __DIR__ . '/../../../src/models/UserWord.php';

    $env = parse_ini_file(__DIR__ . '/../../../.env');

    use function App\redirect;
    use function App\customGetEnv;
    use function App\getSvgIcon;
    use App\Database;
    use App\UserRepository;
    use App\LanguageRepository;
    use App\TagRepository;
    use App\UserWordRepository;

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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $language = isset($_POST['language']) ? $_POST['language'] : '';
        if (empty($language)) {
            redirect("/personal");
        }
        $language = $languageR->getLanguageByCode($language);
        if (!$language) {
            redirect("/personal");
        }

        $linkForRedirecting = "/personal/add?langdict={$language->languageCode}";

        $userFor = isset($_POST['user_for']) ? $_POST['user_for'] : '';
        if (empty($userFor)) {
            redirect('/personal');
        }
        $userFor = $userR->getUserByUsername($_POST['user_for'], $user->id);
        if (!$userFor) {
            redirect('/personal');
        }
        if ($userFor->id !== $user->id) {
            $linkForRedirecting .= "&for={$userFor->id}";
        }

        $word = isset($_POST['word']) ? $_POST['word'] : '';
        if (empty($word)) {
            $_SESSION['add_error'] = 'Слово должно быть введено';
            redirect($linkForRedirecting);
        }
        if (!preg_match('/^[\p{L}0-9]([\p{L}0-9 ])*[\p{L}0-9]$/u', $word)) {
            $_SESSION['add_error'] = 'Слово должно состоять из букв и цифр';
            redirect($linkForRedirecting);
        }
        if (mb_strlen($word) > 100) {
            $_SESSION['add_error'] = 'Слово не должно быть больше 100 символов';
            redirect($linkForRedirecting);
        }
        $word = mb_strtolower($word);

        $translation = isset($_POST['translation']) ? $_POST['translation'] : '';
        if (empty($translation)) {
            $_SESSION['add_error'] = 'Перевод должен быть введён';
            redirect($linkForRedirecting);
        }
        if (!preg_match('/^[\p{L}0-9]([\p{L}0-9 ])*[\p{L}0-9]$/u', $translation)) {
            $_SESSION['add_error'] = 'Перевод должен состоять из букв и цифр';
            redirect($linkForRedirecting);
        }
        if (mb_strlen($translation) > 100) {
            $_SESSION['add_error'] = 'Перевод не должен быть больше 100 символов';
            redirect($linkForRedirecting);
        }
        $translation = mb_strtolower($translation);

        $transcription = isset($_POST['transcription']) ? htmlspecialchars($_POST['transcription']) : '';

        $description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '';

        $tags = $_POST['tags'];

        try {
            $userWordR->addWord($userFor->id, $language->languageCode, $word, $translation, $transcription, $description);
        } catch(Exception $e) {
            if ($e->getCode() == '23000') {
                $_SESSION['add_error'] = 'Такое слово уже существует в словаре выбранного языка';
                redirect($linkForRedirecting);
            } else {
                $_SESSION['add_error'] = 'При работе с базой данных произошла ошибка. Пожалуйста, попробуйте ещё раз';
                error_log("Failed to create word: {$e->getMessage()}");
                redirect($linkForRedirecting);
            }
        }

        $tagR->assignTags($userFor->id, $language->languageCode, $userWordR->lastWordAddedID, $tags);

        $_SESSION['add_success'] = "Слово <b>{$word}</b> успешно добавлено!";
        redirect($linkForRedirecting);
    }

    if (!isset($_GET['langdict'])) {
        redirect("/personal");
    }

    if (isset($_GET['for'])) {
        $user = $userR->getUserByID((int)$_GET['for'], $user->id);
        if (!$user) {
            redirect("/");
        }
    }

    $allLanguages = $languageR->getAllLanguages();
    if (!$allLanguages) {
        redirect("/");
    }

    $allUserTags = $tagR->getAllTagsByUserIDandLanguageCode($user->id, $_GET['langdict']);

    $title = "Добавить слово";
    $pageName = "personal/add";
    $addSuccess = array_key_exists('add_success', $_SESSION) ? $_SESSION['add_success'] : '';
    $addError = array_key_exists('add_error', $_SESSION) ? $_SESSION['add_error'] : '';
    unset($_SESSION['add_success']);
    unset($_SESSION['add_error']);
?>
<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../../templates/header.php'; ?>
    <main>
        <section class="add-language-title no-margin">
            <h1>Добавить слово</h1>
            <a href="/personal?langdict=<?= $_GET['langdict'] ?><?php if (isset($_GET['for']) && is_numeric($_GET['for'])) { echo "&for={$_GET['for']}"; } ?>" class="a-button">Назад</a>
        </section>
        <?php if (!empty($addSuccess)): ?>
            <section class="success-window no-margin">
                <p><?= getSvgIcon('file-check') . $addSuccess ?></p>
            </section>
        <?php endif; ?>
        <?php if(!empty($addError)): ?>
            <section class="error-window no-margin">
                <p><?= getSvgIcon('cross-rounded') . $addError ?></p>
            </section>
        <?php endif; ?>
        <article>
            <form action="/personal/add" method="POST">
                <fieldset class="general">
                    <div>
                        <label for="a_language">Язык</label>
                        <select id="a_language" name="language">
                            <?php foreach($allLanguages as $language): ?>
                                <option value="<?= $language->languageCode ?>" <?php if ($language->languageCode == $_GET['langdict']): ?> selected <?php endif; ?>><?= $language->languageName ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if (isset($allUserTags)): ?>
                        <div>
                            <label for="a_tag">Теги</label>
                            <select id="a_tag" name="tags[]" multiple>
                                <?php foreach ($allUserTags as $tag): ?>
                                    <option value="<?= $tag->tagName ?>"><?= $tag->tagName ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <a href="/personal/addTag?langdict=<?= $_GET['langdict'] ?><?php if (isset($_GET['for'])) { echo "&for={$_GET['for']}"; } ?>"><?= getSvgIcon("plus") ?>Создать тег</a>
                    <div>
                        <label for="a_user">Пользователь</label>
                        <input type="text" name="user_for" value="<?= $user->username ?>" readonly>
                    </div>
                </fieldset>
                <fieldset>
                    <label for="a_word">Слово</label>
                    <input id="a_word" type="text" name="word" maxlength="100" pattern=".*\S+.*" autocomplete="off" autofocus required>
                    <label for="a_translation">Перевод</label>
                    <input id="a_translation" type="text" name="translation" maxlength="100" pattern=".*\S+.*" autocomplete="off" required>
                    <label for="a_transcription">Транскрипция</label>
                    <input id="a_transcription" type="text" name="transcription" maxlength="100" pattern=".*\S+.*" autocomplete="off">
                    <label for="a_description">Описание</label>
                    <textarea id="a_description" name="description" rows="5" maxlength="1000" autocomplete="off"></textarea>
                    <input type="submit" value="Добавить слово">
                </fieldset>
            </form>
        </article>
    </main>
</body>
</html>