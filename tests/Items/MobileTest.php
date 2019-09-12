<?php

declare(strict_types=1);

/**
 * This file is part of Wszetko Sitemap.
 *
 * (c) Paweł Kłopotek-Główczewski <pawelkg@pawelkg.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Wszetko\Sitemap\Tests;

use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Items\Mobile;

/**
 * Class MobileTest.
 *
 * @package Wszetko\Sitemap\Tests
 *
 * @internal
 * @coversNothing
 */
class MobileTest extends TestCase
{
    public function testToArray()
    {
        $mobile = new Mobile();

        $expectedResult = [
            '_namespace' => $mobile::NAMESPACE_NAME,
            '_element' => 'mobile',
            'mobile' => [],
        ];

        $this->assertEquals($expectedResult, $mobile->toArray());
    }
}
