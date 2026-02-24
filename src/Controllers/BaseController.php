<?php

namespace App\Controllers;

use Mustache_Engine;
use Psr\Http\Message\ResponseInterface as Response;

abstract class BaseController
{
    protected Mustache_Engine $mustache;

    public function __construct(Mustache_Engine $mustache)
    {
        $this->mustache = $mustache;
    }

    /**
     * Render a named template wrapped inside the shared layout.
     *
     * @param Response $response
     * @param string   $template  Template filename (without .mustache)
     * @param array    $data      Variables available inside the child template
     * @param array    $layout    Extra variables available in the layout (e.g. page_title)
     */
    protected function render(
        Response $response,
        string $template,
        array $data = [],
        array $layout = []
    ): Response {
        $content     = json_decode(file_get_contents(__DIR__ . '/../../data/content.json'), true);
        $isLoggedIn  = isset($_SESSION['user_id']);

        $flashSuccess = $_SESSION['flash_success'] ?? null;
        $flashError   = $_SESSION['flash_error']   ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $body = $this->mustache->loadTemplate($template)->render($data);

        $layoutVars = array_merge([
            'site_name'     => $content['site_name'],
            'page_title'    => $layout['page_title'] ?? ucfirst($template),
            'footer_text'   => $content['footer']['text'],
            'is_logged_in'  => $isLoggedIn,
            'flash_success' => $flashSuccess,
            'flash_error'   => $flashError,
            'body'          => $body,
        ], $layout);

        $html = $this->mustache->loadTemplate('layout')->render($layoutVars);

        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }
}
