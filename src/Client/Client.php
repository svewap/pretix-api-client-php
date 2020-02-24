<?php

/*
 * This file is part of itk-dev/pretix-api-client-php.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace ItkDev\Pretix\Client;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Response as HttpResponse;
use ItkDev\Pretix\Entity\AbstractEntity;
use ItkDev\Pretix\Entity\Event;
use ItkDev\Pretix\Entity\Item;
use ItkDev\Pretix\Entity\Organizer;
use ItkDev\Pretix\Entity\Quota;
use ItkDev\Pretix\Exception\ClientException;
use ItkDev\Pretix\Exception\InvalidArgumentException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Pretix client.
 *
 * @see https://docs.pretix.eu/en/latest/api/resources/index.html
 */
class Client
{
    /** @var array */
    private $options;

    /**
     * The pretix url.
     *
     * @var string
     */
    private $url;

    /**
     * The pretix organizer slug.
     *
     * @var string
     */
    private $organizer;

    /**
     * The pretix api token.
     *
     * @var string
     */
    private $apiToken;

    /** @var HttpClient */
    private $client;

    /**
     * Constructor.
     *
     * @param string $url           The pretix api url
     * @param string $organizerSlug The organizer slug
     * @param string $apiToken      The api token
     */
    public function __construct(array $options)
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setDefaults([
                'url' => 'https://pretix.eu',
            ])
            ->setRequired(['url', 'organizer', 'api_token']);

