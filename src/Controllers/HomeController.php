<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeController extends BaseController
{
    public function index(Request $request, Response $response): Response
    {
        $content = json_decode(
            file_get_contents(__DIR__ . '/../../data/content.json'),
            true
        );

        $data = [
            'hero_title'    => $content['hero']['title'],
            'hero_subtitle' => $content['hero']['subtitle'],
            'hero_cta'      => $content['hero']['cta_text'],
            'features'      => $content['features'],
        ];

        return $this->render($response, 'home', $data, ['page_title' => 'Home']);
    }
}
