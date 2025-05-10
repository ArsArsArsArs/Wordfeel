<?php
    require __DIR__ . '/../../../src/functions.php';
    require_once __DIR__ . '/../../../src/Database.php';
    require_once __DIR__ . '/../../../src/models/User.php';
    require_once __DIR__ . '/../../../src/models/Language.php';
    require_once __DIR__ . '/../../../src/models/UserStats.php';

    $env = parse_ini_file(__DIR__ . '/../../../.env');

    use function App\redirect;
    use function App\customGetEnv;
    use App\Database;
    use App\UserRepository;
    use App\LanguageRepository;
    use App\UserStatsRepository;

    $token = $_COOKIE['at'];
    if (!isset($token)) {
        redirect("/auth");
    }

    $db = new Database(customGetEnv("DB_HOST", $env), customGetEnv("DB_NAME", $env), customGetEnv("DB_CHARSET", $env), customGetEnv("DB_USERNAME", $env), customGetEnv("DB_PASSWORD", $env));
    $userR = new UserRepository($db->getConnection());
    $languageR = new LanguageRepository($db->getConnection());
    $userStatsR = new UserStatsRepository($db->getConnection());

    $user = $userR->getUserByToken($token);
    if (!$user) {
        redirect("/");
    }
    if (!isset($_GET['langdict'])) {
        redirect("/");
    }
    $language = $languageR->getLanguageByCode($_GET['langdict']);
    if (!$language) {
        redirect("/");
    }

    $title = "Статистика | {$language->languageName}";
    $personalName = "personal/stats";
?>
<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../../templates/header.php'; ?>  
    
    <main>
        <section class="stats-title no-margin">
            <h1>Статистика</h1>
            <a href="/personal?langdict=<?= $_GET['langdict'] ?>" class="a-button">Назад</a>
        </section>
        <section>
            <form action="/personal/stats?langdict=<?= $_GET['langdict'] ?>" method="GET" class="inline">
                <label for="startDateInput">Начало периода</label>
                <input type="date" name="start" id="startDateInput">
                <label for="endDateInput">Конец периода</label>
                <input type="date" name="end" id="endDateInput">
                <input type="submit" value="Применить">
            </form>
        </section>
        <hr>
        <section class="no-margin">
            <canvas id="statsCanvas"></canvas>
        </section>
    </main>

    <script src="/assets/scripts/pages/personal/stats.js"></script>
</body>
</html>