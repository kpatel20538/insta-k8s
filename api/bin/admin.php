<?php

use Symfony\Component\Console\Output\OutputInterface;

require __DIR__ . '/../vendor/autoload.php';

$app = new Silly\Application();

$app->command('greet [name] [--yell]', function ($name, $yell, OutputInterface $output) {
  if ($name) {
    $text = 'Hello, ' . $name;
  } else {
    $text = 'Hello';
  }

  if ($yell) {
    $text = strtoupper($text);
  }

  $output->writeln($text);
});

$app->run();
