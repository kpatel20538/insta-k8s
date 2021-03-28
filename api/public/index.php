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
$builder->addDefinitions(__DIR__ . '/../src/definitions/common.php');
$container = $builder->build();
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->get('/api/sql', function (Request $request, Response $response) use ($container) {
    /** @var DBAL\Connection */
    $connection = $container->get(DBAL\Connection::class);
    
    $value = $connection->fetchOne("SELECT NOW()");
    
    $response->getBody()->write("Sql, $value");
    return $response;
});

$app->get('/api/redis', function (Request $request, Response $response) use ($container) {
    /** @var Predis\ClientInterface */
    $client = $container->get(Predis\ClientInterface::class);
    
    $value = $client->incr("counter");
    
    $response->getBody()->write("Redis, $value");
    return $response;
});

$app->get('/api/di', function (Request $request, Response $response) {    
    $value = glob(__DIR__ . '/../src/definitions/*.php');
    
    $response->getBody()->write(json_encode($value));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/health', function (Request $request, Response $response) {
    $response->getBody()->write("ok");
    return $response;
});

$app->run();
