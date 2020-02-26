<?php

/*
 * This file is part of itk-dev/pretix-api-client-php.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace ItkDev\Pretix\Api\Collections;

use Doctrine\Common\Collections\Collection;

interface EntityCollectionInterface extends Collection
{
    /**
     * {@inheritdoc}
     *
     * @return array
     *
     * @psalm-return array<TKey,T>
     */
    public function toArray(bool $recursive = true);
}
