<?php
    session_start();

    require_once __DIR__ . '/../../../src/functions.php';
    require_once __DIR__ . '/../../../src/Database.php';
    require_once __DIR__ . '/../../../src/models/User.php';

    use function App\customGetEnv;
    use function App\getSvgIcon;
    use function App\redirect;
    use function App\veryfiyCaptcha;
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

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // $hCaptchaResponse = isset($_POST['h-captcha-response']) ? $_POST['h-captcha-response'] : '';
        // if (empty($hCaptchaResponse)) {
        //     $_SESSION['muc_error'] = 'Перед отправкой нужно пройти проверку (каптчу). Если её не видно, стоит перезагрузить страницу';
        //     redirect('/manage-users/create');
        // }
        // $isCaptchaValid = veryfiyCaptcha(customGetEnv('HCAPTCHA_SECRET', $env), $hCaptchaResponse);
        // if (!$isCaptchaValid) {
        //     $_SESSION['muc_error'] = 'Каптча не была пройдена правильно. Пожалуйста, попробуйте ещё раз';
        //     redirect('/manage-users/create');
        // }

        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        if (empty($username)) {
            $_SESSION['muc_error'] = 'Имя пользователя должно быть заполнено';
            redirect('/manage-users/create');
        }
        if (!preg_match('/^[0-9A-Za-z\x{0400}-\x{04FF}]+$/u', $username)) {
            $_SESSION['muc_error'] = 'Имя пользователя должно состоять из букв и цифр';
            redirect('/manage-users/create');
        }
        $usernameLen = mb_strlen($username);
        if (($usernameLen < 4) || ($usernameLen > 22)) {
            $_SESSION['muc_error'] = 'Имя пользователя должно содержать от 4 до 22 символов';
            redirect('/manage-users/create');
        }
        
        try {
            $newUser = $userR->createManagedUser($user->id, $username);
        } catch(Exception $e) {
            if ($e->getCode() == '23000') {
                $_SESSION['muc_error'] = 'Пользователь с таким именем уже существует. Пожалуйста, придумайте что-нибудь другое';
                redirect('/manage-users/create');
            } else {
                $_SESSION['muc_error'] = 'При работе с базой данных произошла ошибка. Пожалуйста, попробуйте ещё раз';
                error_log("Failed to create user: {$e->getMessage()}");
                redirect('/manage-users/create');
            }
        }

        $_SESSION['muc_success'] = "Пользователь <b>{$username}</b> создан! Данные аккаунта:<br><b>Пароль:</b> {$newUser['generatedPassword']}<br><b>Ключ:</b> {$newUser['generatedLinkKey']}<br>Скопируйте ссылку, используя кнопку ниже, и отправьте ученику, чтобы он вошёл в свой аккаунт";
        $_SESSION['muc_link'] = "https://{$_SERVER['HTTP_HOST']}/auth?key={$newUser['generatedLinkKey']}";

        redirect('/manage-users/create');
    }

    $title = "Создание ученика";
    $pageName = "manage-users/create";

    $mucError = array_key_exists('muc_error', $_SESSION) ? $_SESSION['muc_error'] : '';
    $mucSuccess = array_key_exists('muc_success', $_SESSION) ? $_SESSION['muc_success'] : '';
    $mucLink = array_key_exists('muc_link', $_SESSION) ? $_SESSION['muc_link'] : '';
    unset($_SESSION['muc_error']);
    unset($_SESSION['muc_success']);
    unset($_SESSION['muc_link']);
?>

<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../../templates/header.php'; ?>

    <main>
        <?php if (!empty($mucSuccess)): ?>
            <section class="success-window no-margin">
                <p><?= getSvgIcon('user-check') . $mucSuccess ?></p>
                <button id="copyMuc" data-link="<?= $mucLink ?>" class="small">Скопировать</button>
            </section>
        <?php endif; ?>
        <?php if(!empty($mucError)): ?>
            <section class="error-window no-margin">
                <p><?= getSvgIcon('cross-rounded') . $mucError ?></p>
            </section>
        <?php endif; ?>
        <section class="create-student-title">
            <h1>Создание ученика</h1>
            <a href="/manage-users" class="a-button">Назад</a>
        </section>
        <section>
        <form action="/manage-users/create" method="POST" id="createForm">
        <fieldset>
            <legend>Реквизиты аккаунта</legend>
            <label for="l_username">Будущее имя пользователя:</label>
            <input type="text" minlength="4" maxlength="22" pattern="[A-Za-z0-9А-Яа-яЁё]+"
                title="Можно использовать только цифры, русские и английские буквы" id="l_username"
                name="username" required />
            <label for="l_password">Пароль:</label>
            <input type="text" id="l_password" name="password" placeholder="Будет сгенерирован автоматически" required readonly />
        </fieldset>
        <!-- <div class="h-captcha" data-sitekey="f89edfbe-1952-43fe-845e-077eeece780c"></div> -->
        <input type="submit" value="Создать ученика">
    </form>
        </section>
    </main>

    <script src="/assets/scripts/pages/manage-users/create.js"></script>
</body>

</html>