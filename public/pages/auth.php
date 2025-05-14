<?php
    session_start();

    require_once __DIR__ . '/../../src/functions.php';
    require_once __DIR__ . '/../../src/Database.php';
    require_once __DIR__ . '/../../src/models/User.php';

    use function App\customGetEnv;
    use function App\getSvgIcon;
    use function App\redirect;
    use App\Database;
    use App\UserRepository;

    $env = parse_ini_file(__DIR__ . '/../../.env');

    if (isset($_COOKIE['at'])) {
        redirect('/personal');
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        

        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        if (empty($username)) {
            $_SESSION['auth_error'] = 'Имя пользователя должно быть заполнено';
            redirect('/auth');
        }
        if (!preg_match('/^[0-9A-Za-z\x{0400}-\x{04FF}]+$/u', $username)) {
            $_SESSION['auth_error'] = 'Имя пользователя должно состоять из букв и цифр';
            redirect('/auth');
        }
        $usernameLen = mb_strlen($username);
        if (($usernameLen < 4) || ($usernameLen > 22)) {
            $_SESSION['auth_error'] = 'Имя пользователя должно содержать от 4 до 22 символов';
            redirect('/auth');
        }

        $password = isset($_POST['password']) ? $_POST['password'] : '';
        if (empty($password)) {
            $_SESSION['auth_error'] = 'Пароль должен быть введён';
            redirect('/auth');
        }

        $action = isset($_POST['action']) ? $_POST['action'] : '';

        $db = new Database(customGetEnv("DB_HOST", $env), customGetEnv("DB_NAME", $env), customGetEnv("DB_CHARSET", $env), customGetEnv("DB_USERNAME", $env), customGetEnv("DB_PASSWORD", $env));
        $userR = new UserRepository($db->getConnection());
        if ($action === "login") {
            try {
                $existingUser = $userR->loginUser($username, $password);
            } catch(Exception $e) {
                if ($e->getMessage() == 'Invalid username or password') {
                    $_SESSION['auth_error'] = 'Имя пользователя или пароль введены неправильно';
                    redirect('/auth');
                } else {
                    $_SESSION['auth_error'] = 'При работе с базой данных произошла ошибка. Пожалуйста, попробуйте ещё раз';
                    error_log("Failed to log in user: {$e->getMessage()}");
                    redirect('/auth');
                }
            }

            setcookie('at', $existingUser->at, time() + (86400 * 30), "/");

            redirect('/personal');
        } elseif ($action === "signup") {
            try {
                $newUser = $userR->createUser($username, $password);
            } catch(Exception $e) {
                if ($e->getCode() == '23000') {
                    $_SESSION['auth_error'] = 'Пользователь с таким именем уже существует. Пожалуйста, придумайте что-нибудь другое';
                    redirect('/auth');
                } else {
                    $_SESSION['auth_error'] = 'При работе с базой данных произошла ошибка. Пожалуйста, попробуйте ещё раз';
                    error_log("Failed to create user: {$e->getMessage()}");
                    redirect('/auth');
                }
            }

            setcookie('at', $newUser->at, time() + (86400 * 30), "/");

            redirect('/personal');
        } else {
            $_SESSION['auth_error'] = 'Ошибка при валидации формы. Обновите страницу';
            redirect('/auth');
        }
    }

    $title = "Вход в Wordfeel";
    $pageName = "auth";

    $authError = array_key_exists('auth_error', $_SESSION) ? $_SESSION['auth_error'] : '';
    unset($_SESSION['auth_error']);
?>

<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../templates/header.php'; ?>

    <main>
        <?php if(!empty($authError)): ?>
        <section class="error-window no-margin">
            <p><?= getSvgIcon('cross-rounded') . $authError ?></p>
        </section>
        <?php endif; ?>
        <section class="auth-windows no-margin">
            <div class="auth-window">
                <hgroup>
                    <h1>Войти в аккаунт</h1>
                    <p>Если уже создавали</p>
                </hgroup>
                <form action="auth" method="POST" id="loginForm">
                    <fieldset>
                        <legend>Реквизиты аккаунта</legend>
                        <label for="l_username">Имя пользователя:</label>
                        <input type="text" minlength="4" maxlength="22" pattern="[A-Za-z0-9А-Яа-яЁё]+"
                            title="Можно использовать только цифры, русские и английские буквы" id="l_username"
                            name="username" required />
                        <label for="l_password">Пароль:</label>
                        <input type="password" id="l_password" name="password" required />
                        <input type="hidden" name="action" value="login">
                    </fieldset>
                    <div class="h-captcha" data-sitekey="f89edfbe-1952-43fe-845e-077eeece780c"></div>
                    <input type="submit" value="Войти">
                </form>
            </div>
            <div class="auth-window">
                <hgroup>
                    <h1>Создать аккаунт</h1>
                    <p>Рады приветствовать Вас :)</p>
                </hgroup>
                <form action="auth" method="POST" id="signupForm">
                    <fieldset>
                        <legend>Реквизиты аккаунта</legend>
                        <label for="s_username">Новое имя пользователя:</label>
                        <input type="text" minlength="4" maxlength="22" pattern="[A-Za-z0-9А-Яа-яЁё]+"
                            title="Можно использовать только цифры, русские и английские буквы" id="s_username"
                            name="username" required />
                        <label for="s_password">Новый пароль:</label>
                        <input type="password" id="s_password" name="password" required />
                        <input type="hidden" name="action" value="signup">
                    </fieldset>
                    <div class="h-captcha" data-sitekey="f89edfbe-1952-43fe-845e-077eeece780c"></div>
                    <input type="submit" value="Создать">
                </form>
            </div>
        </section>
    </main>

    <script src="/assets/scripts/pages/auth.js"></script>
</body>

</html>