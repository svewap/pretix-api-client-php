<?php

/*
 * This file is part of itk-dev/pretix-api-client-php.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace ItkDev\Pretix\Entity;

/**
 * @see https://docs.pretix.eu/en/latest/api/resources/quotas.html
 */
class Quota extends AbstractEntity
{
    protected static $fields = [
        'id' => 'integer',
        // Internal ID of the quota
        'name' => 'string',
        // The internal name of the quota
        'size' => 'integer',
        // The size of the quota or null for unlimited
        'items' => 'list of integers',
        // List of item IDs this quota acts on.
        'variations' => 'list of integers',
        // List of item variation IDs this quota acts on.
        'subevent' => 'integer',
        // ID of the date inside an event series this quota belongs to (or null).
        'close_when_sold_out' => 'boolean',
        // If true, the quota will “close” as soon as it is sold out once. Even if tickets become available again, they will not be sold unless the quota is set to open again.
        'closed' => 'boolean',
        // Whether the quota is currently closed (see above field).
    ];
}
