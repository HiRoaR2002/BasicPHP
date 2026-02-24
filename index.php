<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$builder = new ContainerBuilder();

$builder->addDefinitions([

    Mustache_Engine::class => function (): Mustache_Engine {
        return new Mustache_Engine([
            'loader' => new Mustache_Loader_FilesystemLoader(
                __DIR__ . '/templates',
                ['extension' => '.mustache']
            ),
            'escape' => fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'),
        ]);
    },

    HomeController::class => \DI\autowire(),
    AuthController::class => \DI\autowire(),
]);

$container = $builder->build();


AppFactory::setContainer($container);
$app = AppFactory::create();

$app->get('/', [HomeController::class, 'index']);

$app->get('/login',  [AuthController::class, 'showLogin']);
$app->post('/login', [AuthController::class, 'handleLogin']);
$app->get('/logout', [AuthController::class, 'logout']);

$app->get('/dashboard', [AuthController::class, 'dashboard']);

$app->run();
