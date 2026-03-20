<?php

declare(strict_types=1);

namespace Souboreu\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

class AdminController
{
    private string $filesDir;
    private string $passwordFile;

    public function __construct(private PhpRenderer $renderer)
    {
        $this->filesDir    = __DIR__ . '/../../files';
        $this->passwordFile = __DIR__ . '/../../data/password.hash';
    }

    public function loginForm(Request $request, Response $response): Response
    {
        if (!empty($_SESSION['admin'])) {
            return $response->withHeader('Location', '/admin')->withStatus(302);
        }

        return $this->renderer->render($response, 'admin/login.php', ['error' => null]);
    }

    public function login(Request $request, Response $response): Response
    {
        $body     = (array) $request->getParsedBody();
        $password = $body['password'] ?? '';

        if (!file_exists($this->passwordFile)) {
            return $this->renderer->render($response, 'admin/login.php', [
                'error' => 'Heslo není nastaveno. Spusťte bin/admin-password.',
            ]);
        }

        $hash = trim((string) file_get_contents($this->passwordFile));

        if (password_verify($password, $hash)) {
            session_regenerate_id(true);
            $_SESSION['admin'] = true;
            return $response->withHeader('Location', '/admin')->withStatus(302);
        }

        return $this->renderer->render($response->withStatus(401), 'admin/login.php', [
            'error' => 'Nesprávné heslo.',
        ]);
    }

    public function logout(Request $request, Response $response): Response
    {
        $_SESSION = [];
        session_destroy();
        return $response->withHeader('Location', '/admin/login')->withStatus(302);
    }

    public function list(Request $request, Response $response): Response
    {
        $saved = isset($request->getQueryParams()['saved']);
        return $this->renderer->render($response, 'admin/list.php', [
            'files' => $this->getFiles(),
            'saved' => $saved,
        ]);
    }

    public function editForm(Request $request, Response $response): Response
    {
        $file     = $request->getQueryParams()['file'] ?? '';
        $filePath = $this->resolvePath($file);

        if ($filePath === null || !file_exists($filePath)) {
            return $response->withStatus(404);
        }

        return $this->renderer->render($response, 'admin/edit.php', [
            'file'    => $file,
            'content' => (string) file_get_contents($filePath),
        ]);
    }

    public function save(Request $request, Response $response): Response
    {
        $body     = (array) $request->getParsedBody();
        $file     = $body['file'] ?? '';
        $content  = $body['content'] ?? '';
        $filePath = $this->resolvePath($file);

        if ($filePath === null) {
            return $response->withStatus(400);
        }

        file_put_contents($filePath, $content);

        return $response->withHeader('Location', '/admin?saved=1')->withStatus(302);
    }

