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
        $base = __DIR__ . '/../../files/' . $path;
        $filePath = file_exists($base . '.md') ? $base . '.md' : $base . '.private.md';

        if (!file_exists($filePath)) {
            return $this->renderer->render($response->withStatus(404), '404.php', [
                'path' => $path,
            ]);
        }

        $markdown = file_get_contents($filePath);
        $content = $this->converter->convert($markdown)->getContent();

        return $this->renderer->render($response, 'file.php', [
            'title' => $path,
            'content' => $content,
        ]);
    }
}
