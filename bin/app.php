<?php
use Zend\Console\Console;
use ZF\Console\Application;

// Set directory
chdir(dirname(__DIR__));
include 'vendor/autoload.php';

// Set up application
$application = new Application(
    "Web Scraper",
    "1.0",
    include 'config/routes.php',
    Console::getInstance()
);

// Run application
$exit = $application->run();
exit($exit);
