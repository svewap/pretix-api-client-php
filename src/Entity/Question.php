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
 * @see https://docs.pretix.eu/en/latest/api/resources/questions.html
 */
class Question extends AbstractEntity
{
    protected static $fields = [
        'id' => 'integer', // Internal ID of the question
        'question' => 'multi-lingual string', // The field label shown to the customer
        'help_text' => 'multi-lingual string', // The help text shown to the customer
        'type' => 'string', // The expected type of answer.
        'required' => 'boolean', // If true, the question needs to be filled out.
        'position' => 'integer', // An integer, used for sorting
        'items' => 'list of integers', // List of item IDs this question is assigned to.
        'identifier' => 'string', // An arbitrary string that can be used for matching with other sources.
        'ask_during_checkin' => 'boolean', // If true, this question will not be asked while buying the ticket, but will show up when redeeming the ticket instead.
        'hidden' => 'boolean', // If true, the question will only be shown in the backend.
        'print_on_invoice' => 'boolean', // If true, the question will only be shown on invoices.
        'options' => [
            // In case of question type C or M, this lists the available objects. Only writable during creation, use separate endpoint to modify this later.
            'type' => 'list of objects',
            'object' => [
                'id' => 'integer', // Internal ID of the option
                'position' => 'integer', // An integer, used for sorting
                'identifier' => 'string', // An arbitrary string that can be used for matching with other sources.
                'answer' => 'multi-lingual string', // The displayed value of this option
            ],
        ],
        'dependency_question' => 'integer', // Internal ID of a different question. The current question will only be shown if the question given in this attribute is set to the value given in dependency_value. This cannot be combined with ask_during_checkin.
        'dependency_values' => 'list of strings', // If dependency_question is set to a boolean question, this should be ["True"] or ["False"]. Otherwise, it should be a list of identifier values of question options.
        'dependency_value' => 'string', // An old version of dependency_values that only allows for one value. Deprecated.
    ];
}
