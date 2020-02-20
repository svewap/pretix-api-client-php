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
 * @see https://docs.pretix.eu/en/latest/api/resources/subevents.html
 */
class SubEvent extends AbstractEntity
{
    protected static $fields = [
        'id' => 'integer', // Internal ID of the sub-event
        'name' => 'multi-lingual string', // The sub-event’s full name
        'event' => 'string', // The slug of the parent event
        'active' => 'boolean', // If true, the sub-event ticket shop is publicly available.
        'is_public' => 'boolean', // If true, the sub-event ticket shop is publicly shown in lists.
        'date_from' => 'datetime', // The sub-event’s start date
        'date_to' => 'datetime', // The sub-event’s end date (or null)
        'date_admission' => 'datetime', // The sub-event’s admission date (or null)
        'presale_start' => 'datetime', // The sub-date at which the ticket shop opens (or null)
        'presale_end' => 'datetime', // The sub-date at which the ticket shop closes (or null)
        'location' => 'multi-lingual string', // The sub-event location (or null)
        'geo_lat' => 'float', // Latitude of the location (or null)
        'geo_lon' => 'float', // Longitude of the location (or null)
        'item_price_overrides' => [ // List of items for which this sub-event overrides the default price
            'type' => 'list of objects',
            'object' => [
                'item' => 'integer', // The internal item ID
                'price' => 'money (string)', // The price or null for the default price
            ],
        ],
        'variation_price_overrides' => [ // List of variations for which this sub-event overrides the default price
            'type' => 'list of objects',
            'object' => [
                'variation' => 'integer', // The internal variation ID
                'price' => 'money (string)', // The price or null for the default price
            ],
        ],
        'meta_data' => 'object', // Values set for organizer-specific meta data parameters.
        'seating_plan' => 'integer', // If reserved seating is in use, the ID of a seating plan. Otherwise null.
        'seat_category_mapping' => 'object', // An object mapping categories of the seating plan (strings) to items in the event (integers or null).
    ];
}
