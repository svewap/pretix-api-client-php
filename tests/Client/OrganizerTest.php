<?php

/*
 * This file is part of itk-dev/pretix-api-client-php.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace ItkDev\Pretix\Client;

/**
 * @internal
 * @coversNothing
 */
class OrganizerTest extends AbstractClientTest
{
    public function testGetOrganizers()
    {
        $organizers = $this->client()->getOrganizers();

        $this->assertSame(1, $organizers->count());
        $this->assertCount(1, $organizers);
    }

    public function testGetOrganizer()
    {
        $organizer = $this->client()->getOrganizer('pretix-api-client');

        $this->assertSame('pretix-api-client', $organizer->getName());
    }
}
