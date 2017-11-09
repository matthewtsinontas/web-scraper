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
        $console->writeLine(sprintf("Found %d pages: searching for emails...", count($links)));

        // Get emails from pages
        $emails = $helper->getEmailsFromPages($links);

        // Merge mailto and email links, unique down to single array
        $finalList = array_values(array_unique(array_merge($mailtos, $emails)));

        $console->writeLine(sprintf("Found %d emails:", count($finalList)));
        // Output to console
        for ($i = 1; $i <= count($finalList); $i++) {
            $console->writeLine(sprintf("  %d) %s", $i, $finalList[$i - 1]));
        }
    }
}
