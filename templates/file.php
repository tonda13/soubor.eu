<?php
preg_match('/<h1[^>]*>(.*?)<\/h1>/si', $content, $matches);
$heading = strip_tags($matches[1] ?? $title);
$body = preg_replace('/<h1[^>]*>.*?<\/h1>/si', '', $content, 1);
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($heading) ?></title>
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
    <main class="page-content">
        <?= $body ?>
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>
</body>
</html>
