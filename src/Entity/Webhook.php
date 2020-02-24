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
 * @see https://docs.pretix.eu/en/latest/api/resources/webhooks.html
 */
class Webhook extends AbstractEntity
{
    protected static $fields = [
        'id' => 'integer',
        // Internal ID of the webhook
        'enabled' => 'boolean',
        // If false, this webhook will not receive any notifications
        'target_url' => 'string',
        // The URL to call
        'all_events' => 'boolean',
        // If true, this webhook will receive notifications on all events of this organizer
        'limit_events' => 'list of strings',
        // If all_events is false, this is a list of event slugs this webhook is active for
        'action_types' => 'list of strings',
        // A list of action type filters that limit the notifications sent to this webhook. See below for valid values
    ];
}
