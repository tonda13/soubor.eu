<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — <?= htmlspecialchars($file) ?></title>
    <link rel="stylesheet" href="/assets/everest.css">
    <link rel="stylesheet" href="/assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/github.min.css" media="(prefers-color-scheme: light)">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/github-dark.min.css" media="(prefers-color-scheme: dark)">
    <style>
        html, body {
            height: 100%;
            overflow: hidden;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        body > header { flex-shrink: 0; }

        body > form {
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }

        .editor-layout {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
        }
        .editor-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.6rem var(--pad-inline);
            border-bottom: 1px solid var(--border);
            gap: 1rem;
            flex-shrink: 0;
        }
        .editor-toolbar code { font-size: var(--fs-sm); color: var(--text-muted); }
        .editor-toolbar .actions { display: flex; gap: 0.5rem; }

        .editor-panes {
            display: flex;
            flex: 1;
            overflow: hidden;
        }
        textarea {
            flex: 1;
            font-family: var(--font-mono);
            font-size: var(--fs-sm);
            line-height: 1.6;
            padding: 1rem 1.25rem;
            border: none;
            border-right: 1px solid var(--border);
            background: var(--bg);
            color: var(--text);
            resize: none;
            outline: none;
            overflow-y: auto;
            transition: background 0.15s;
        }
        textarea.drag-over {
            background: color-mix(in srgb, var(--accent-gradient-from) 8%, var(--bg));
            border-right-color: var(--accent-gradient-from);
        }
        .preview-pane {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 1.5rem;
            font-size: var(--fs-base);
        }
        .preview-pane pre { background: transparent; padding: 0; }
        .preview-pane pre code.hljs { border-radius: 6px; }

        /* Panel obrázků */
        .images-pane {
            width: 220px;
            flex-shrink: 0;
            border-left: 1px solid var(--border);
            display: none;
            flex-direction: column;
            overflow: hidden;
        }
        .images-pane.open { display: flex; }
        .images-pane-header {
            padding: 0.6rem 0.75rem;
            font-size: var(--fs-sm);
            font-weight: 600;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        .images-grid {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.4rem;
            align-content: start;
        }
        .images-grid .img-wrap {
            position: relative;
        }
        .images-grid img {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            border-radius: 3px;
            border: 1px solid var(--border);
            cursor: pointer;
            display: block;
            transition: opacity 0.15s;
        }
        .images-grid img:hover { opacity: 0.75; }
        .img-delete {
            position: absolute;
            top: 3px;
            right: 3px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--accent-alt);
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 11px;
            line-height: 18px;
            text-align: center;
            padding: 0;
            display: none;
        }
        .img-wrap:hover .img-delete { display: block; }
        .images-upload {
            padding: 0.5rem;
            border-top: 1px solid var(--border);
            flex-shrink: 0;
        }
        .images-upload label {
            display: block;
            text-align: center;
            padding: 0.4rem;
            background: var(--code-bg);
            border: 1px dashed var(--border);
            border-radius: 4px;
            font-size: var(--fs-sm);
            cursor: pointer;
            color: var(--text-muted);
        }
        .images-upload label:hover { border-color: var(--accent-gradient-from); color: var(--accent-gradient-from); }
        .images-upload input[type="file"] { display: none; }
        .images-empty {
            grid-column: 1/-1;
            text-align: center;
            font-size: var(--fs-sm);
            color: var(--text-muted);
            padding: 1rem 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.75rem;
            border-radius: 4px;
            font-size: var(--fs-sm);
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-family: var(--font-body);
        }
        .btn-primary   { background: var(--accent-gradient-from); color: #fff; }
        .btn-secondary { background: var(--code-bg); color: var(--text); border: 1px solid var(--border); }
        .btn-toggle    { background: transparent; color: var(--text-muted); border: 1px solid var(--border); }
        .btn-toggle.active { color: var(--accent-gradient-from); border-color: var(--accent-gradient-from); }
    </style>
</head>
<body>
    <header class="page-header" style="padding: 1rem 0;">
        <div class="page-content">
            <h1 style="font-size: var(--fs-lg);">Úprava souboru</h1>
        </div>
    </header>

    <form method="POST" action="/admin/edit" id="edit-form">
        <input type="hidden" name="file" value="<?= htmlspecialchars($file) ?>">
        <div class="editor-layout">
            <div class="editor-toolbar">
                <code><?= htmlspecialchars($file) ?></code>
                <div class="actions">
                    <button type="button" class="btn btn-toggle" id="toggle-images" title="Obrázky">🖼 Obrázky</button>
                    <button type="button" class="btn btn-toggle active" id="toggle-preview">Náhled</button>
                    <button type="button" class="btn btn-secondary" id="btn-rename">Přejmenovat</button>
                    <a href="/admin" class="btn btn-secondary">← Zpět</a>
                    <button type="submit" class="btn btn-primary">Uložit</button>
                </div>
            </div>
            <div class="editor-panes">
                <textarea name="content" id="editor" spellcheck="false"><?= htmlspecialchars($content) ?></textarea>
                <div class="preview-pane" id="preview"></div>
                <div class="images-pane" id="images-pane">
                    <div class="images-pane-header">
                        <span>Obrázky</span>
                        <span style="font-size:0.7rem; color:var(--text-muted);">klik = vložit</span>
                    </div>
                    <div class="images-upload">
                        <label>
                            + Vybrat soubor
                            <input type="file" id="image-upload-input" accept="image/*">
                        </label>
                        <p style="font-size:var(--fs-sm); color:var(--text-muted); margin-top:0.4rem; margin-bottom:0;">nebo přetáhni / Ctrl+V</p>
                    </div>
                    <div class="images-grid" id="images-grid">
                        <span class="images-empty">Načítání…</span>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/marked/12.0.0/marked.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js"></script>
    <script>
    marked.setOptions({ breaks: true, gfm: true });

    const editor      = document.getElementById('editor');
    const preview     = document.getElementById('preview');
    const imagesPane  = document.getElementById('images-pane');
    const imagesGrid  = document.getElementById('images-grid');
    const imageDir    = <?= json_encode(preg_replace('/\.(?:private\.)?md$/', '', $file)) ?>;
    let previewVisible = true;
    let saved = true;

    // Preview
    function renderPreview() {
        preview.innerHTML = marked.parse(editor.value);
        preview.querySelectorAll('pre code').forEach(b => hljs.highlightElement(b));
    }
    editor.addEventListener('input', () => { saved = false; renderPreview(); });
    renderPreview();

    document.getElementById('edit-form').addEventListener('submit', () => { saved = true; });
    window.addEventListener('beforeunload', e => { if (!saved) { e.preventDefault(); e.returnValue = ''; } });

    document.getElementById('toggle-preview').addEventListener('click', function () {
        previewVisible = !previewVisible;
        preview.style.display = previewVisible ? '' : 'none';
        this.classList.toggle('active', previewVisible);
    });

    // Upload obrázku a vložení Markdown syntaxe
    function uploadImage(file) {
        if (!file || !file.type.startsWith('image/')) return;
        const placeholder = `![nahrávám ${file.name}…]()`;
        insertAtCursor(placeholder);
        const start = editor.value.lastIndexOf(placeholder);

        const fd = new FormData();
        fd.append('image', file);
        fd.append('dir', imageDir);
        fetch('/admin/images/upload', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.url) {
                    editor.value = editor.value.replace(placeholder, `![${file.name}](${data.url})`);
                    saved = false;
                    renderPreview();
                    if (imagesPane.classList.contains('open')) loadImages();
                } else {
                    editor.value = editor.value.replace(placeholder, '');
                    alert('Upload selhal: ' + (data.error ?? 'neznámá chyba'));
                }
            })
            .catch(err => {
                editor.value = editor.value.replace(placeholder, '');
                alert('Upload selhal: ' + err);
            });
    }

    // Drag & drop
    editor.addEventListener('dragover', e => { e.preventDefault(); editor.classList.add('drag-over'); });
    editor.addEventListener('dragleave', () => editor.classList.remove('drag-over'));
    editor.addEventListener('drop', e => {
        e.preventDefault();
        editor.classList.remove('drag-over');
        const file = e.dataTransfer.files[0];
        if (file) uploadImage(file);
    });

    // Paste
    editor.addEventListener('paste', e => {
        const file = Array.from(e.clipboardData.items)
            .find(i => i.kind === 'file' && i.type.startsWith('image/'))
            ?.asFile();
        if (file) { e.preventDefault(); uploadImage(file); }
    });

    // Přejmenování
    document.getElementById('btn-rename').addEventListener('click', () => {
        const next = prompt('Nový název souboru:', <?= json_encode($file) ?>);
        if (!next || next === <?= json_encode($file) ?>) return;
        const newFile = next.endsWith('.md') ? next : next + '.md';
        const fd = new FormData();
        fd.append('from', <?= json_encode($file) ?>);
        fd.append('to', newFile);
        fetch('/admin/rename', { method: 'POST', body: fd, redirect: 'manual' })
            .then(() => {
                window.location.href = '/admin/edit?file=' + encodeURIComponent(newFile);
            });
    });

    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault(); saved = true;
            document.getElementById('edit-form').submit();
        }
    });

    // Obrázky
    function insertAtCursor(text) {
        const start = editor.selectionStart;
        const end   = editor.selectionEnd;
        editor.value = editor.value.slice(0, start) + text + editor.value.slice(end);
        editor.selectionStart = editor.selectionEnd = start + text.length;
        editor.focus();
        saved = false;
        renderPreview();
    }

    function renderImages(images) {
        if (!images.length) {
            imagesGrid.innerHTML = '<span class="images-empty">Žádné obrázky</span>';
            return;
        }
        imagesGrid.innerHTML = images.map(img =>
            `<div class="img-wrap">
                <img src="${img.url}" title="${img.name}" alt="${img.name}">
                <button type="button" class="img-delete" title="Smazat" data-url="${img.url}">✕</button>
            </div>`
        ).join('');
        imagesGrid.querySelectorAll('img').forEach(img => {
            img.addEventListener('click', () => insertAtCursor(`![${img.title}](${img.getAttribute('src')})`));
        });
        imagesGrid.querySelectorAll('.img-delete').forEach(btn => {
            btn.addEventListener('click', e => {
                e.stopPropagation();
                if (!confirm('Smazat obrázek ' + btn.dataset.url + '?')) return;
                fetch('/admin/images/delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'url=' + encodeURIComponent(btn.dataset.url),
                }).then(r => r.json()).then(data => {
                    if (data.ok) loadImages();
                    else alert('Chyba: ' + data.error);
                });
            });
        });
    }

    function loadImages() {
        fetch(`/admin/images?dir=${encodeURIComponent(imageDir)}`)
            .then(r => r.json())
            .then(renderImages);
    }

    document.getElementById('toggle-images').addEventListener('click', function () {
        const open = imagesPane.classList.toggle('open');
        this.classList.toggle('active', open);
        if (open) loadImages();
    });

    document.getElementById('image-upload-input').addEventListener('change', function () {
        if (!this.files.length) return;
        uploadImage(this.files[0]);
        this.value = '';
    });
    </script>
</body>
</html>
