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
 * @see https://docs.pretix.eu/en/latest/api/resources/events.html
 *
 * @method string getName(string $locale = null)
 * @method string getSlug()
 * @method bool   getTestmode()
 */
class Event extends AbstractEntity
{
    protected static $fields = [
        'name' => 'multi-lingual string', // The event’s full name
        'slug' => 'string', // A short form of the name, used e.g. in URLs.
        'live' => 'boolean', // If <code class="docutils literal notranslate"><span class="pre">true</span></code>, the event ticket shop is publicly available.
        'testmode' => 'boolean', // If <code class="docutils literal notranslate"><span class="pre">true</span></code>, the ticket shop is in test mode.
        'currency' => 'string', // The currency this event is handled in.
        'date_from' => 'datetime', // The event’s start date
        'date_to' => 'datetime', // The event’s end date (or <code class="docutils literal notranslate"><span class="pre">null</span></code>)
        'date_admission' => 'datetime', // The event’s admission date (or <code class="docutils literal notranslate"><span class="pre">null</span></code>)
        'is_public' => 'boolean', // If <code class="docutils literal notranslate"><span class="pre">true</span></code>, the event shows up in places like the organizer’s public list of events
        'presale_start' => 'datetime', // The date at which the ticket shop opens (or <code class="docutils literal notranslate"><span class="pre">null</span></code>)
        'presale_end' => 'datetime', // The date at which the ticket shop closes (or <code class="docutils literal notranslate"><span class="pre">null</span></code>)
        'location' => 'multi-lingual string', // The event location (or <code class="docutils literal notranslate"><span class="pre">null</span></code>)
        'geo_lat' => 'float', // Latitude of the location (or <code class="docutils literal notranslate"><span class="pre">null</span></code>)
        'geo_lon' => 'float', // Longitude of the location (or <code class="docutils literal notranslate"><span class="pre">null</span></code>)
        'has_subevents' => 'boolean', // <code class="docutils literal notranslate"><span class="pre">true</span></code> if the event series feature is active for this event. Cannot change after event is created.
        'meta_data' => 'object', // Values set for organizer-specific meta data parameters.
        'plugins' => 'list', // A list of package names of the enabled plugins for this event.
        'seating_plan' => 'integer', // If reserved seating is in use, the ID of a seating plan. Otherwise <code class="docutils literal notranslate"><span class="pre">null</span></code>.
        'seat_category_mapping' => 'object', // An object mapping categories of the seating plan (strings) to items in the event (integers or <code class="docutils literal notranslate"><span class="pre">null</span></code>).
        'timezone' => 'string', // Event timezone name
    ];
}
