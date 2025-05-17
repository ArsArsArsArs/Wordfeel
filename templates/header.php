<?php
    require_once __DIR__ . '/../src/functions.php';

    use function App\getSvgIcon;
?>
<header>
    <div class="logo-part">
        <a href="/">
            <img src="/assets/images/wordfeellogo.webp" class="logo">
        </a>
    </div>
    <div class="account-part">
        <a href="/account" target="_blank">
            <?= getSvgIcon("user"); ?>
        </a>
    </div>
</header>