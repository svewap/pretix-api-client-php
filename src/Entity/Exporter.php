<?php

/*
 * This file is part of itk-dev/pretix-api-client-php.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace ItkDev\Pretix\Api\Entity;

/**
 * @see https://docs.pretix.eu/en/latest/api/resources/exporters.html
 *
 * @method string getIdentifier()
 * @method array  getInputParameters()
 */
class Exporter extends AbstractEntity
{
    public function getName()
    {
        return $this->getValue('verbose_name', []);
    }
}
