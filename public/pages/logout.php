<?php
    require_once __DIR__ . '/../../src/functions.php';

    use function App\redirect;

    if (isset($_COOKIE['at'])) {
        unset($_COOKIE['at']);
        setcookie("at", "", time()-3600);
    }
    redirect("/");
?>