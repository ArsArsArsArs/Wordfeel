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

        $linkForRedirecting = "/personal/addTag?langdict={$language->languageCode}";

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
        

        $tag = isset($_POST['tag']) ? $_POST['tag'] : '';
        if (empty($tag)) {
            $_SESSION['addTag_error'] = 'Название тега должно быть введено';
            redirect($linkForRedirecting);
        }
        if (!preg_match('/^[0-9A-Za-z\x{0400}-\x{04FF}]+$/u', $tag)) {
            $_SESSION['addTag_error'] = 'Тег должен состоять из букв и цифр';
            redirect($linkForRedirecting);
        } 
        if (mb_strlen($tag) > 50) {
            $_SESSION['addTag_error'] = "Тег может быть длиною лишь до 50 символов";
            redirect($linkForRedirecting);
        }

        try {
            $tagR->createTag($userFor->id, $language->languageCode, $tag);
        } catch(Exception $e) {
            if ($e->getCode() == '23000') {
                $_SESSION['addTag_error'] = 'Тег с таким названием уже существует. Пожалуйста, попробуйте что-нибудь другое';
                redirect($linkForRedirecting);
            } else {
                $_SESSION['addTag_error'] = 'При работе с базой данных произошла ошибка. Пожалуйста, попробуйте ещё раз';
                error_log("Failed to create tag: {$e->getMessage()}");
                redirect($linkForRedirecting);
            }
        }

        redirect(str_replace("addTag", "add", $linkForRedirecting));
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

    $title = "Добавить тег";
    $pageName = "personal/add";
    $addTagError = array_key_exists('addTag_error', $_SESSION) ? $_SESSION['addTag_error'] : '';
    unset($_SESSION['addTag_error']);
?>
<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../../templates/header.php'; ?>
    <main>
        <section class="add-language-title no-margin">
            <h1>Создать тег</h1>
            <a href="/personal/add?langdict=<?= $_GET['langdict'] ?><?php if(isset($_GET['for'])) { echo "&for={$_GET['for']}"; } ?>" class="a-button">Назад</a>
        </section>
        <?php if(!empty($addTagError)): ?>
            <section class="error-window no-margin">
                <p><?= getSvgIcon('cross-rounded') . $addTagError ?></p>
            </section>
        <?php endif; ?>
        <article>
            <form action="/personal/addTag" method="POST">
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
                    <label for="a_word">Название для тега</label>
                    <input id="a_word" type="text" name="tag" maxlength="50" pattern="[A-Za-z0-9А-Яа-яЁё]+" title="Можно использовать только цифры, русские и английские буквы" autocomplete="off" required>
                    <div class="create-or-delete">
                        <input type="submit" value="Создать тег">
                        <a href="/personal/deleteTag?langdict=<?= $_GET['langdict'] ?><?php if (isset($_GET['for'])) { echo "&for={$_GET['for']}"; } ?>"><?= getSvgIcon('file-minus') ?> Я хочу удалить тег</a>
                    </div>
                </fieldset>
            </form>
        </article>
    </main>
</body>
</html>