<?php
preg_match('/<h1[^>]*>(.*?)<\/h1>/si', $content, $matches);
$heading = strip_tags($matches[1] ?? $title);
$body    = preg_replace('/<h1[^>]*>.*?<\/h1>/si', '', $content, 1);
$url     = 'https://www.soubor.eu/' . ltrim($title, '/');

// Breadcrumb — segmenty cesty
$segments  = array_filter(explode('/', $title));
$crumbs = [];
foreach ($segments as $i => $segment) {
    $isLast   = $i === array_key_last($segments);
    $crumbs[] = [
        'label' => $isLast ? $heading : ucfirst(str_replace(['-', '_'], ' ', $segment)),
    ];
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($heading) ?></title>

    <meta property="og:type"        content="article">
    <meta property="og:title"       content="<?= htmlspecialchars($heading) ?>">
    <meta property="og:url"         content="<?= htmlspecialchars($url) ?>">
    <meta property="og:site_name"   content="Soubor.eu">
    <?php if ($description): ?>
    <meta property="og:description" content="<?= htmlspecialchars($description) ?>">
    <meta name="description"        content="<?= htmlspecialchars($description) ?>">
    <?php endif; ?>

    <link rel="stylesheet" href="/assets/everest.css">
    <link rel="stylesheet" href="/assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/github.min.css" media="(prefers-color-scheme: light)">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/github-dark.min.css" media="(prefers-color-scheme: dark)">
</head>
<body>
    <header class="page-header">
        <div class="page-content">
            <h1><?= htmlspecialchars($heading) ?></h1>
        </div>
    </header>

    <nav class="breadcrumb">
        <a href="/">Soubor.eu</a>
        <?php foreach ($crumbs as $crumb): ?>
            <span class="sep">›</span>
            <span><?= htmlspecialchars($crumb['label']) ?></span>
        <?php endforeach; ?>
    </nav>

    <main class="page-content">
        <?= $body ?>
    </main>

    <footer class="page-footer">
        <a href="/">← Zpět na seznam článků</a>
        <a href="/download/<?= htmlspecialchars($title) ?>">⬇ Stáhnout Markdown</a>
        <span class="footer-date">Upraveno <?= date('j. n. Y', $lastModified) ?></span>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>
</body>
</html>
