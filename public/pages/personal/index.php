<?php
    require __DIR__ . '/../../../src/functions.php';
    require_once __DIR__ . '/../../../src/Database.php';
    require_once __DIR__ . '/../../../src/models/User.php';
    require_once __DIR__ . '/../../../src/models/Language.php';
    require_once __DIR__ . '/../../../src/models/Tag.php';
    require_once __DIR__ . '/../../../src/models/UserWord.php';

    use function App\redirect;
    use function App\customGetEnv;
    use function App\getSvgIcon;
    use App\Database;
    use App\UserRepository;
    use App\LanguageRepository;
    use App\TagRepository;
    use App\UserWordRepository;

    $env = parse_ini_file(__DIR__ . '/../../../.env');

    $token = $_COOKIE['at'];
    if (!isset($token)) {
        redirect("/auth");
    }

    $db = new Database(customGetEnv("DB_HOST", $env), customGetEnv("DB_NAME", $env), customGetEnv("DB_CHARSET", $env), customGetEnv("DB_USERNAME", $env), customGetEnv("DB_PASSWORD", $env));
    $userR = new UserRepository($db->getConnection());
    $languageR = new LanguageRepository($db->getConnection());
    $tagR = new TagRepository($db->getConnection());
    $userWordR = new UserWordRepository($db->getConnection());

    $user = $userR->getUserByToken($token);
    if (!$user) {
        redirect("/");
    }
    $allLanguages = $languageR->getAllLanguages();
    if (!$allLanguages) {
        redirect("/");
    }


    $title = "Wordfeel";
    $pageName = "personal";
?>

<!DOCTYPE html>
<html lang="ru">
<?php include_once __DIR__ . '/../../../templates/head.php'; ?>

<body>
    <?php include_once __DIR__ . '/../../../templates/header.php'; ?>
    
    <main>
        <?php if (isset($_GET['langdict'])): ?>
            <section>
                <section class="langdict-options">
                    <div>
                        <label for="lo_langselect">Язык</label>
                        <select id="lo_langselect">
                            <?php foreach ($allLanguages as $language): ?>
                                <option value="<?= $language->languageCode ?>" <?php if ($_GET['langdict'] == $language->languageCode): ?> selected <?php endif; ?>><?= $language->languageName ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php
                        $allUserLangTags = $tagR->getAllTagsByUserIDandLanguageCode($user->id, $_GET['langdict']);

                        if ($allUserLangTags):
                    ?>
                    <div>
                        <label for="lo_tagselect">Тег</label>
                        <select id="lo_langselect">
                            <?php foreach($allUserLangTags as $tag): ?>
                                <option value="<?= $tag->$tagID ?>" <?php if ($_GET['tag'] == $tag->$tagID): ?> selected <?php endif; ?>><?= $tag->$tagName ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </section>
                <hr>
                <?php 
                    $currentLanguage = $languageR->getLanguageByCode($_GET['langdict']);
                    if (!$currentLanguage) {
                        redirect("/");
                    }

                    $words = $userWordR->getAllWordsByUserIDandLanguageCode($user->id, $currentLanguage->languageCode);
                ?>
                <div class="language-title">
                    <h1><?= $currentLanguage->languageName ?></h1>
                    <div class="buttons">
                        <a href="add.php?langdict=<?= $currentLanguage->languageCode ?>" class="a-button">Добавить</a>
                        <a href="train.php?langdict=<?= $currentLanguage->languageCode ?>" class="a-button">Тренироваться</a>
                        <a href="stats.php?langdict=<?= $currentLanguage->languageCode ?>" class="a-button">Статистика</a>
                    </div>
                </div>
                <?php if (!$words): ?>
                    <p>Слов пока нет. Добавляйте их сюда по мере своего обучения</p>
                <?php else: ?>

                <?php endif; ?>
            </section>
        <?php else: ?>
            <section>
                <?php
                    $allUserLanguages = $languageR->getAllLanguagesByUserId($user->id);
                ?>
                <hgroup>
                    <h1>В словарь какого языка перейдём?</h1>
                    <p>Все словари дополняете и обновляете Вы на своё усмотрение ;)</p>
                </hgroup>
                <?php if (!empty($allUserLanguages)): ?>
                    <section>
                        <h2>Ваши языки:</h2>
                        <?php foreach ($allUserLanguages as $userLanguage): ?>
                            <div class="language-choice">
                                <a href="/personal?langdict=<?= $userLanguage->languageCode ?>"><?= $userLanguage->languageName ?></a>
                            </div>
                        <?php endforeach; ?>
                    </section>
                <?php endif; ?>
                <section>
                    <div class="all-langs-title">
                        <h2>Все языки:</h2>
                        <div class="langs-search-bar">
                            <?= getSvgIcon("search") ?>
                            <input type="text" id="languagesSearch" placeholder="Искать язык">
                        </div>
                    </div>
                    <div id="allLangsContainer">
                        <?php foreach ($allLanguages as $language): ?>
                            <div class="language-choice">
                                <a href="/personal?langdict=<?= $language->languageCode ?>"><?= $language->languageName ?></a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </section>
        <?php endif; ?>
    </main>

    <script src="/assets/scripts/pages/personal.js"></script>
</body>

</html>