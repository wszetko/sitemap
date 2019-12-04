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

namespace Wszetko\Sitemap\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use Wszetko\Sitemap\Helpers\Directory;

/**
 * Class HelpersTest.
 *
 * @package Wszetko\Sitemap\Tests
 *
 * @internal
 */
class DirectoryTest extends TestCase
{
    public function testCheckDir()
    {
        $exampleDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sitemapTest';
        $this->assertStringContainsString('sitemapTest', Directory::checkDirectory($exampleDir));
    }

    public function testCheckDirSub()
    {
        $exampleDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sitemapTest' . DIRECTORY_SEPARATOR . 'test';
        $this->assertStringContainsString('sitemapTest' . DIRECTORY_SEPARATOR . 'test', Directory::checkDirectory($exampleDir));
    }

    public function testRemoveDir()
    {
        $exampleDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sitemapTest';
        Directory::checkDirectory($exampleDir);
        Directory::removeDir($exampleDir);
        $this->assertFalse(realpath($exampleDir));
    }

    public function testRemoveDirWithContent()
    {
        $exampleDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sitemapTest';
        $exampleDir = Directory::checkDirectory($exampleDir);
        file_put_contents($exampleDir . DIRECTORY_SEPARATOR . 'testFile.txt', 'test');
        Directory::removeDir($exampleDir);
        $this->assertFalse(realpath($exampleDir));
    }

    public function testRemoveDirNoDir()
    {
        $exampleDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sitemapTest';
        Directory::removeDir($exampleDir);
        $this->assertFalse(realpath($exampleDir));
    }
}
