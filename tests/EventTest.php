<?php

/**
 * This file is part of itk-dev/serviceplatformen.
 * (c) 2020 ITK Development
 * This source file is subject to the MIT license.
 */

namespace ItkDev\Pretix\Api;

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
        $numberOfEvents = $this->client()->getEvents()->count();

        $data = [
            'name' => [
                'da' => __METHOD__,
            ],
            'slug' => __FUNCTION__,
            'date_from' => (new DateTimeImmutable('2020-01-01'))->format(DateTimeImmutable::ATOM),
        ];
        $event = $this->client()->createEvent($data);

        $events = $this->client()->getEvents();

        $this->assertSame($numberOfEvents + 1, $events->count());
    }

    public function testGetEvent()
    {
        $data = [
            'name' => [
                'da' => __METHOD__,
            ],
            'slug' => __FUNCTION__,
            'date_from' => (new DateTimeImmutable('2020-01-01'))->format(DateTimeImmutable::ATOM),
        ];
        $event = $this->client()->createEvent($data);

        // Get the event.
        $event = $this->client()->getEvent($event);
        $this->assertSame($data['name'], $event->getName());
        $this->assertSame($data['name']['da'], $event->getName('da'));
    }
}
