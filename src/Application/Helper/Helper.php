<?php
namespace Application\Helper;

use Goutte\Client;

class Helper
{
    public function validateEmail(string $email = ''): bool
    {
        if (!$email) {
            return false;
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function getDomainFromEmail(string $email): string
    {
        $splitEmail = explode("@", $email);
        return $splitEmail[1];
    }

    public function getPageUrlsFromDomain(string $domain): array
    {
        $client = new Client();

        $crawler = $client->request('GET', "http://{$domain}");

        return $crawler->filter('a')->each(function ($node) {
            return $node->attr('href');
        });
    }

    public function scrapeMailtos(array $links): array
    {
        $mailtos = array_filter($links, function($link) {
            return strpos($link, "mailto:") === 0;
        });

        return array_unique(array_map(function ($mailto) {
            return explode("mailto:", $mailto)[1];
        }, $mailtos));
    }

    public function dissectLinks(array $links, string $domain): array
    {
        $formatted = [];

        foreach ($links as $link) {
            // Relative link: /about-us, /docs
            if (strpos($link, '/') === 0) {
                $formatted[] = "http://{$domain}{$link}";
                continue;
            }
            // Static link: http://domain.com/whatever
            if (strpos($link, 'http') === 0 && strpos($link, "$domain/") !== false) {
                $formatted[] = $link;
                continue;
            }
        }

        return array_unique($formatted);
    }

    public function filterUrls(array $urls, string $domain): array
    {
        $domainOnly = array_filter($urls, function ($url) use ($domain) {
            return strpos($url, "{$domain}/") !== false;
        });

        return array_unique($domainOnly);
    }

    public function getEmailsFromPages(array $urls): array
    {
        $client = new Client();
        $allEmails = [];

        foreach ($urls as $url) {
          // Go to url then preg match emails from the page text, then return them
          $crawler = $client->request('GET', $url);
          $pattern = '/[a-z0-9_\-\+]+@[a-z0-9\-]+\.([a-z]{2,3})(?:\.[a-z]{2})?/i';
          $text = $crawler->filter('body')->text();
          $matches = [];
          preg_match_all($pattern, $text, $matches);
          $allEmails[] = $matches[0];
        }

        $flattenedArray = [];

        array_walk_recursive($allEmails, function($a) use (&$flattenedArray) {
          $flattenedArray[] = $a;
        });

        return $flattenedArray;
    }
}
