<?php

declare(strict_types=1);

namespace Souboreu\Controller;

use League\CommonMark\GithubFlavoredMarkdownConverter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

class FileController
{
    public function __construct(
        private PhpRenderer $renderer,
        private GithubFlavoredMarkdownConverter $converter,
    ) {
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $path = $args['path'] ?? 'default';

        // Přesměruj /soubor.md → /soubor
        if (str_ends_with($path, '.md')) {
            $canonical = '/' . preg_replace('/\.md$/', '', $path);
            return $response->withHeader('Location', $canonical)->withStatus(301);
        }

        $base = __DIR__ . '/../../files/' . $path;
        $filePath = file_exists($base . '.md') ? $base . '.md' : $base . '.private.md';

        if (!file_exists($filePath)) {
            return $this->renderer->render($response->withStatus(404), '404.php', [
                'path' => $path,
            ]);
        }

        $markdown = file_get_contents($filePath);
        $content  = $this->converter->convert($markdown)->getContent();

        // Popis pro OG tagy — první odstavec zbavený HTML tagů
        $description = '';
        if (preg_match('/<p>(.*?)<\/p>/si', $content, $m)) {
            $description = mb_strimwidth(strip_tags($m[1]), 0, 200, '…');
        }

        return $this->renderer->render($response, 'file.php', [
            'title'        => $path,
            'content'      => $content,
            'description'  => $description,
            'lastModified' => filemtime($filePath),
        ]);
    }

    public function download(Request $request, Response $response, array $args): Response
    {
        $path = $args['path'] ?? '';
        $base = __DIR__ . '/../../files/' . $path;
        $filePath = file_exists($base . '.md') ? $base . '.md' : $base . '.private.md';

        if (!file_exists($filePath)) {
            return $response->withStatus(404);
        }

        $response->getBody()->write((string) file_get_contents($filePath));

        return $response
            ->withHeader('Content-Type', 'text/markdown; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="' . basename($filePath) . '"');
    }
}
