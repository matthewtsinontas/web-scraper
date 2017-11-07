<?php
namespace Application\Command;

use Zend\Console\ColorInterface as Color;
use Zend\Console\Adapter\AdapterInterface;
use ZF\Console\Route;

class Scraper
{
    public function __invoke(Route $route, AdapterInterface $console)
    {
        $email = $route->getMatchedParam('email');

        if (!$email) {
          $console->writeLine('Error: Missing email parameter', Color::RED);
          return;
        }

        $console->write("Scrape: {$email}");
    }
}
