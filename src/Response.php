<?php

/*
 * This file is part of itk-dev/pretix-api-client-php.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace ItkDev\Pretix;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

class Response
{
    /** @var \Psr\Http\Message\ResponseInterface */
    protected $response;

    /** @var array */
    protected $data;

    public function __construct(HttpResponseInterface $response = null)
    {
        $this->response = $response;
    }

    public static function create(HttpResponseInterface $response)
    {
        if (self::isErrorStatusCode($response->getStatusCode())) {
            return new Error($response);
        }

        $data = self::getResponseData($response);

        return array_key_exists('results', $data) ? new Collection($response) : new Item($response);
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function isError(): bool
    {
        return self::isErrorStatusCode($this->getStatusCode());
    }

    public function get(string $name)
    {
        $data = $this->getData();

        return $data[$name] ?? null;
    }

    public function has(string $name): bool
    {
        $data = $this->getData();

        return is_array($data) && array_key_exists($name, $data);
    }

    protected function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    private function getData(): array
    {
        if (null === $this->data && null !== $this->response) {
            $this->setData(self::getResponseData($this->response));
        }

        return $this->data;
    }

    private static function isErrorStatusCode($statusCode): bool
    {
        return $statusCode < 200 || 299 < $statusCode;
    }

    private static function getResponseData(HttpResponseInterface $response)
    {
        return json_decode((string) $response->getBody(), true);
    }
}
