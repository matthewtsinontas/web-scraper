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
        // Get email param and set up helper class
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
        $console->writeLine("Found domain name: {$domain}");

        // Scrape homepage for links
        $urls = $helper->getPageUrlsFromDomain($domain);

        // Extract mailtos:
        $mailtos = $helper->scrapeMailtos($urls);

        // Extract and format links:
        $links = $helper->dissectLinks($urls, $domain);
        $linkCount = count($links);
        $estimatedTime = sprintf(
          "Estimated completion time: %s - %s seconds",
          ceil($linkCount * 0.3),
          ceil($linkCount * 0.4)
        );
        $console->writeLine(sprintf("Found %d pages: searching for data", $linkCount));
        $console->writeLine($estimatedTime, Color::YELLOW);

        // Get data from pages
        $data = $helper->traverseLinks($links);

        // Dont like this... want to find a nicer way to iterate over the data
        $console->writeLine("");
        foreach ($data as $key => $value) {
            $console->writeLine(sprintf("Found %d %s:", count($value), $key));
            foreach ($value as $i => $data) {
                $console->writeLine(sprintf("  %d) %s", $i + 1, $data));
            }
            $console->writeLine("");
        }

        // End
        $console->writeLine("Thank you for using the web scraper!", Color::GREEN);
    }
}
