<?php
    session_start();

    require __DIR__ . '/../../../src/functions.php';
    require_once __DIR__ . '/../../../src/Database.php';
    require_once __DIR__ . '/../../../src/models/User.php';
    require_once __DIR__ . '/../../../src/models/Language.php';
    require_once __DIR__ . '/../../../src/models/Tag.php';

    $env = parse_ini_file(__DIR__ . '/../../../.env');

    use function App\redirect;
    use function App\customGetEnv;
    use function App\getSvgIcon;
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $language = isset($_POST['language']) ? $_POST['language'] : '';
        if (empty($language)) {
            redirect("/personal");
        }
        $language = $languageR->getLanguageByCode($language);
        if (!$language) {
            redirect("/personal");
        }

        $linkForRedirecting = "/personal/deleteTag?langdict={$language->languageCode}";

        $userFor = isset($_POST['user_for']) ? $_POST['user_for'] : '';
        if (empty($userFor)) {
            redirect('/personal');
        }
        $userFor = $userR->getUserByUsername($_POST['user_for'], $user->id);
        if (!$userFor) {
            redirect('/personal');
        }
        $linkForRedirecting .= "&for={$userFor->id}";

        if (!isset($_POST['tagsToDelete'])) {
            $_SERVER['deleteTag_error'] = 'Не было выбрано ни одного тега';
            redirect($linkForRedirecting);
        }

        $tagsToDelete = $_POST['tagsToDelete'];

        foreach($tagsToDelete as $tag) {
            try {
                $tagR->deleteTag($userFor->id, $language->languageCode, $tag);
            } catch(Exception $e) {
                $_SERVER['deleteTag_error'] = "С удалением тега {$tag} произошла ошибка. Возможно, его просто уже не существует. Попробуйте ещё раз, пожалуйста";
                error_log("Failed to delete tag: {$e->getMessage()}");
                redirect($linkForRedirecting);
            }
        }

        redirect(str_replace("deleteTag", "add", $linkForRedirecting));
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

    $title = "Удалить теги";
    $pageName = "personal/add";
    $deleteTagError = array_key_exists('deleteTag_error', $_SESSION) ? $_SESSION['deleteTag_error'] : '';
    unset($_SESSION['deleteTag_error']);
?>
<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../../templates/header.php'; ?>
    <main>
        <section class="add-language-title no-margin">
            <h1>Удалить теги</h1>
            <div class=".buttons">
                <a href="/personal/add?langdict=<?= $_GET['langdict'] ?><?php if(isset($_GET['for'])) { echo "&for={$_GET['for']}"; } ?>" class="a-button">Назад</a>
            </div>
        </section>
        <?php if(!empty($deleteTagError)): ?>
            <section class="error-window no-margin">
                <p><?= getSvgIcon('cross-rounded') . $deleteTagError ?></p>
            </section>
        <?php endif; ?>
        <article>
            <?php if (isset($allUserTags) && count($allUserTags) > 0): ?>
                <form action="/personal/deleteTag" method="POST">
                    <fieldset class="general">
                        <div>
                            <label for="a_language">Язык</label>
                            <select id="a_language" name="language">
                                <?php foreach($allLanguages as $language): ?>
                                    <option value="<?= $language->languageCode ?>" <?php if ($language->languageCode == $_GET['langdict']): ?> selected <?php endif; ?>><?= $language->languageName ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="a_user">Пользователь</label>
                            <input type="text" name="user_for" value="<?= $user->username ?>" readonly>
                        </div>
                    </fieldset>
                    <fieldset>
                        <?php foreach($allUserTags as $tag): ?>
                            <label><input type="checkbox" name="tagsToDelete[]" value="<?= $tag->tagName ?>"><?= $tag->tagName ?></label>
                        <?php endforeach; ?>
                    </fieldset>
                    <input type="submit" value="Удалить выбранное">
                </form>
            <?php else: ?>
                <p>Тегов пока нет</p>
            <?php endif; ?>
        </article>
    </main>
</body>
</html>