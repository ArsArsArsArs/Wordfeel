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
use App\UserStats;
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
    if (isset($_GET['for']) && is_numeric($_GET['for'])) {
        $user = $userR->getUserByID((int)$_GET['for'], $user->id);
        if (!$user) {
            redirect("/");
        }
    }

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

    $currentDateObject = new DateTime();
    if (isset($_GET['start'])) {
        list ($year, $month, $day) = explode("-", $_GET['start']);
        if (!is_numeric($year) || !is_numeric($month) || !is_numeric($day)) {
            redirect("/");
        }
        if (!checkdate($month, $day, $year)) {
            $startDate = (clone $currentDateObject)->modify('-5 days')->format("Y-m-d");
        } else {
            $startDate = $_GET['start'];
        }
    } else {
        $startDate = (clone $currentDateObject)->modify('-5 days')->format("Y-m-d");
    }
    if (isset($_GET['end'])) {
        list ($year, $month, $day) = explode("-", $_GET['end']);
        if (!is_numeric($year) || !is_numeric($month) || !is_numeric($day)) {
            redirect("/");
        }
        if (!checkdate($month, $day, $year)) {
            $endDate = $currentDateObject->format("Y-m-d");
        } else {
            $endDate = $_GET['end'];
        }
    } else {
        $endDate = $currentDateObject->format("Y-m-d");
    }
    if (strtotime($startDate) > strtotime($endDate)) {
        $startDate = (clone $currentDateObject)->modify('-5 days')->format("Y-m-d");
        $endDate = $currentDateObject->format("Y-m-d");
    }

    $userStatsArray = $userStatsR->getRangeOfStats($user->id, $language->languageCode, $startDate, $endDate);
    
    $jsonUserStats = json_encode($userStatsArray);
    if (!$jsonUserStats) {
        error_log("Failed to encode user stats. ID: {$user->id}, LC: {$language->languageCode}, start date: {$startDate}, end date: {$endDate}");
        redirect("/personal");
    }
 
    $title = "Статистика | {$language->languageName}";
    $pageName = "personal/stats";
?>
<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../../templates/header.php'; ?>  
    
    <main>
        <?php if (isset($_GET['for']) && is_numeric($_GET['for'])): ?>
            <section class="caution">
                <p>Вы смотрите словарь пользователя <i><?= $user->username ?></i></p>
            </section>
        <?php endif ?>
        <section class="stats-title no-margin">
            <h1>Статистика</h1>
            <a href="/personal?langdict=<?= $_GET['langdict'] ?><?php if (isset($_GET['for']) && is_numeric($_GET['for'])) { echo "&for={$_GET['for']}"; } ?>" class="a-button">Назад</a>
        </section>
        <section>
            <form action="/personal/stats" method="GET" class="inline">
                <label for="startDateInput">Начало периода</label>
                <input type="date" name="start" id="startDateInput" max="<?= $currentDateObject->format("Y-m-d"); ?>" required>
                <label for="endDateInput">Конец периода</label>
                <input type="date" name="end" id="endDateInput" max="<?= $currentDateObject->format("Y-m-d"); ?>" required>
                <input type="hidden" name="langdict" value="<?= $_GET['langdict'] ?>">
                <?php if (isset($_GET['for']) && is_numeric($_GET['for'])): ?>
                    <input type="hidden" name="for" value="<?= $_GET['for'] ?>">
                <?php endif; ?>
                <input type="submit" value="Применить">
            </form>
        </section>
        <hr>
        <section class="no-margin">
            <div class="chart-scroll-container">
                <div class="chart-inner-container">
                    <canvas id="statsCanvas" data-statsinfo='<?= $jsonUserStats ?>'></canvas>
                </div>
            </div>
        </section>
    </main>

    <script src="/assets/scripts/pages/personal/stats.js"></script>
</body>
</html>