<?php

use Doctrine\DBAL;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$builder = new DI\ContainerBuilder();
$builder->addDefinitions([
    LoggerInterface::class => function () {
        $logger = new Logger("root");
        $logger->pushHandler(new StreamHandler('php://stderr', Logger::ERROR));
        $logger->pushHandler(new StreamHandler('php://stdout'));
        return $logger;
    },

    'db.host' => 'database-service',
    'db.dbname' => DI\env('MYSQL_DATABASE'),
    'db.user' => DI\env('MYSQL_USER'),
    'db.password' => DI\env('MYSQL_PASSWORD'),
    'db.dsn' => DI\string('mysql://{db.user}:{db.password}@{db.host}/{db.dbname}'),
    DBAL\Connection::class => function (ContainerInterface $container) {
        return DBAL\DriverManager::getConnection(['url' => $container->get('db.dsn')]);
    },
]);
$container = $builder->build();
AppFactory::setContainer($container);
$app = AppFactory::create();

/** @var LoggerInterface */
$logger = $container->get(LoggerInterface::class);
$logger->info($container->get('db.dsn'));

$app->get('/api/{name}', function (Request $request, Response $response, array $args) use ($container) {
    /** @var DBAL\Connection */
    $connection = $container->get(DBAL\Connection::class);
    
    $value = $connection->fetchOne("SELECT NOW()");

    $response->getBody()->write("Hello, $value");
    return $response;
});

$app->get('/health', function (Request $request, Response $response) {
    $response->getBody()->write("ok");
    return $response;
});

$app->run();
