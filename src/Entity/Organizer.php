<?php

/**
 * This file is part of itk-dev/serviceplatformen.
 * (c) 2020 ITK Development
 * This source file is subject to the MIT license.
 */

namespace ItkDev\Pretix\Api\Entity;

/**
 * @see https://docs.pretix.eu/en/latest/api/resources/organizers.html
 *
 * @method string getName()
 * @method string getSlug()
 */
class Organizer extends AbstractEntity
{
    protected static $fields = [
        // The organizer’s full name, i.e. the name of an organization or company.
        'name' => 'string',
        // A short form of the name, used e.g. in URLs.
        'slug' => 'string',
    ];
}
