<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController extends BaseController
{
    public function showLogin(Request $request, Response $response): Response
    {
        if (isset($_SESSION['user_id'])) {
            return $response
                ->withHeader('Location', '/dashboard')
                ->withStatus(302);
        }

        return $this->render($response, 'login', ['old_id' => ''], ['page_title' => 'Login']);
    }

    public function handleLogin(Request $request, Response $response): Response
    {
        $body = (array) $request->getParsedBody();
        $id   = trim($body['id'] ?? '');

        // Validate: id must not be empty
        if ($id === '') {
            $_SESSION['flash_error'] = 'User ID is required.';
            return $response
                ->withHeader('Location', '/login')
                ->withStatus(302);
        }

        $_SESSION['user_id'] = $id;

        $_SESSION['flash_success'] = "Welcome back, {$id}!";
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }


    public function dashboard(Request $request, Response $response): Response
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_error'] = 'Please log in first.';
            return $response
                ->withHeader('Location', '/login')
                ->withStatus(302);
        }

        $data = [
            'user_id'    => htmlspecialchars($_SESSION['user_id']),
            'session_id' => session_id(),
        ];

        return $this->render($response, 'dashboard', $data, ['page_title' => 'Dashboard']);
    }

    public function logout(Request $request, Response $response): Response
    {
        $userId = $_SESSION['user_id'] ?? 'User';

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();

        session_start();
        $_SESSION['flash_success'] = "Goodbye, {$userId}! You have been logged out.";

        return $response
            ->withHeader('Location', '/')
            ->withStatus(302);
    }
}
