<?php
    require __DIR__ . '/../../../src/functions.php';
    require_once __DIR__ . '/../../../src/Database.php';
    require_once __DIR__ . '/../../../src/models/User.php';
    require_once __DIR__ . '/../../../src/models/Language.php';
    require_once __DIR__ . '/../../../src/models/Tag.php';

    $env = parse_ini_file(__DIR__ . '/../../../.env');

    use function App\redirect;
    use function App\customGetEnv;
    use App\Database;
    use App\UserRepository;
    use App\LanguageRepository;
    use App\TagRepository;

    $token = $_COOKIE['at'];
    if (!isset($token)) {
        redirect("/auth");
    }

    $db = new Database(customGetEnv("DB_HOST", $env), customGetEnv("DB_NAME", $env), customGetEnv("DB_CHARSET", $env), customGetEnv("DB_USERNAME", $env), customGetEnv("DB_PASSWORD", $env));
    $userR = new UserRepository($db->getConnection());
    $languageR = new LanguageRepository($db->getConnection());
    $tagR = new TagRepository($db->getConnection());

    $user = $userR->getUserByToken($token);
    if (!$user) {
        redirect("/");
    }
    $allLanguages = $languageR->getAllLanguages();
    if (!$allLanguages) {
        redirect("/");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    }

    $title = "Добавить слово";
    $pageName = "personal/add";
    $addError = $_SESSION['add_error'];
    unset($_SESSION['add_error']);
?>
<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../../templates/header.php'; ?>
    <main>
        <div class="add-language-title">
            <h1>Добавить слово</h1>
            <div class=".buttons">
                <a href="/personal?langdict=<?= $_GET['langdict'] ?>" class="a-button">Назад</a>
            </div>
        </div>
        <article>
            <form action="/personal/add" method="POST">
                <fieldset>
                    <label for="a_language">Язык</label>
                    <select id="a_language" disabled>
                        <?php foreach($allLanguages as $language): ?>
                            <option value="<?= $language->languageCode ?>" <?php if ($language->languageCode == $_GET['langdict']): ?> selected <?php endif; ?>><?= $language->languageName ?></option>
                        <?php endforeach; ?>
                    </select>
                </fieldset>
                <fieldset>
                    <label for="a_word">Слово</label>
                    <input id="a_word" type="text" name="word" maxlength="60" pattern=".*\S+.*" required>
                    <label for="a_translation">Перевод</label>
                    <input id="a_translation" type="text" name="translation" maxlength="60" pattern=".*\S+.*" required>
                    <label for="a_transcription">Транскрипция</label>
                    <input id="a_transcription" type="text" name="transcription" maxlength="60" pattern=".*\S+.*">
                    <label for="a_description">Описание</label>
                    <textarea id="a_description" name="description" rows="5" maxlength="1000"></textarea>
                    <input type="submit" value="Добавить слово">
                </fieldset>
            </form>
        </article>
    </main>
</body>
</html>