    public function imageList(Request $request, Response $response): Response
    {
        $dir     = $request->getQueryParams()['dir'] ?? '';
        $dir     = implode('/', array_filter(explode('/', $dir), fn($p) => $p !== '..' && $p !== '.' && $p !== ''));
        $absDir  = __DIR__ . '/../../uploads/' . $dir;
        $images  = [];

        if (is_dir($absDir)) {
            foreach (new \FilesystemIterator($absDir, \FilesystemIterator::SKIP_DOTS) as $file) {
                if ($file->isFile() && in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                    $relPath = ($dir ? $dir . '/' : '') . $file->getFilename();
                    $images[] = ['url' => '/uploads/' . $relPath, 'name' => $file->getFilename()];
                }
            }
        }

        sort($images);
        $response->getBody()->write((string) json_encode($images));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function imageUpload(Request $request, Response $response): Response
    {
        $body     = (array) $request->getParsedBody();
        $dir      = $body['dir'] ?? '';
        $dir      = implode('/', array_filter(explode('/', $dir), fn($p) => $p !== '..' && $p !== '.' && $p !== ''));
        $uploaded = ($request->getUploadedFiles())['image'] ?? null;

        if ($uploaded === null) {
            $keys = implode(', ', array_keys($request->getUploadedFiles()));
            $response->getBody()->write(json_encode(['error' => 'Soubor nenalezen. Dostupné klíče: ' . ($keys ?: 'žádné')]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if ($uploaded->getError() !== UPLOAD_ERR_OK) {
            $message = match ($uploaded->getError()) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Soubor je příliš velký (max ' . ini_get('upload_max_filesize') . ').',
                UPLOAD_ERR_PARTIAL   => 'Soubor byl nahrán jen částečně, zkuste to znovu.',
                UPLOAD_ERR_NO_FILE   => 'Nebyl vybrán žádný soubor.',
                default              => 'Upload selhal (kód ' . $uploaded->getError() . ').',
            };
            $response->getBody()->write(json_encode(['error' => $message]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $ext      = strtolower(pathinfo($uploaded->getClientFilename(), PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

        if (!in_array($ext, $allowed)) {
            $response->getBody()->write(json_encode(['error' => 'Nepodporovaný formát.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $targetDir = __DIR__ . '/../../uploads/' . ($dir ? $dir . '/' : '');
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $filename = $uploaded->getClientFilename();
        $uploaded->moveTo($targetDir . $filename);

        $url = '/uploads/' . ($dir ? $dir . '/' : '') . $filename;
        $response->getBody()->write(json_encode(['url' => $url, 'name' => $filename]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function imageDelete(Request $request, Response $response): Response
    {
        $body = (array) $request->getParsedBody();
        $url  = $body['url'] ?? '';

        // Z URL /uploads/... vytáhneme relativní cestu
        if (!str_starts_with($url, '/uploads/')) {
            $response->getBody()->write(json_encode(['error' => 'Neplatná cesta.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $rel  = ltrim(substr($url, strlen('/uploads/')), '/');
        $parts = array_filter(explode('/', $rel), fn($p) => $p !== '..' && $p !== '.' && $p !== '');
        $path = __DIR__ . '/../../uploads/' . implode('/', $parts);

        if (!file_exists($path)) {
            $response->getBody()->write(json_encode(['error' => 'Soubor nenalezen.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        unlink($path);
        $response->getBody()->write(json_encode(['ok' => true]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function upload(Request $request, Response $response): Response
    {
        $uploaded = ($request->getUploadedFiles())['file'] ?? null;

        if ($uploaded === null || $uploaded->getError() !== UPLOAD_ERR_OK) {
            return $response->withHeader('Location', '/admin')->withStatus(302);
        }

        $filename = $uploaded->getClientFilename();
        if (!str_ends_with($filename, '.md')) {
            $filename .= '.md';
        }

        $targetPath = $this->resolvePath($filename);
        if ($targetPath === null) {
            return $response->withStatus(400);
        }

        $tempPath = sys_get_temp_dir() . '/soubor_upload_' . uniqid() . '.md';
        $uploaded->moveTo($tempPath);

        if (file_exists($targetPath)) {
            return $this->renderer->render($response, 'admin/upload-confirm.php', [
                'filename' => $filename,
                'tempPath' => $tempPath,
            ]);
        }

        rename($tempPath, $targetPath);
        return $response->withHeader('Location', '/admin?saved=1')->withStatus(302);
    }

    public function uploadConfirm(Request $request, Response $response): Response
    {
        $body     = (array) $request->getParsedBody();
        $filename = $body['filename'] ?? '';
        $tempPath = $body['temp_path'] ?? '';

        if (!str_starts_with($tempPath, sys_get_temp_dir() . '/soubor_upload_')) {
            return $response->withStatus(400);
        }

        $targetPath = $this->resolvePath($filename);
        if ($targetPath === null || !file_exists($tempPath)) {
            return $response->withStatus(400);
        }

        rename($tempPath, $targetPath);
        return $response->withHeader('Location', '/admin?saved=1')->withStatus(302);
    }

    public function create(Request $request, Response $response): Response
    {
        $body = (array) $request->getParsedBody();
        $file = $body['file'] ?? '';

        if (!str_ends_with($file, '.md')) {
            $file .= '.md';
        }

        $filePath = $this->resolvePath($file);

        if ($filePath === null) {
            return $response->withStatus(400);
        }

        if (file_exists($filePath)) {
            return $response
                ->withHeader('Location', '/admin/edit?file=' . urlencode($file))
                ->withStatus(302);
        }

        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($filePath, '# ' . pathinfo($file, PATHINFO_FILENAME) . "\n");

        return $response
            ->withHeader('Location', '/admin/edit?file=' . urlencode($file))
            ->withStatus(302);
    }

    public function rename(Request $request, Response $response): Response
    {
        $body     = (array) $request->getParsedBody();
        $from     = $body['from'] ?? '';
        $to       = $body['to'] ?? '';
        $fromPath = $this->resolvePath($from);
        $toPath   = $this->resolvePath($to);

        if ($fromPath === null || $toPath === null || !file_exists($fromPath)) {
            return $response->withStatus(400);
        }

        $toDir = dirname($toPath);
        if (!is_dir($toDir)) {
            mkdir($toDir, 0755, true);
        }

        rename($fromPath, $toPath);

        return $response->withHeader('Location', '/admin')->withStatus(302);
    }

    public function delete(Request $request, Response $response): Response
    {
        $body     = (array) $request->getParsedBody();
        $file     = $body['file'] ?? '';
        $filePath = $this->resolvePath($file);

        if ($filePath === null || !file_exists($filePath)) {
            return $response->withStatus(404);
        }

        unlink($filePath);

        return $response->withHeader('Location', '/admin')->withStatus(302);
    }

    private function resolvePath(string $path): ?string
    {
        if (!str_ends_with($path, '.md')) {
            return null;
        }

        // Strip ../ and other traversal attempts
        $parts = array_filter(
            explode('/', str_replace('\\', '/', $path)),
            fn($p) => $p !== '..' && $p !== '.' && $p !== ''
        );

        if (empty($parts)) {
            return null;
        }

        return $this->filesDir . '/' . implode('/', $parts);
    }

    private function getFiles(): array
    {
        $files    = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->filesDir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (!str_ends_with($file->getFilename(), '.md')) {
                continue;
            }
            $files[] = ltrim(str_replace($this->filesDir, '', $file->getPathname()), '/');
        }

        sort($files);
        return $files;
    }
}
