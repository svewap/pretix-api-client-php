<?php

/*
 * This file is part of itk-dev/pretix-api-client-php.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace ItkDev\Pretix\Client;

use DateTimeImmutable;

/**
 * @internal
 * @coversNothing
 */
class EventTest extends AbstractClientTest
{
    public function testGetEvents()
    {
        $events = $this->client()->getEvents();

        $this->assertSame(0, $events->count());
        $this->assertCount(0, $events);
    }

    public function testCreateEvent()
    {
        $data = [
            'name' => [
                'da' => __METHOD__,
            ],
            'slug' => __FUNCTION__,
            'date_from' => (new DateTimeImmutable('2020-01-01'))->format(DateTimeImmutable::ATOM),
        ];
        $event = $this->client()->createEvent($data);

        $events = $this->client()->getEvents();

        $this->assertSame(1, $events->count());
        $this->assertCount(1, $events);

        $event = $events[0];
        $this->assertSame(['da' => __METHOD__], $event->getName());
        $this->assertSame(__METHOD__, $event->getName('da'));

        $event = $this->client()->getEvent($event);
    }
}
