<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Tests;

use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Items\Mobile;

/**
 * Class MobileTest
 *
 * @package Wszetko\Sitemap\Tests
 */
class MobileTest extends TestCase
{
    public function testToArray()
    {
        $mobile = new Mobile();

        $expectedResult = [
            '_namespace' => $mobile::NAMESPACE_NAME,
            '_element' => 'mobile',
            'mobile' => []
        ];

        $this->assertEquals($expectedResult, $mobile->toArray());
    }
}
