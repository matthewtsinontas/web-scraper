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

        $crawler = $client->request('GET', "http://${domain}");

        $urls = $crawler->filter('a')->each(function ($node) {
            return $node->attr('href');
        });

        return $this->filterUrls($urls, $domain);
    }

    public function filterUrls(array $urls, string $domain): array
    {
        $domainOnly = array_filter($urls, function ($url) use ($domain) {
            return strpos($url, "{$domain}/") !== false;
        });

        return array_unique($domainOnly);
    }
}
