<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soubor.eu</title>
    <link rel="stylesheet" href="/assets/everest.css">
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <header class="page-header">
        <div class="page-content">
            <h1>Soubor.eu</h1>
        </div>
    </header>
    <main class="page-content">
        <p>Jednoduchá platforma pro sdílení samostatných článků a dokumentů. Každý článek je uložen jako Markdown soubor a zobrazí se na vlastní URL — bez databáze, bez přihlašování, bez zbytečností.</p>
        <p>Obsah se píše v Markdownu s podporou <a href="https://github.github.com/gfm/" target="_blank">GitHub Flavored Markdown</a> — tabulky, přeškrtnutí, bloky kódu se zvýrazněním syntaxe.</p>

        <table>
            <thead>
                <tr>
                    <th>Název</th>
                    <th>URL</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $file): ?>
                <tr>
                    <td><a href="<?= htmlspecialchars($file['url']) ?>"><?= htmlspecialchars($file['title']) ?></a></td>
                    <td><code><?= htmlspecialchars($file['url']) ?></code></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
