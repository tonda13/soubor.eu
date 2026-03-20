<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — přihlášení</title>
    <link rel="stylesheet" href="/assets/everest.css">
    <link rel="stylesheet" href="/assets/style.css">
    <style>
        .login-form {
            max-width: 360px;
            margin: 4rem auto;
            padding: 0 var(--pad-inline);
        }
        .login-form input[type="password"] {
            width: 100%;
            padding: 0.6rem 0.8rem;
            font-size: var(--fs-base);
            border: 1px solid var(--border);
            border-radius: 4px;
            background: var(--bg);
            color: var(--text);
            margin: 0.5rem 0 1rem;
        }
        .login-form button {
            width: 100%;
            padding: 0.65rem;
            background: var(--accent-gradient);
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: var(--fs-base);
            font-family: var(--font-heading);
            cursor: pointer;
        }
        .login-error {
            color: var(--accent-alt);
            font-size: var(--fs-sm);
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <header class="page-header">
        <div class="page-content">
            <h1>Administrace</h1>
        </div>
    </header>
    <div class="login-form">
        <?php if ($error): ?>
            <p class="login-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="/admin/login">
            <label for="password">Heslo</label>
            <input type="password" id="password" name="password" autofocus>
            <button type="submit">Přihlásit se</button>
        </form>
    </div>
</body>
</html>
