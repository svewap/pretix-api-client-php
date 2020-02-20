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
 * @see https://docs.pretix.eu/en/latest/api/resources/orders.html
 */
class Order extends AbstractEntity
{
    protected static $fields = [
        'code' => 'string', // Order code
        'status' => 'string', // Order status, one of:
        'testmode' => 'boolean', // If true, this order was created when the event was in test mode. Only orders in test mode can be deleted.
        'secret' => 'string', // The secret contained in the link sent to the customer
        'email' => 'string', // The customer email address
        'locale' => 'string', // The locale used for communication with this customer
        'sales_channel' => 'string', // Channel this sale was created through, such as "web".
        'datetime' => 'datetime', // Time of order creation
        'expires' => 'datetime', // The order will expire, if it is still pending by this time
        'payment_date' => 'date', // DEPRECATED AND INACCURATE Date of payment receipt
        'payment_provider' => 'string', // DEPRECATED AND INACCURATE Payment provider used for this order
        'total' => 'money (string)', // Total value of this order
        'comment' => 'string', // Internal comment on this order
        'checkin_attention' => 'boolean', // If true, the check-in app should show a warning that this ticket requires special attention if a ticket of this order is scanned.
        'invoice_address' => [ // Invoice address information (can be null)
            'type' => 'object',
            'object' => [
                'last_modified' => 'datetime', // Last modification date of the address
                'company' => 'string', // Customer company name
                'is_business' => 'boolean', // Business or individual customers (always false for orders created before pretix 1.7, do not rely on it).
                'name' => 'string', // Customer name
                'name_parts' => 'object of strings', // Customer name decomposition
                'street' => 'string', // Customer street
                'zipcode' => 'string', // Customer ZIP code
                'city' => 'string', // Customer city
                'country' => 'string', // Customer country code
                'state' => 'string', // Customer state (ISO 3166-2 code). Only supported in AU, BR, CA, CN, MY, MX, and US.
                'internal_reference' => 'string', // Customer’s internal reference to be printed on the invoice
                'vat_id' => 'string', // Customer VAT ID
                'vat_id_validated' => 'string', // true, if the VAT ID has been validated against the EU VAT service and validation was successful. This only happens in rare cases.
            ],
        ],
        'positions' => 'list of objects', // List of order positions (see below). By default, only non-canceled positions are included.
        'fees' => [ // List of fees included in the order total. By default, only non-canceled fees are included.
            'type' => 'list of objects',
            'object' => [
                'fee_type' => 'string', // Type of fee (currently payment, passbook, other)
                'value' => 'money (string)', // Fee amount
                'description' => 'string', // Human-readable string with more details (can be empty)
                'internal_type' => 'string', // Internal string (i.e. ID of the payment provider), can be empty
                'tax_rate' => 'decimal (string)', // VAT rate applied for this fee
                'tax_value' => 'money (string)', // VAT included in this fee
                'tax_rule' => 'integer', // The ID of the used tax rule (or null)
                'canceled' => 'boolean', // Whether or not this fee has been canceled.
            ],
        ],
        'downloads' => [ // List of ticket download options for order-wise ticket downloading. This might be a multi-page PDF or a ZIP file of tickets for outputs that do not support multiple tickets natively. See also order position download options.
            'type' => 'list of objects',
            'object' => [
                'output' => 'string', // Ticket output provider (e.g. pdf, passbook)
                'url' => 'string', // Download URL
            ],
        ],
        'require_approval' => 'boolean', // If true and the order is pending, this order needs approval by an organizer before it can continue. If true and the order is canceled, this order has been denied by the event organizer.
        'url' => 'string', // The full URL to the order confirmation page
        'payments' => 'list of objects', // List of payment processes (see below)
        'refunds' => 'list of objects', // List of refund processes (see below)
        'last_modified' => 'datetime', // Last modification of this object
    ];
}
