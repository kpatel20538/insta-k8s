<?php

use Aws\S3\S3Client;
use Aws\S3\S3ClientInterface;
use Doctrine\DBAL;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return [
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

  'session.host' => 'session-service',
  'session.port' => 6379,
  'session.dsn' => DI\string('redis://{session.host}:{session.port}'),
  Predis\ClientInterface::class => function(ContainerInterface $container) {
    return new Predis\Client($container->get('session.dsn'));
  },
  
  'storage.host' => 'storage-service',
  'storage.key' => DI\env('MINIO_ROOT_USER'),
  'storage.secret' => DI\env('MINIO_ROOT_PASSWORD'),
  S3ClientInterface::class => function (ContainerInterface $container) {
    return new S3Client([
      'version' => 'latest',
      'region' => 'us-east-1',
      'endpoint' => $container->get('storage.host'),
      'use_path_style_endpoint' => true,
      'credentials' => [
        'key' => $container->get('storage.key'),
        'secret' => $container->get('storage.secret'),
      ],
    ]);
  }
];
