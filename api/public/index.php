<?php

use Aws\S3\S3ClientInterface;
use Doctrine\DBAL;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$builder = new DI\ContainerBuilder();
foreach (glob(__DIR__ . '/../src/definitions/*.php') as $filename) {
    $builder->addDefinitions($filename);
}
$container = $builder->build();
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->get('/api/sql', function (Request $request, Response $response) use ($container) {
    /** @var DBAL\Connection */
    $connection = $container->get(DBAL\Connection::class);
    
    $value = $connection->fetchOne("SELECT NOW()");
    
    $response->getBody()->write($value);
    return $response;
});

$app->get('/api/redis', function (Request $request, Response $response) use ($container) {
    /** @var Predis\ClientInterface */
    $client = $container->get(Predis\ClientInterface::class);
    
    $value = $client->incr("counter");
    
    $response->getBody()->write($value);
    return $response;
});


$app->get('/api/minio/{key}', function (Request $request, Response $response, array $args) use ($container) {
    /** @var S3ClientInterface */
    $client = $container->get(S3ClientInterface::class);
    
    $putCommand = $client->getCommand('PutObject', [
        'Bucket' => 'uploads',
        'Key' => $args['key'],
    ]);
    $presignedRequest = $client->createPresignedRequest($putCommand, '+30 minutes');
    $value = (string) $presignedRequest->getUri();
    
    $response->getBody()->write("
        <input type='file' />
        <button>Upload</button>
        <script>
            document.querySelector('button')
                .addEventListener('click', () => {
                    fetch('$value', {
                        method: 'PUT',
                        body: document.querySelector('input').files[0] 
                    })
                });
        </script>
    ");
    return $response->withHeader("Content-Type", "text/html");
});


$app->get('/health', function (Request $request, Response $response) {
    $response->getBody()->write("ok");
    return $response;
});

$app->run();
