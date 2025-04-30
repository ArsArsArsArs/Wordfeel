<?php
    use function App\getSvgIcon;
    require __DIR__ . '/../src/functions.php';

    $title = 'Wordfeel';
    $pageName = 'index';
?>

<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../templates/header.php'; ?>
    <main>
        <section>
            <h1>Добро пожаловать на Wordfeel!</h1>
            <p>Изучаете язык и хотите иметь надёжное место для хранения и репетиции выученных слов? Воспользуйтесь
                <b>Wordfeel</b>: он позволяет создать собственную коллекцию слов на разных языках, а увлекательные
                режимы тренировок помогут их не забывать.
            </p>
            <p><?= getSvgIcon('free') ?> <b>Бесплатно!</b></p>
        </section>
        <hr>
        <section>
            <div class="word-search">
                <hgroup>
                    <h2>Поискать слово</h2>
                    <p>Среди своей коллекции</p>
                </hgroup>
                <form method="GET">
                    <input type="text" placeholder="Из любого языка" name="word">
                    <input type="submit" value="Поискать">
                </form>
            </div>
        </section>
    </main>
</body>

</html>