<?php

/*
 * This file is part of itk-dev/pretix-api-client-php.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace ItkDev\Pretix\Entity;

use ItkDev\Pretix\Collections\EntityCollection;
use ItkDev\Pretix\Collections\EntityCollectionInterface;
use ItkDev\Pretix\Exception\InvalidArgumentException;

/**
 * @method int   getId()
 * @method array toArray()
 */
class AbstractEntity
{
    /** @var array */
    protected static $fields = [];

    /** @var array */
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Convert entity to array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_map(static function ($value) {
            return ($value instanceof AbstractEntity || $value instanceof EntityCollectionInterface) ? $value->toArray() : $value;
        }, $this->data);
    }

    public function __call($name, $arguments)
    {
        if (preg_match('/^get(?P<key>.+)$/', $name, $matches)) {
            $key = lcfirst($matches['key']);
            if (is_array($this->data) && array_key_exists($key, $this->data)) {
                return $this->getValue($key, $arguments);
            }
        }

        if (preg_match('/^(is|has).+$/', $name, $matches)) {
            $key = preg_replace_callback('/([A-Z])/', static function ($matches) {
                return '_'.strtolower($matches[1]);
            }, $matches[0]);
            if (is_array($this->data) && array_key_exists($key, $this->data)) {
                return $this->getValue($key, $arguments);
            }
        }

        throw new \Error(sprintf('Call to undefined method %s::%s()', static::class, $name));
    }

    protected function getValue($key, $arguments)
    {
        $value = $this->data[$key];
        $type = static::$fields[$key] ?? 'string';

        switch ($type) {
            case 'multi-lingual string':
                $value = $this->getLocalizedString($value,
                    $arguments[0] ?? null);
                break;
        }

        return $value;
    }

    private function getLocalizedString($value, $locale = null)
    {
        if (null !== $locale && !isset($value[$locale])) {
            throw new InvalidArgumentException(sprintf('Invalid locale: %s', $locale));
        }

        return null === $locale ? $value : $value[$locale];
    }

    protected function buildCollection($class, array $items)
    {
        return new EntityCollection(array_map(static function (array $data) use ($class) {
            return new $class($data);
        }, $items));
    }
}
