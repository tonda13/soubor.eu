<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — nahradit soubor?</title>
    <link rel="stylesheet" href="/assets/everest.css">
    <link rel="stylesheet" href="/assets/style.css">
    <style>
        .confirm-box {
            max-width: 480px;
            margin: 3rem auto;
            padding: 0 var(--pad-inline);
        }
        .confirm-box p { margin-bottom: 1.5rem; }
        .confirm-actions { display: flex; gap: 0.75rem; }
        .btn {
            display: inline-block;
            padding: 0.45rem 1rem;
            border-radius: 4px;
            font-size: var(--fs-sm);
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-family: var(--font-body);
        }
        .btn-danger    { background: var(--accent-alt); color: #fff; }
        .btn-secondary { background: var(--code-bg); color: var(--text); border: 1px solid var(--border); }
    </style>
</head>
<body>
    <header class="page-header">
        <div class="page-content">
            <h1>Nahradit soubor?</h1>
        </div>
    </header>
    <div class="confirm-box">
        <p>Soubor <code><?= htmlspecialchars($filename) ?></code> již existuje. Chcete ho nahradit nahraným souborem?</p>
        <div class="confirm-actions">
            <form method="POST" action="/admin/upload/confirm">
                <input type="hidden" name="filename"  value="<?= htmlspecialchars($filename) ?>">
                <input type="hidden" name="temp_path" value="<?= htmlspecialchars($tempPath) ?>">
                <button type="submit" class="btn btn-danger">Ano, nahradit</button>
            </form>
            <a href="/admin" class="btn btn-secondary">Zrušit</a>
        </div>
    </div>
</body>
</html>
