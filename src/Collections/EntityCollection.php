<?php

/*
 * This file is part of itk-dev/pretix-api-client-php.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace ItkDev\Pretix\Api\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use ItkDev\Pretix\Api\Entity\AbstractEntity;

class EntityCollection extends ArrayCollection implements EntityCollectionInterface
{
    public function toArray(bool $recursive = true)
    {
        $elements = parent::toArray();

        if ($recursive) {
            foreach ($elements as &$element) {
                if ($element instanceof AbstractEntity || $element instanceof EntityCollectionInterface) {
                    $element = $element->toArray($recursive);
                }
            }
        }

        return $elements;
    }
}
