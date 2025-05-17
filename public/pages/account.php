<?php
    require_once __DIR__ . '/../../src/functions.php';
    require_once __DIR__ . '/../../src/Database.php';
    require_once __DIR__ . '/../../src/models/User.php';
    require_once __DIR__ . '/../../src/models/Language.php';

    use function App\customGetEnv;
    use function App\redirect;
    use App\Database;
    use App\UserRepository;
    use App\LanguageRepository;

    $env = parse_ini_file(__DIR__ . '/../../.env');

    if (!isset($_COOKIE['at'])) {
        redirect('/auth');
    }
    $token = $_COOKIE['at'];

    $db = new Database(customGetEnv("DB_HOST", $env), customGetEnv("DB_NAME", $env), customGetEnv("DB_CHARSET", $env), customGetEnv("DB_USERNAME", $env), customGetEnv("DB_PASSWORD", $env));
    $userR = new UserRepository($db->getConnection());
    $languageR = new LanguageRepository($db->getConnection());

    $user = $userR->getUserByToken($token);
    if (!$user) {
        redirect("/");
    }

    $userLanguages = $languageR->getAllLanguagesByUserId($user->id);
    $userLanguagesCount = 0;
    if (is_array($userLanguages)) {
        $userLanguagesCount = count($userLanguages);
    }
    $accountManager = $userR->getAccountManager($user->id);
    $accountType = is_null($accountManager) ? "самостоятельный" : "под управлением пользователя {$accountManager->username}";

    $title = $user->username;
    $pageName = "account";
?>
<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../templates/header.php'; ?>
    
    <main>
        <section class="no-margin">
            <article>
                <h1><?= $user->username ?></h1>
                <ul>
                    <li><b>Покрыто языков:</b> <?= $userLanguagesCount ?></li>
                    <li><b>Статус аккаунта:</b> <?= $accountType ?></li>
                </ul>
            </article>
        </section>
        <section class="actions">
            <a href="/personal" class="a-button">Мои слова</a>
            <a href="/manage-users" class="a-button">Управлять учениками</a>
            <a href="/logout" class="a-button danger">Выйти из аккаунта</a>
        </section>
    </main>
</body>
</html>