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

namespace Wszetko\Sitemap\Helpers;

use Exception;

/**
 * Class Directory.
 *
 * @package Wszetko\Sitemap\Helpers
 */
class Directory
{
    /**
     * @param string $directory
     *
     * @return string
     *
     * @throws \Exception
     */
    public static function checkDirectory(string $directory): string
    {
        $dir = realpath($directory);

        if (false === $dir) {
            mkdir(
                $directory,
                0777,
                true
            );
            $dir = realpath($directory);
        }

        if (false === $dir) {
            // @codeCoverageIgnoreStart
            throw new Exception("Can't get directory $directory.");
            // @codeCoverageIgnoreEnd
        }

        return $dir;
    }

    /**
     * @param string $dir
     */
    public static function removeDir(string $dir): void
    {
        if (is_dir($dir)) {
            return;
        }

        $objects = scandir($dir);

        if (false !== $objects) {
            foreach ($objects as $object) {
                if ('.' === $object || '..' === $object) {
                    continue;
                }

                if (is_dir($dir . '/' . $object)) {
                    self::removeDir($dir . '/' . $object);
                } else {
                    unlink($dir . '/' . $object);
                }
            }
        }

        rmdir($dir);
    }
}
