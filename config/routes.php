<?php
return [
    [
        'name' => 'scrape',
        'command' => '<email>',
        'description' => 'Scrape the web for information based off an email address including phone number, address etc.',
        'short_description' => 'Scrape the web based off an email',
        'handler' => 'Application\Command\Scraper',
    ]
];
