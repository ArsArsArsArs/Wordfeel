<?php
    require __DIR__ . '/../../src/functions.php';
    use function App\redirect;

    $token = $_COOKIE['at'];
    if (!isset($token)) {
        redirect("/auth");
    }

    $word = $_GET['word'];

    $title = "Поиск | {$word}";
?>

<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../templates/header.php'; ?>
</body>

</html>