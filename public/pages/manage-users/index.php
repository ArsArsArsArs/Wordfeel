<?php
    require_once __DIR__ . '/../../../src/functions.php';
    require_once __DIR__ . '/../../../src/Database.php';
    require_once __DIR__ . '/../../../src/models/User.php';

    use function App\customGetEnv;
    use function App\getSvgIcon;
    use function App\redirect;
    use App\Database;
    use App\UserRepository;

    $env = parse_ini_file(__DIR__ . '/../../../.env');

    if (!isset($_COOKIE['at'])) {
        redirect('/auth');
    }
    $token = $_COOKIE['at'];

    $db = new Database(customGetEnv("DB_HOST", $env), customGetEnv("DB_NAME", $env), customGetEnv("DB_CHARSET", $env), customGetEnv("DB_USERNAME", $env), customGetEnv("DB_PASSWORD", $env));
    $userR = new UserRepository($db->getConnection());

    $user = $userR->getUserByToken($token);
    if (!$user) {
        redirect("/");
    }
    $managedUsers = $userR->getListOfManagedUsers($user->id);

    $title = "Управление учениками";
    $pageName = "manage-users";
?>
<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../../templates/header.php'; ?>

    <main>
        <section class="mu-title no-margin">
            <h1>Управление учениками</h1>
            <a href="/account" class="a-button">Назад</a>
        </section>
        <section class="actions">
            <a href="/manage-users/create" class="a-button">Создать ученика</a>
        </section>
        <section class="list">
            <?php if (is_null($managedUsers) || count($managedUsers) === 0): ?>
                <p>За Вами пока не закреплено никаких учеников</p>
            <?php else: ?>
                <?php foreach ($managedUsers as $managedUser): ?>
                    <div class="element">
                        <?= getSvgIcon("user"); ?>
                        <a href="/personal?for=<?= $managedUser->id ?>" target="_blank"><?= $managedUser->username ?></a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>