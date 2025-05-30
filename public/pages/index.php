<?php
    require __DIR__ . '/../../src/functions.php';
    use function App\getSvgIcon;

    $title = 'Wordfeel';
    $pageName = 'index';

    $metaTitle = "Wordfeel | Главная";
    $metaDescription = "Wordfeel позволяет запоминать слова из изучаемых языков более эффективно благодаря тренировкам";
?>

<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../templates/header.php'; ?>
    <main>
        <section class="no-margin">
            <h1>Добро пожаловать на Wordfeel!</h1>
            <p>Изучаете язык и хотите иметь надёжное место для хранения и репетиции выученных слов? Воспользуйтесь
                <b>Wordfeel</b>: он позволяет создать собственную коллекцию слов на разных языках, а увлекательные
                режимы тренировок помогут их не забывать.
            </p>
            <p><?= getSvgIcon('free') ?> <b>Бесплатно!</b></p>
        </section>
        <hr>
        <section class="words-access no-margin">
            <hgroup>
                <h2>Поискать слово</h2>
                <p>Среди своей коллекции</p>
            </hgroup>
            <form action="look-for" method="GET" class="inline">
                <input type="text" placeholder="Из любого языка" name="word" pattern=".*\S+.*" required>
                <input type="submit" value="Поискать">
            </form>
        </section>
        <section class="words-access">
            <a href="/personal" class="a-button">Мои слова</a>
        </section>
    </main>
</body>

</html>