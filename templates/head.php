<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php if (!empty($metaDescription)): ?>
        <meta name="description" content="<?= $metaDescription ?>">
        <meta property="og:description" content="<?= $metaDescription ?>">
    <?php endif; ?>
    <?php if (!empty($metaTitle)): ?>
        <meta property="og:title" content="<?= $metaTitle ?>">
    <?php endif; ?>

    <meta property="og:type" content="website">
    <meta property="og:locale" content="ru_RU">
    <meta name="keywords" content="языки, изучение, словарь, тренировка, упражнения, английский">

    <title><?= $title ?></title>

    <link rel="icon" href="/assets/images/wordfeellogo.svg" type="image/svg+xml">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/assets/styles/styles.css">

    <?php if (!empty($pageName)): ?>
    <link rel="stylesheet" href="/assets/styles/pages/<?= $pageName ?>.css">
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js"></script>
    <?php if (in_array($title, ["Создание ученика", "Вход в Wordfeel"])): ?>
        <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
    <?php endif; ?>
</head>