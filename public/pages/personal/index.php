<?php
    require __DIR__ . '/../../../src/functions.php';
    require_once __DIR__ . '/../../../src/Database.php';
    require_once __DIR__ . '/../../../src/models/User.php';
    require_once __DIR__ . '/../../../src/models/Language.php';
    require_once __DIR__ . '/../../../src/models/Tag.php';
    require_once __DIR__ . '/../../../src/models/UserWord.php';

    use function App\capitalizeFirstLetter;
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
                        <select id="lo_tagselect">
                            <option value=""></option>
                            <?php foreach($allUserLangTags as $tag): ?>
                                <option value="<?= $tag->tagID ?>" <?php if ($_GET['tag'] == $tag->tagID): ?> selected <?php endif; ?>><?= $tag->tagName ?></option>
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

                    if (!isset($_GET['tag'])) {
                        $words = $userWordR->getAllWordsByUserIDandLanguageCode($user->id, $currentLanguage->languageCode);
                    } else {
                        if (!is_numeric($_GET['tag'])) {
                            $words = $userWordR->getAllWordsByUserIDandLanguageCode($user->id, $currentLanguage->languageCode);
                        } else {
                            $words = $userWordR->getTaggedWordsByUserIDandLanguageCode($user->id, $currentLanguage->languageCode, (int)$_GET['tag']);
                        }
                    }
                ?>
                <div class="language-title">
                    <h1><?= $currentLanguage->languageName ?></h1>
                    <div class="buttons">
                        <a href="/personal/add?langdict=<?= $currentLanguage->languageCode ?>" class="a-button">Добавить</a>
                        <a href="/personal/train?langdict=<?= $currentLanguage->languageCode ?>" class="a-button">Тренироваться</a>
                        <a href="/personal/stats?langdict=<?= $currentLanguage->languageCode ?>" class="a-button">Статистика</a>
                    </div>
                </div>
                <?php if (!$words): ?>
                    <p>Слов пока нет. Добавляйте их сюда по мере своего обучения</p>
                <?php else: ?>
                    <table>
                        <colgroup>
                            <col style="width: 40%;">
                            <col style="width: 40%;">
                            <col style="width: 10%;">
                            <col style="width: 10%;">
                        </colgroup>
                        <caption>Список слов</caption>
                        <tr>
                            <th>Слово</th>
                            <th>Перевод</th>
                            <th>%</th>
                            <th></th>
                        </tr>
                        <?php foreach($words as $word): ?>
                            <tr id="word<?= $word->wordID ?>">
                                <td><a href="/personal/word?langdict=<?= $currentLanguage->languageCode ?>&id=<?= $word->wordID ?>" target="_blank"><?= capitalizeFirstLetter($word->word) ?></a></td>
                                <td><?= capitalizeFirstLetter($word->translation) ?></td>
                                <td class="<?php $memorizationPercent = $word->memorizationPercent ? $word->memorizationPercent : 0; if ($memorizationPercent <= 24) { echo 'low-percent'; } else if ($memorizationPercent <= 74) { echo 'medium-percent'; } else { echo 'high-percent'; } ?>"><?= $memorizationPercent; ?></td>
                                <td><img src="/assets/images/svgs/delete.svg" alt="Delete button" class="delete-word-button" data-wordid="<?= $word->wordID ?>" data-word="<?= $word->word ?>" data-username="<?= $user->username ?>"></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
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