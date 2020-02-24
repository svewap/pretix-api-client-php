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
 * @see https://docs.pretix.eu/en/latest/api/resources/items.html
 */
class Item extends AbstractEntity
{
    protected static $fields = [
        'id' => 'integer',
        // Internal ID of the item
        'name' => 'multi-lingual string',
        // The item’s visible name
        'internal_name' => 'string',
        // An optional name that is only used in the backend
        'default_price' => 'money (string)',
        // The item price that is applied if the price is not overwritten by variations or other options.
        'category' => 'integer',
        // The ID of the category this item belongs to (or null).
        'active' => 'boolean',
        // If false, the item is hidden from all public lists and will not be sold.
        'description' => 'multi-lingual string',
        // A public description of the item. May contain Markdown syntax or can be null.
        'free_price' => 'boolean',
        // If true, customers can change the price at which they buy the product (however, the price can’t be set lower than the price defined by default_price or otherwise).
        'tax_rate' => 'decimal (string)',
        // The VAT rate to be applied for this item (read-only, set through tax_rule).
        'tax_rule' => 'integer',
        // The internal ID of the applied tax rule (or null).
        'admission' => 'boolean',
        // true for items that grant admission to the event (such as primary tickets) and false for others (such as add-ons or merchandise).
        'position' => 'integer',
        // An integer, used for sorting
        'picture' => 'string',
        // A product picture to be displayed in the shop (read-only, can be null).
        'sales_channels' => 'list of strings',
        // Sales channels this product is available on, such as "web" or "resellers". Defaults to ["web"].
        'available_from' => 'datetime',
        // The first date time at which this item can be bought (or null).
        'available_until' => 'datetime',
        // The last date time at which this item can be bought (or null).
        'hidden_if_available' => 'integer',
        // The internal ID of a quota object, or null. If set, this item won’t be shown publicly as long as this quota is available.
        'require_voucher' => 'boolean',
        // If true, this item can only be bought using a voucher that is specifically assigned to this item.
        'hide_without_voucher' => 'boolean',
        // If true, this item is only shown during the voucher redemption process, but not in the normal shop frontend.
        'allow_cancel' => 'boolean',
        // If false, customers cannot cancel orders containing this item.
        'min_per_order' => 'integer',
        // This product can only be bought if it is included at least this many times in the order (or null for no limitation).
        'max_per_order' => 'integer',
        // This product can only be bought if it is included at most this many times in the order (or null for no limitation).
        'checkin_attention' => 'boolean',
        // If true, the check-in app should show a warning that this ticket requires special attention if such a product is being scanned.
        'original_price' => 'money (string)',
        // An original price, shown for comparison, not used for price calculations (or null).
        'require_approval' => 'boolean',
        // If true, orders with this product will need to be approved by the event organizer before they can be paid.
        'require_bundling' => 'boolean',
        // If true, this item is only available as part of bundles.
        'generate_tickets' => 'boolean',
        // If false, tickets are never generated for this product, regardless of other settings. If true, tickets are generated even if this is a non-admission or add-on product, regardless of event settings. If this is null, regular ticketing rules apply.
        'allow_waitinglist' => 'boolean',
        // If false, no waiting list will be shown for this product when it is sold out.
        'issue_giftcard' => 'boolean',
        // If true, buying this product will yield a gift card.
        'show_quota_left' => 'boolean',
        // Publicly show how many tickets are still available. If this is null, the event default is used.
        'has_variations' => 'boolean',
        // Shows whether or not this item has variations.
        'variations' => [ // A list with one object for each variation of this item. Can be empty. Only writable during creation, use separate endpoint to modify this later.
            'type' => 'list of objects',
            'object' => [
                'id' => 'integer',
                // Internal ID of the variation
                'value' => 'multi-lingual string',
                // The “name” of the variation
                'default_price' => 'money (string)',
                // The price set directly for this variation or null
                'price' => 'money (string)',
                // The price used for this variation. This is either the same as default_price if that value is set or equal to the item’s default_price.
                'original_price' => 'money (string)',
                // An original price, shown for comparison, not used for price calculations (or null).
                'active' => 'boolean',
                // If false, this variation will not be sold or shown.
                'description' => 'multi-lingual string',
                // A public description of the variation. May contain Markdown syntax or can be null.
                'position' => 'integer',
                // An integer, used for sorting
            ],
        ],
        'addons' => [  // Definition of add-ons that can be chosen for this item. Only writable during creation, use separate endpoint to modify this later.
            'type' => 'list of objects',
            'object' => [
                'addon_category' => 'integer',
                // Internal ID of the item category the add-on can be chosen from.
                'min_count' => 'integer',
                // The minimal number of add-ons that need to be chosen.
                'max_count' => 'integer',
                // The maximal number of add-ons that can be chosen.
                'position' => 'integer',
                // An integer, used for sorting
                'price_included' => 'boolean',
                // Adding this add-on to the item is free
            ],
        ],
        'bundles' => [ // Definition of bundles that are included in this item. Only writable during creation, use separate endpoint to modify this later.
            'type' => 'list of objects',
            'object' => [
                'bundled_item' => 'integer',
                // Internal ID of the item that is included.
                'bundled_variation' => 'integer',
                // Internal ID of the variation of the item (or null).
                'count' => 'integer',
                // Number of items included
                'designated_price' => 'money (string)',
                // Designated price of the bundled product. This will be used to split the price of the base item e.g. for mixed taxation. This is not added to the price.
            ],
        ],
    ];
}
