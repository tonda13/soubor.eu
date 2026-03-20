<?php

declare(strict_types=1);

namespace Souboreu\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

class IndexController
{
    public function __construct(
        private PhpRenderer $renderer,
    ) {
    }

    public function index(Request $request, Response $response): Response
    {
        $filesDir = __DIR__ . '/../../files';
        $files = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($filesDir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (!str_ends_with($file->getFilename(), '.md') || str_ends_with($file->getFilename(), '.private.md')) {
                continue;
            }

            $absolutePath = $file->getPathname();
            $relativePath = ltrim(str_replace($filesDir, '', $absolutePath), '/');
            $urlPath = preg_replace('/\.md$/', '', $relativePath);
            $title = $this->extractTitle($absolutePath) ?? $urlPath;

            $files[] = [
                'title' => $title,
                'url'   => '/' . $urlPath,
                'path'  => $relativePath,
            ];
        }

        usort($files, fn($a, $b) => strcmp($a['path'], $b['path']));

        return $this->renderer->render($response, 'index.php', ['files' => $files]);
    }

    private function extractTitle(string $filePath): ?string
    {
        $handle = fopen($filePath, 'r');
        while ($handle && ($line = fgets($handle)) !== false) {
            if (preg_match('/^#\s+(.+)/', trim($line), $matches)) {
                fclose($handle);
                return trim($matches[1]);
            }
        }
        if ($handle) fclose($handle);
        return null;
    }
}
