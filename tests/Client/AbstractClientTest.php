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
    /** @var array */
    private $clientOptions;

    /** @var \ItkDev\Pretix\Client */
    private $client;

    protected function setUp(): void
    {
        $this->clientOptions = [
            'url' => getenv('PRETIX_URL'),
            'organizer' => getenv('PRETIX_ORGANIZER'),
            'api_token' => getenv('PRETIX_API_TOKEN'),
        ];
    }

    protected function client(): Client
    {
        if (null === $this->client) {
            $this->client = new Client($this->clientOptions);
        }

        return $this->client;
    }
}
