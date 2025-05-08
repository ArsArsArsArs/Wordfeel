<?php 
    require __DIR__ . '/../../../src/functions.php';
    require_once __DIR__ . '/../../../src/Database.php';
    require_once __DIR__ . '/../../../src/models/User.php';
    require_once __DIR__ . '/../../../src/models/UserWord.php';

    $env = parse_ini_file(__DIR__ . '/../../../.env');

    use function App\redirect;
    use function App\customGetEnv;
    use App\Database;
    use App\UserRepository;
    use App\UserWordRepository;

    $token = $_COOKIE['at'];
    if (!isset($token)) {
        redirect("/auth");
    }

    $db = new Database(customGetEnv("DB_HOST", $env), customGetEnv("DB_NAME", $env), customGetEnv("DB_CHARSET", $env), customGetEnv("DB_USERNAME", $env), customGetEnv("DB_PASSWORD", $env));
    $userR = new UserRepository($db->getConnection());
    $userWordR = new UserWordRepository($db->getConnection());

    $user = $userR->getUserByToken($token);
    if (!$user) {
        redirect("/");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userFor = isset($_POST['user_for']) ? $_POST['user_for'] : '';
        if (empty($userFor)) {
            http_response_code(400);
            exit();
        }
        $userFor = $userR->getUserByUsername($_POST['user_for'], $user->id);
        if (!$userFor) {
            http_response_code(400);
            exit();
        }

        $wordID = isset($_POST['wordID']) ? $_POST['wordID'] : '';
        if (empty($wordID)) {
            http_response_code(400);
            exit();
        }
        if (!is_numeric($wordID)) {
            http_response_code(400);
            exit();
        }

        try {
            $userWordR->deleteWord($userFor->id, (int)$wordID);
        } catch(Exception $e) {
            http_response_code(500);
            error_log("Failed to delete word: {$e->getMessage()}");
            exit();
        }

        http_response_code(200);
    }
?>