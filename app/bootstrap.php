<?php

use App\Models\User;
use Slim\Http\{
    Environment,
    Uri
};
use Twig\Extra\Intl\IntlExtension;

session_start();

date_default_timezone_set('America/Sao_Paulo');

require __DIR__ . '/./../vendor/autoload.php';
require __DIR__ . '/../env.php';

$app = new Slim\App([
    'settings' => [
        'displayErrorDetails' => getenv('DISPLAY_ERROS_DETAILS'),
        'db' => [
            'driver' => getenv('HCODE_STORE_DRIVER'),
            'host' => getenv('HCODE_STORE_HOST'),
            'database' => getenv('HCODE_STORE_DBNAME'),
            'username' => getenv('HCODE_STORE_USER'),
            'password' => getenv('HCODE_STORE_PASSWORD'),
            'charset'   => getenv('HCODE_STORE_CHARSET'),
            'collation' => getenv('HCODE_STORE_COLLATION'),
            'prefix'    => getenv('HCODE_STORE_PREFIX'),
        ]
    ]
]);

/**
 * Container Pimple
 * Instância o container.
 */
$container = $app->getContainer();

$capsule = new Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['upload_directory'] = "C:/xampp/htdocs/hcode-slim-3/public/images";

/**
 * Serviço de Logging em Arquivo
 */
$container['logger'] = function($container) {
    $logger = new Monolog\Logger('books-microservice');
    $logfile = __DIR__ . '/../logs/hcode.log';
    $stream = new Monolog\Handler\StreamHandler($logfile, Monolog\Logger::DEBUG);
    $fingersCrossed = new Monolog\Handler\FingersCrossedHandler(
        $stream, Monolog\Logger::INFO);
    $logger->pushHandler($fingersCrossed);

    return $logger;
};


$container['validator'] = function($container) {
    return new App\Validation\Validator;
};

$container['flash'] = function($container) {
    return new Slim\Flash\Messages;
};

$container['auth'] = function($container) {
    return new App\Auth\Auth($container);
};

$container['mail'] = function($container) {
    return new App\Models\Mail($container);
};

$container['view'] = function($container) {
    $view = new Slim\Views\Twig(__DIR__ . '/../resources/views', [
        'cache' => false,
    ]);
    $router = $container->get('router');
    $uri = Uri::createFromEnvironment(new Environment($_SERVER));

    $view->addExtension(new Slim\Views\TwigExtension($router, $uri));
    $view->addExtension(new IntlExtension());

    $view->getEnvironment()->addGlobal('flash', $container->flash);

    $user_name = User::find($_SESSION['user'])->deslogin;

    $view->getEnvironment()->addGlobal('user_name', $user_name );

    $view->getEnvironment()->addGlobal('auth', [
        'check' => $container->auth->check(),
        'user' => $container->auth->user()
    ]);

    return $view;
};


$container['AuthController'] = function($container) {
    return new App\Controllers\AuthController($container);
};

/**
 * @param $container -> key =  'SiteController', value = function($container)
 */
$container['SiteController'] = function($container) {
    return new App\Controllers\SiteController($container);
};

$container['AdminController'] = function($container) {
    return new App\Controllers\AdminController($container);
};

$container['ProductController'] = function($container) {
    return new App\Controllers\ProductController($container);
};

$container['CategoryController'] = function($container) {
    return new App\Controllers\CategoryController($container);
};



require __DIR__ . '/commons.php';

getControllers($container, [
    'SiteController',
    'AuthController',
    'AdminController',
    'ProductController',
    'CategoryController']);

$app->add(new App\Middleware\DisplayInputErrorsMiddleware($container));

require __DIR__ . '/routes.php';