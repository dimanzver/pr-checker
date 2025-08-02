<?php

declare(strict_types=1);

use App\ConflictingPullRequestFinder;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$finder = ConflictingPullRequestFinder::createFromEnv();
$finder->findAndReport();
