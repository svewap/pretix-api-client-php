<?php

/*
 * This file is part of itk-dev/pretix-api-client-php.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace ItkDev\Pretix\Entity;

use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testToArray()
    {
        $order = new Order([
            'code' => 'order-1',
            'positions' => [
                [
                    'id' => 1,
                    'order' => 'order-1',
                ],
                [
                    'id' => 2,
                    'order' => 'order-1',
                ],
            ],
        ]);

        $expected = [
            'code' => 'order-1',
            'positions' => [
                [
                    'id' => 1,
                    'order' => 'order-1',
                ],
                [
                    'id' => 2,
                    'order' => 'order-1',
                ],
            ],
        ];
        $actual = $order->toArray();

        $this->assertEquals($expected, $actual);
    }
}
