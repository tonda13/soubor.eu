<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — soubory</title>
    <link rel="stylesheet" href="/assets/everest.css">
    <link rel="stylesheet" href="/assets/style.css">
    <style>
        .btn {
            display: inline-block;
            padding: 0.3rem 0.75rem;
            border-radius: 4px;
            font-size: var(--fs-sm);
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-family: var(--font-body);
            line-height: 1;
            height: 2rem;
            box-sizing: border-box;
            display: inline-flex;
            align-items: center;
        }
        .btn-primary   { background: var(--accent-gradient-from); color: #fff; }
        .btn-secondary { background: var(--code-bg); color: var(--text); border: 1px solid var(--border); }
        .btn-danger    { background: var(--accent-alt); color: #fff; }

        /* Toolbar */
        .toolbar {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
        }
        .toolbar input[type="text"] {
            flex: 1;
            min-width: 160px;
            max-width: 300px;
            height: 2rem;
            padding: 0 0.75rem;
            font-size: var(--fs-sm);
            border: 1px solid var(--border);
            border-radius: 4px;
            background: var(--bg);
            color: var(--text);
            box-sizing: border-box;
            margin: 0;
        }
        .toolbar-sep {
            width: 1px;
            height: 1.5rem;
            background: var(--border);
            margin: 0 0.25rem;
        }

        /* Rozbalovací panely */
        .panel {
            display: none;
            background: var(--code-bg);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.25rem;
        }
        .panel.open { display: flex; gap: 0.75rem; align-items: flex-start; flex-wrap: wrap; }
        .panel input[type="text"],
        .panel input[type="file"] {
            height: 2rem;
            padding: 0 0.75rem;
            font-size: var(--fs-sm);
            font-family: var(--font-mono);
            border: 1px solid var(--border);
            border-radius: 4px;
            background: var(--bg);
            color: var(--text);
            box-sizing: border-box;
            margin: 0;
        }
        .panel input[type="text"] { width: 280px; }
        .panel input[type="file"] { padding: 0.25rem 0.5rem; font-family: var(--font-body); }
        .panel-hint {
            width: 100%;
            font-size: var(--fs-sm);
            color: var(--text-muted);
            margin-top: 0.4rem;
        }

        /* Tabulka */
        .admin-actions { display: flex; gap: 0.5rem; }
        table td:last-child { white-space: nowrap; }

        .badge-private {
            display: inline-block;
            font-size: 0.7rem;
            font-family: var(--font-body);
            background: color-mix(in srgb, var(--accent-alt) 15%, transparent);
            color: var(--accent-alt);
            border: 1px solid color-mix(in srgb, var(--accent-alt) 30%, transparent);
            border-radius: 3px;
            padding: 0.1rem 0.4rem;
            vertical-align: middle;
            margin-left: 0.4rem;
        }
        .saved-notice {
            background: color-mix(in srgb, var(--accent-gradient-from) 12%, transparent);
            border-left: 4px solid var(--accent-gradient-from);
            padding: 0.6rem 1rem;
            margin-bottom: 1.25rem;
            font-size: var(--fs-sm);
        }
    </style>
</head>
<body>
    <header class="page-header">
        <div class="page-content" style="display:flex; justify-content:space-between; align-items:center;">
            <h1>Administrace</h1>
            <div style="display:flex; gap:0.5rem;">
                <a href="/" class="btn btn-secondary">← Web</a>
                <a href="/admin/logout" class="btn btn-secondary">Odhlásit se</a>
            </div>
        </div>
    </header>

    <main class="page-content">
        <?php if ($saved): ?>
            <div class="saved-notice">Soubor byl uložen.</div>
        <?php endif; ?>

        <div class="toolbar">
            <input type="text" id="filter" placeholder="Hledat soubor…">
            <div class="toolbar-sep"></div>
            <button class="btn btn-primary" onclick="togglePanel('panel-create')">+ Nový soubor</button>
            <button class="btn btn-secondary" onclick="togglePanel('panel-upload')">⬆ Nahrát soubor</button>
        </div>

        <div class="panel" id="panel-create">
            <form method="POST" action="/admin/create" style="display:contents">
                <input type="text" name="file" placeholder="clanek nebo sekce/clanek">
                <button type="submit" class="btn btn-primary">Vytvořit</button>
                <p class="panel-hint">Přípona <code>.md</code> se doplní automaticky. Lomítkem vytvoříš podsložku, např. <code>cestopisy/pariz</code>.</p>
            </form>
        </div>

        <div class="panel" id="panel-upload">
            <form method="POST" action="/admin/upload" enctype="multipart/form-data" style="display:contents">
                <input type="file" name="file" accept=".md" required>
                <button type="submit" class="btn btn-primary">Nahrát</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Soubor</th>
                    <th>Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $file): ?>
                <tr>
                    <td>
                        <code><?= htmlspecialchars($file) ?></code>
                        <?php if (str_ends_with($file, '.private.md')): ?>
                            <span class="badge-private">private</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="admin-actions">
                            <a href="/admin/edit?file=<?= urlencode($file) ?>" class="btn btn-primary">Upravit</a>
                            <a href="/download/<?= htmlspecialchars(preg_replace('/\.(?:private\.)?md$/', '', $file)) ?>" class="btn btn-secondary">Stáhnout</a>
                            <button class="btn btn-secondary" onclick="renameFile(<?= htmlspecialchars(json_encode($file)) ?>)">Přejmenovat</button>
                            <button class="btn btn-danger" onclick="deleteFile(<?= htmlspecialchars(json_encode($file)) ?>)">Smazat</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <form id="rename-form" method="POST" action="/admin/rename" style="display:none">
        <input type="hidden" name="from" id="rename-from">
        <input type="hidden" name="to"   id="rename-to">
    </form>
    <form id="delete-form" method="POST" action="/admin/delete" style="display:none">
        <input type="hidden" name="file" id="delete-file">
    </form>

    <script>
    document.getElementById('filter').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = row.querySelector('code').textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    function togglePanel(id) {
        const panel = document.getElementById(id);
        const allPanels = document.querySelectorAll('.panel');
        allPanels.forEach(p => { if (p.id !== id) p.classList.remove('open'); });
        panel.classList.toggle('open');
        if (panel.classList.contains('open')) {
            panel.querySelector('input')?.focus();
        }
    }

    function renameFile(current) {
        const next = prompt('Nový název souboru (relativní cesta):', current);
        if (next && next !== current) {
            document.getElementById('rename-from').value = current;
            document.getElementById('rename-to').value   = next.endsWith('.md') ? next : next + '.md';
            document.getElementById('rename-form').submit();
        }
    }

    function deleteFile(file) {
        if (confirm('Opravdu chcete smazat soubor „' + file + '"?\n\nTato akce je nevratná.')) {
            document.getElementById('delete-file').value = file;
            document.getElementById('delete-form').submit();
        }
    }
    </script>
</body>
</html>
