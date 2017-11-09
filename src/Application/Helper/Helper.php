<?php
namespace Application\Helper;

use Goutte\Client;

class Helper
{
    public function validateEmail(string $email = ""): bool
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

    public function getEmailsFromText(string $text): array
    {
        $pattern = '/[a-z0-9_\-\+]+@[a-z0-9\-]+\.([a-z]{2,3})(?:\.[a-z]{2})?/i';
        $matches = [];
        preg_match_all($pattern, $text, $matches);
        return $matches[0];
    }

    public function getPhoneNumbersFromText(string $text): array
    {
        // Fetch all numbers longer than 2 digits
        $matches = [];
        $pattern = '/([0-9]{2,})\d+/';
        preg_match_all($pattern, $text, $matches);
        return array_unique($this->buildPhoneNumbersFromDigitArray($matches[0]));
    }

    public function buildPhoneNumbersFromDigitArray(array $digits): array
    {
        $possibleNumbers = [];
        $currentNumber = '';

        for ($i = 0; $i < count($digits); $i++) {
            $number = $digits[$i];

            // If not building a current number, check if the beginning of the number
            // if 0x with x being a digit other than 0 (01, 02, 03 etc.)
            if (empty($currentNumber)) {
                if ($number[0] != 0 || $number[1] == 0) {
                    continue;
                }
            }

            // Append number onto current build and check length
            $currentNumber .= $digits[$i];
            $currentLength = strlen($currentNumber);

            // If greater than 10, its either a formed number, or too long
            if ($currentLength > 10) {
                if ($currentLength === 11) {
                    $possibleNumbers[] = $currentNumber;
                }
                $currentNumber = '';
                continue;
            }
        }

        return $possibleNumbers;
    }

    public function flattenAndUniqueArrays(array $array): array
    {
        $flattenedArray = [];
        array_walk_recursive($array, function($a) use (&$flattenedArray) {
          $flattenedArray[] = $a;
        });
        return array_values(array_unique($flattenedArray));
    }

    public function traverseLinks(array $urls): array
    {
        $client = new Client();
        $emails = [];
        $phones = [];

        foreach ($urls as $url) {
          // Go to url then preg match emails from the page text, then return them
          $crawler = $client->request('GET', $url);
          $text = $crawler->filter('body')->text();

          // Drop emails into array
          $emails[] = $this->getEmailsFromText($text);

          // Fetch phone numbers
          $phones[] = $this->getPhoneNumbersFromText($text);
        }

        return [
          'emails' => $this->flattenAndUniqueArrays($emails),
          'phones' => $this->flattenAndUniqueArrays($phones)
        ];
    }
}