        $this->options = $resolver->resolve($options);
        $this->url = trim($this->options['url'], '/');
        $this->organizer = $this->options['organizer'];
        $this->apiToken = $this->options['api_token'];
    }

    /**
     * Set organizer slug.
     *
     * @param string $organizer The organizer slug
     *
     * @return \ItkDev\Pretix\Client
     */
    public function setOrganizer($organizer): self
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * Get organizers.
     *
     * @return Collection<Organizer>
     */
    public function getOrganizers(): Collection
    {
        return $this->getCollection(Organizer::class, 'organizers/');
    }

    /**
     * Get organizer.
     *
     * @param mixed $organizer
     */
    public function getOrganizer($organizer): Organizer
    {
        return $this->getEntity(Organizer::class,
            'organizers/'.$organizer.'/');
    }

    /**
     * Get Events.
     *
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->getCollection(Event::class,
            'organizers/'.$this->organizer.'/events/');
    }

    /**
     * Get event.
     *
     * @param object|string $event
     *                             The event or event slug
     *
     * @return object
     *                The result
     */
    public function getEvent($event)
    {
        $eventSlug = $this->getSlug($event);

        return $this->getEntity(Event::class,
            'organizers/'.$this->organizer.'/events/'.$eventSlug.'/');
    }

    /**
     * Create event.
     *
     * @param array $data The data
     */
    public function createEvent(array $data): Event
    {
        return $this->postEntity(Event::class,
            'organizers/'.$this->organizer.'/events/', [
                'json' => $data,
            ]);
    }

    /**
     * Clone event.
     *
     * @param object|string $event
     *                             The event or event slug
     * @param array         $data
     *                             The data
     */
    public function cloneEvent($event, array $data): Event
    {
        $eventSlug = $this->getSlug($event);

        return $this->postEntity(
            Event::class,
            'organizers/'.$this->organizer.'/events/'.$eventSlug.'/clone/',
            [
                'data' => $data,
            ]
        );
    }

    /**
     * Update event.
     *
     * @param object|string $event
     *                             The event or event slug
     * @param array         $data
     *                             The data
     */
    public function updateEvent($event, array $data): Event
    {
        $eventSlug = $this->getSlug($event);

        return $this->patchEntity(
            Event::class,
            'organizers/'.$this->organizer.'/events/'.$eventSlug.'/',
            [
                'data' => $data,
            ]
        );
    }

    /**
     * Delete event.
     *
     * @param object|string $event
     *                             The event or event slug
     *
     * @return object
     *                The result
     */
    public function deleteEvent($event)
    {
        $eventSlug = $this->getSlug($event);

        return $this->delete('organizers/'.$this->organizer.'/events/'.$eventSlug.'/');
    }

    /**
     * Get items (products).
     *
     * @param object|string $event
     *                             The event or event slug
     *
     * @return object
     *                The result
     */
    public function getItems($event)
    {
        $eventSlug = $this->getSlug($event);

        return $this->getCollection(Item::class,
            'organizers/'.$this->organizer.'/events/'.$eventSlug.'/items/');
    }

    /**
     * Get quotas.
     *
     * @param object|string $event
     *                               The event or event slug
     * @param array         $options
     *                               The options
     *
     * @return object
     *                The result
     */
    public function getQuotas($event, array $options = [])
    {
        $eventSlug = $this->getSlug($event);

        return $this->getCollection(
            Quota::class,
            'organizers/'.$this->organizer.'/events/'.$eventSlug.'/quotas/',
            $options
        );
    }

    /**
     * Create quota.
     *
     * @param object|string $event
     *                             The event or event slug
     * @param array         $data
     *                             The data
     *
     * @return object
     *                The result
     */
    public function createQuota($event, array $data)
    {
        $eventSlug = $this->getSlug($event);

        $response = $this->post(
            'organizers/'.$this->organizer.'/events/'.$eventSlug.'/quotas/',
            ['data' => $data]
        );

        return $this->loadEntity(Quota::class, $response);
    }

    /**
     * Update quota.
     *
     * @param object|string $event
     *                             The event or event slug
     * @param int|object    $quota
     *                             The quota or quota id
     * @param array         $data
     *                             The data
     *
     * @return object
     *                The result
     */
    public function updateQuota($event, $quota, array $data)
    {
        $eventSlug = $this->getSlug($event);
        $quotaId = $this->getId($quota);

        return $this->patch(
            'organizers/'.$this->organizer.'/events/'.$eventSlug.'/quotas/'.$quotaId.'/',
            ['data' => $data]
        );
    }

    /**
     * Get quota availability.
     *
     * @param object|string $event
     *                             The event or event slug
     * @param int|object    $quota
     *                             The quota or quota id
     *
     * @return object
     *                The result
     */
    public function getQuotaAvailability($event, $quota)
    {
        $eventSlug = $this->getSlug($event);
        $quotaId = $this->getId($quota);

        return $this->get('organizers/'.$this->organizer.'/events/'.$eventSlug.'/quotas/'.$quotaId.'/availability/');
    }

    /**
     * Get sub-events (event series dates).
     *
     * @param object|string $event
     *                             The event or event slug
     *
     * @return object
     *                The result
     */
    public function getSubEvents($event)
    {
        $eventSlug = $this->getSlug($event);

        return $this->get('organizers/'.$this->organizer.'/events/'.$eventSlug.'/subevents/');
    }

    /**
     * Create sub-event.
     *
     * @param object|string $event
     *                             The event or event slug
     * @param array         $data
     *                             The data
     *
     * @return object
     *                The result
     */
    public function createSubEvent($event, array $data)
    {
        $eventSlug = $this->getSlug($event);

        return $this->post(
            'organizers/'.$this->organizer.'/events/'.$eventSlug.'/subevents/',
            [
                'data' => $data,
            ]
        );
    }

    /**
     * Update sub-event.
     *
     * @param object|string $event
     *                                The event or event slug
     * @param object|string $subEvent
     *                                The sub-event or sub-event slug
     * @param array         $data
     *                                The data
     *
     * @return object
     *                The result
     */
    public function updateSubEvent($event, $subEvent, array $data)
    {
        $eventSlug = $this->getSlug($event);
        $subEventId = $this->getId($subEvent);

        return $this->patch(
            'organizers/'.$this->organizer.'/events/'.$eventSlug.'/subevents/'.$subEventId.'/',
            [
                'data' => $data,
            ]
        );
    }

    /**
     * Delete sub-event.
     *
     * @param object|string $event
     *                                The event or event slug
     * @param object|string $subEvent
     *                                The sub-event or sub-event slug
     *
     * @return object
     *                The result
     */
    public function deleteSubEvent($event, $subEvent)
    {
        $eventSlug = $this->getSlug($event);
        $subEventId = $this->getId($subEvent);

        return $this->delete('organizers/'.$this->organizer.'/events/'.$eventSlug.'/subevents/'.$subEventId.'/');
    }

    /**
     * Get webhooks.
     */
    public function getWebhooks(): Response
    {
        return $this->get('organizers/'.$this->organizer.'/webhooks/');
    }

    /**
     * Get webhook.
     *
     * @param string $id
     *                   The id
     *
     * @return \ItkDev\Pretix\Response
     */
    public function getWebhook($id): Response
    {
        return $this->get('organizers/'.$this->organizer.'/webhooks/'.$id);
    }

    /**
     * Create webhook.
     *
     * @param array $data
     *                    The data
     *
     * @return \ItkDev\Pretix\Response
     */
    public function createWebhook(array $data): Response
    {
        return $this->post('organizers/'.$this->organizer.'/webhooks/', [
            'data' => $data,
        ]);
    }

    /**
     * Update webhook.
     *
     * @param object $webhook
     *                        The webhook
     * @param array  $data
     *                        The data
     *
     * @return \ItkDev\Pretix\Response
     */
    public function updateWebhook($webhook, array $data): Response
    {
        return $this->patch(
            'organizers/'.$this->organizer.'/webhooks/'.$webhook->id.'/',
            [
                'data' => $data,
            ]
        );
    }

    /**
     * Get order.
     *
     * @param object|string $organizer
     *                                 The organizer
     * @param object|string $event
     *                                 The event
     * @param string        $code
     *                                 The code
     *
     * @return \ItkDev\Pretix\Response
     */
    public function getOrder($organizer, $event, $code): Response
    {
        $organizerSlug = $this->getSlug($organizer);
        $eventSlug = $this->getSlug($event);

        return $this->get('organizers/'.$organizerSlug.'/events/'.$eventSlug.'/orders/'.$code.'/');
    }

    /**
     * Get questions.
     *
     * @param object|string $organizer
     *                                 The organizer
     * @param object|string $event
     *                                 The event
     *
     * @return \ItkDev\Pretix\Response
     */
    public function getQuestions($organizer, $event): Response
    {
        $organizerSlug = $this->getSlug($organizer);
        $eventSlug = $this->getSlug($event);

        return $this->get('organizers/'.$organizerSlug.'/events/'.$eventSlug.'/questions/');
    }

    private function getEntity($class, $path, array $options = [])
    {
        return $this->requestEntity($class, 'GET', $path, $options);
    }

    private function getCollection($class, $path, array $options = [])
    {
        return $this->requestCollection($class, 'GET', $path, $options);
    }

    private function postEntity($class, $path, array $options = [])
    {
        return $this->requestEntity($class, 'POST', $path, $options);
    }

    /**
     * GET request.
     *
     * @param string $path
     *                        The path
     * @param array  $options
     *                        The options
     *
     * @return \ItkDev\Pretix\Response
     */
    private function get($path, array $options = []): HttpResponse
    {
        return $this->request('GET', $path, $options);
    }

    /**
     * POST request.
     *
     * @param string $path
     *                        The path
     * @param array  $options
     *                        The options
     *
     * @return \ItkDev\Pretix\Response
     */
    private function post($path, array $options = []): HttpResponse
    {
        return $this->request('POST', $path, $options);
    }

    /**
     * PATCH request.
     *
     * @param string $path
     *                        The path
     * @param array  $options
     *                        The options
     *
     * @return \ItkDev\Pretix\Response
     */
    private function patch($path, array $options = []): HttpResponse
    {
        return $this->request('PATCH', $path, $options);
    }

    /**
     * DELETE request.
     *
     * @param string $path
     *                     The path
     *
     * @return \ItkDev\Pretix\Response
     */
    private function delete($path): HttpResponse
    {
        return $this->request('DELETE', $path);
    }

    /**
     * Request.
     *
     * @param string $method
     *                        The method
     * @param string $path
     *                        The path
     * @param array  $options
     *                        The options
     *
     * @return \ItkDev\Pretix\Response The result
     *                                 The result
     */
    private function request(
        $method,
        $path,
        array $options = []
    ): HttpResponse {
        if (null === $this->client) {
            $this->client = new HttpClient([
                'base_uri' => $this->url,
            ]);
        }
        $headers = [
            'accept' => 'application/json, text/javascript',
            'authorization' => 'Token '.$this->apiToken,
            'content-type' => 'application/json',
        ];

        $options += [
            'headers' => $headers,
        ];

        $uri = 'api/v1/'.$path;

        try {
            return $this->client->request($method, $uri, $options);
        } catch (\Exception $exception) {
            throw new ClientException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private function requestEntity(
        string $class,
        string $method,
        string $path,
        array $options = []
    ): AbstractEntity {
        if (!is_subclass_of($class, AbstractEntity::class)) {
            throw new \RuntimeException(sprintf('Class %s must be an %s', $class, AbstractEntity::class));
        }
        $response = $this->request($method, $path, $options);

        return $this->loadEntity($class, $response);
    }

    private function requestCollection(
        string $class,
        string $method,
        string $path,
        array $options = []
    ): Collection {
        if (!is_subclass_of($class, AbstractEntity::class)) {
            throw new \RuntimeException(sprintf('Class %s must be an %s', $class, AbstractEntity::class));
        }
        $response = $this->request($method, $path, $options);

        return $this->loadCollection($class, $response);
    }

    private function loadCollection(
        string $class,
        HttpResponse $response
    ): Collection {
        $data = json_decode((string) $response->getBody(), true);

        return new ArrayCollection(array_map(static function ($data) use ($class
        ) {
            return new $class($data);
        }, $data['results']));
    }

    /**
     * Get event slug.
     *
     * @param \ItkDev\Pretix\Item|string $event The event or event slug
     *
     * @return string The event slug
     */
    private function getSlug($event): string
    {
        if ($event instanceof Event) {
            $event = $event->getSlug();
        } elseif (!is_string($event)) {
            throw new InvalidArgumentException('String expected');
        }

        return $event;
    }

    /**
     * Get id.
     *
     * @param int|Item $item The object or object id
     *
     * @return int The id
     */
    private function getId($item): int
    {
        if ($item instanceof AbstractEntity) {
            $item = $item->getId();
        } elseif (!is_int($item)) {
            throw new InvalidArgumentException('Integer expected');
        }

        return $item;
    }

    private function loadEntity(string $class, HttpResponse $response)
    {
        $data = json_decode((string) $response->getBody(), true);

        return new $class($data);
    }
}
