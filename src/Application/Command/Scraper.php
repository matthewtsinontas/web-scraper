<?php
namespace Application\Command;

use Zend\Console\ColorInterface as Color;
use Zend\Console\Adapter\AdapterInterface;
use ZF\Console\Route;
use Application\Validator\Validator;

class Scraper
{
    public function __invoke(Route $route, AdapterInterface $console)
    {
        $email = $route->getMatchedParam('email');
        $validator = new Validator();

        if (!$validator->validateEmail($email)) {
          $console->writeLine('Error: Email is missing or invalid - Please check email spelling and try again', Color::RED);
          return;
        }

        $console->writeLine("Scrape: {$email}");
    }
}
