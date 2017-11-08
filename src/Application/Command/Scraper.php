<?php
namespace Application\Command;

use Zend\Console\ColorInterface as Color;
use Zend\Console\Adapter\AdapterInterface;
use ZF\Console\Route;
use Application\Helper\Helper;

class Scraper
{
    public function __invoke(Route $route, AdapterInterface $console)
    {
        $email = $route->getMatchedParam("email");
        $helper = new Helper();

        // Validate email, error if invalid
        if (!$helper->validateEmail($email)) {
            $console->writeLine(
                "Error: Email is missing or invalid - Please check email spelling and try again",
                Color::RED
            );
            return;
        }
        $console->writeLine("Scraping: {$email}");

        // Get domain and output
        $domain = $helper->getDomainFromEmail($email);
        $console->writeLine("Found domain name: ${domain}");

        // Scrape site for urls
        $urls = $helper->getPageUrlsFromDomain($domain);
        $console->writeLine(sprintf("Found %d domains - scraping...", count($urls)));
    }
}
