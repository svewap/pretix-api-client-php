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
 * @see https://docs.pretix.eu/en/latest/api/resources/quotas.html#get--api-v1-organizers-(organizer)-events-(event)-quotas-(id)-availability-
 */
class QuotaAvailability extends AbstractEntity
{
    protected static $fields = [
        'available' => 'boolean',
        'available_number' => 'integer',
        'total_size' => 'integer',
        'pending_orders' => 'integer',
        'paid_orders' => 'integer',
        'cart_positions' => 'integer',
        'blocking_vouchers' => 'integer',
        'waiting_list' => 'integer',
    ];
}
