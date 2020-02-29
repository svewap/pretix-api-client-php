<?php

/*
 * This file is part of itk-dev/pretix-api-client-php.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace ItkDev\Pretix\Api;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Response as HttpResponse;
use ItkDev\Pretix\Api\Collections\EntityCollection;
use ItkDev\Pretix\Api\Collections\EntityCollectionInterface;
use ItkDev\Pretix\Api\Entity\AbstractEntity;
use ItkDev\Pretix\Api\Entity\Event;
use ItkDev\Pretix\Api\Entity\Item;
use ItkDev\Pretix\Api\Entity\Order;
use ItkDev\Pretix\Api\Entity\Organizer;
use ItkDev\Pretix\Api\Entity\Quota;
use ItkDev\Pretix\Api\Entity\QuotaAvailability;
use ItkDev\Pretix\Api\Entity\SubEvent;
use ItkDev\Pretix\Api\Entity\Webhook;
use ItkDev\Pretix\Api\Exception\ClientException;
use ItkDev\Pretix\Api\Exception\InvalidArgumentException;
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
     * @return \ItkDev\Pretix\Api\Client
     */
    public function setOrganizer($organizer): self
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * Get api endpoints.
     */
    public function getApiEndpoints(): array
    {
        $response = $this->get('/');

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * Get organizers.
     *
     * @return EntityCollectionInterface<Organizer>
     */
    public function getOrganizers(): EntityCollectionInterface
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
     * @return EntityCollectionInterface|Event[]
     */
    public function getEvents(array $options = []): EntityCollectionInterface
    {
        return $this->getCollection(
            Event::class,
            'organizers/'.$this->organizer.'/events/',
            $options
        );
    }

    /**
     * Get event.
     *
     * @param object|string $event
     *                             The event or event slug
     *
     * @return Event
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
                'json' => $data,
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
                'json' => $data,
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
     * @return EntityCollectionInterface<Item>
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
     * @return EntityCollectionInterface<\ItkDev\Pretix\Api\Entity\Quota>
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

        return $this->postEntity(
            Quota::class,
            'organizers/'.$this->organizer.'/events/'.$eventSlug.'/quotas/',
            ['json' => $data]
        );
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

        return $this->patchEntity(
            Quota::class,
            'organizers/'.$this->organizer.'/events/'.$eventSlug.'/quotas/'.$quotaId.'/',
            ['json' => $data]
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
     * @return \ItkDev\Pretix\Api\Entity\QuotaAvailability
     */
    public function getQuotaAvailability($event, $quota)
    {
        $eventSlug = $this->getSlug($event);
        $quotaId = $this->getId($quota);

        return $this->getEntity(
            QuotaAvailability::class,
            'organizers/'.$this->organizer.'/events/'.$eventSlug.'/quotas/'.$quotaId.'/availability/'
        );
    }

    /**
     * Get sub-events (event series dates).
     *
     * @param object|string $event
     *                             The event or event slug
     *
     * @return EntityCollectionInterface<\ItkDev\Pretix\Api\Entity\SubEvent>
     */
    public function getSubEvents($event)
    {
        $eventSlug = $this->getSlug($event);

        return $this->getCollection(SubEvent::class, 'organizers/'.$this->organizer.'/events/'.$eventSlug.'/subevents/');
    }

    /**
     * Create sub-event.
     *
     * @param object|string $event
     *                             The event or event slug
     * @param array         $data
     *                             The data
     *
     * @return SubEvent
     */
    public function createSubEvent($event, array $data)
    {
        $eventSlug = $this->getSlug($event);

        return $this->postEntity(
            SubEvent::class,
            'organizers/'.$this->organizer.'/events/'.$eventSlug.'/subevents/',
            [
                'json' => $data,
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

        return $this->patchEntity(
            SubEvent::class,
            'organizers/'.$this->organizer.'/events/'.$eventSlug.'/subevents/'.$subEventId.'/',
            [
                'json' => $data,
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
     *
     * @return EntityCollectionInterface|Webhook[]
     */
    public function getWebhooks(): EntityCollectionInterface
    {
        return $this->getCollection(Webhook::class, 'organizers/'.$this->organizer.'/webhooks/');
    }

    /**
     * Get webhook.
     *
     * @param string $id
     *                   The id
     *
     * @return \ItkDev\Pretix\Api\Response
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
     * @return \ItkDev\Pretix\Api\Response
     */
    public function createWebhook(array $data): Webhook
    {
        return $this->postEntity(
            Webhook::class,
            'organizers/'.$this->organizer.'/webhooks/',
            [
                'json' => $data,
            ]
        );
    }

    /**
     * Update webhook.
     *
     * @param object $webhook
     *                        The webhook
     * @param array  $data
     *                        The data
     *
     * @return \ItkDev\Pretix\Api\Response
     */
    public function updateWebhook($webhook, array $data): Webhook
    {
        return $this->patchEntity(
            Webhook::class,
            'organizers/'.$this->organizer.'/webhooks/'.$webhook->id.'/',
            [
                'json' => $data,
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
     */
    public function getOrder($organizer, $event, $code): Order
    {
        $organizerSlug = $this->getSlug($organizer);
        $eventSlug = $this->getSlug($event);

        return $this->getEntity(Order::class, 'organizers/'.$organizerSlug.'/events/'.$eventSlug.'/orders/'.$code.'/');
    }

    /**
     * @see https://docs.pretix.eu/en/latest/api/resources/orders.html#get--api-v1-organizers-(organizer)-events-(event)-orderpositions-
     *
     * @param $event
     */
    public function getOrderPositions($event, array $query = []): EntityCollectionInterface
    {
        $eventSlug = $this->getSlug($event);

        return $this->getCollection(Order\Position::class, 'organizers/'.$this->organizer.'/events/'.$eventSlug.'/orderpositions/');
    }

    /**
     * Get questions.
     *
     * @param object|string $organizer
     *                                 The organizer
     * @param object|string $event
     *                                 The event
     *
     * @return \ItkDev\Pretix\Api\Response
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

    private function patchEntity($class, $path, array $options = [])
    {
        return $this->requestEntity($class, 'PATCH', $path, $options);
    }

    /**
     * GET request.
     *
     * @param string $path
     *                        The path
     * @param array  $options
     *                        The options
     *
     * @return \ItkDev\Pretix\Api\Response
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
     * @return \ItkDev\Pretix\Api\Response
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
     * @return \ItkDev\Pretix\Api\Response
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
     * @return \ItkDev\Pretix\Api\Response
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
     * @return \ItkDev\Pretix\Api\Response The result
     *                                     The result
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
    ): EntityCollectionInterface {
        if (!is_subclass_of($class, AbstractEntity::class)) {
            throw new \RuntimeException(sprintf('Class %s must be an %s', $class, AbstractEntity::class));
        }
        $response = $this->request($method, $path, $options);

        return $this->loadCollection($class, $response);
    }

    private function loadCollection(
        string $class,
        HttpResponse $response
    ): EntityCollectionInterface {
        $data = json_decode((string) $response->getBody(), true);

        return new EntityCollection(array_map(function ($data) use ($class
        ) {
            return $this->createEntity($class, $data);
        }, $data['results']));
    }

    /**
     * Get event slug.
     *
     * @param \ItkDev\Pretix\Api\Entity\Event|string $event The event or event slug
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

        return $this->createEntity($class, $data);
    }

    private function createEntity(string $class, array $data)
    {
        return new $class($data + [
            'pretix_options' => [
                'url' => $this->url,
                'organizer' => $this->organizer,
            ],
        ]);
    }
}
