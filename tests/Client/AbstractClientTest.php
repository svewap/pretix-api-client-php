<?php

/*
 * This file is part of itk-dev/pretix-api-client-php.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace ItkDev\Pretix\Client;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
abstract class AbstractClientTest extends TestCase
{
    /** @var string */
    private $url;

    /** @var string */
    private $organizer;

    /** @var string */
    private $apiToken;

    /** @var \ItkDev\Pretix\Client */
    private $client;

    protected function setUp(): void
    {
        $this->url = getenv('PRETIX_URL');
        $this->organizer = getenv('PRETIX_ORGANIZER');
        $this->apiToken = getenv('PRETIX_API_TOKEN');
    }

    protected function client(): Client
    {
        if (null === $this->client) {
            $this->client = new Client($this->url, $this->organizer, $this->apiToken);
        }

        return $this->client;
    }
}
