<?php

session_start();

require_once 'vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Twig\Node\Expression\NameExpression;

// create a log channel
$log = new Logger('main');
$log->pushHandler(new StreamHandler('logs/everything.log', Logger::DEBUG));
$log->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));

$log->pushProcessor(function ($record) {
    // $record['extra']['user'] = isset($_SESSION['user']) ? $_SESSION['user']['username'] : '=anonymous=';
    $record['extra']['ip'] = $_SERVER['REMOTE_ADDR'];
    return $record;
});
if (strpos($_SERVER['HTTP_HOST'], "fsd01.ca") !== false) {
    DB::$dbName = 'cp5016_dating';
    DB::$user = 'cp5016_dating';
    DB::$password = 'TTJYrGR.g#.X';
}else{
    DB::$dbName = 'day06slimauctions';
    DB::$user = 'day06slimauctions';
    DB::$password = '.(S.Mo8p6cYuty5F';
    DB::$host = 'localhost';
    DB::$port = 3333;

    // DB::$dbName = 'dating';
    // DB::$user = 'dating';
    // DB::$password = '.2u0)]h6EiMSzVbz';
    // DB::$host = 'localhost';
    // DB::$port = 3306;
}

// Name:cp5016_dating
// UserName:cp5016_dating
// Password:"TTJYrGR.g#.X"

DB::$error_handler = 'db_error_handler'; // runs on mysql query errors
DB::$nonsql_error_handler = 'db_error_handler'; // runs on library errors (bad syntax, etc)

function db_error_handler($params) {
    global $log, $container;
    $log->error("Database error: " . $params['error']);
    if (isset($params['query'])) {
        $log->error("SQL query: " . $params['query']);
    }
    // this was tricky to find - getting access to twig rendering directly, without PHP Slim
    http_response_code(500); // internal server error
    $twig = $container['view']->getEnvironment();
    // die($twig->render('error_internal.html.twig'));
    // Note: the above trick may also be useful to render a template into an email body
    //header("Location: /internalerror"); // another possibility, not my favourite
}


// Create and configure Slim app
$config = ['settings' => [
    'addContentLengthHeader' => false,
    'displayErrorDetails' => true
]];
$app = new \Slim\App($config);

// Fetch DI Container
$container = $app->getContainer();

// Register Twig View helper
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig(dirname(__FILE__) . '/templates', [
        'cache' => dirname(__FILE__) . '/tmplcache',
        'debug' => true, // This line should enable debug mode
    ]);
    // add a value to ALL twig templates
    $view->getEnvironment()->addGlobal('test1','VALUE');
    $view->getEnvironment()->addGlobal('clientIP', $_SERVER['REMOTE_ADDR']);
    $view->getEnvironment()->addGlobal('authUser', @$_SESSION['authUser']);
    // Instantiate and add Slim specific extension
    $router = $c->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
    return $view;
};

