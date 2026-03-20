<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stránka nenalezena</title>
    <link rel="stylesheet" href="/assets/everest.css">
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <header class="page-header">
        <div class="page-content">
            <h1>404 — Stránka nenalezena</h1>
        </div>
    </header>
    <main class="page-content">
        <p>Požadovaný dokument <code><?= htmlspecialchars($path) ?></code> neexistuje.</p>
        <p><a href="/">← Zpět na seznam článků</a></p>
    </main>
</body>
</html>
