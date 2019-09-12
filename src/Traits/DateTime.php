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

namespace Wszetko\Sitemap\Traits;

use DateTimeInterface;
use InvalidArgumentException;

/**
 * Trait DateTime.
 *
 * @package Wszetko\Sitemap\Traits
 */
trait DateTime
{
    /**
     * @param DateTimeInterface|string $dateTime
     * @param bool                     $required
     *
     * @return null|string
     */
    private function processDateTime($dateTime, $required = false): ?string
    {
        if (is_string($dateTime)) {
            $dateTime = date_create($dateTime);
        }

        if ($dateTime && (int) $dateTime->format('Y') < 0) {
            $dateTime = null;
        }

        if (!empty($dateTime)) {
            if (0 == $dateTime->format('H') &&
                0 == $dateTime->format('i') &&
                0 == $dateTime->format('s')) {
                $dateTime = $dateTime->format('Y-m-d');
            } else {
                $dateTime = $dateTime->format(DateTimeInterface::W3C);
            }
        } elseif ($required) {
            throw new InvalidArgumentException('Invalid date parameter.');
        }

        return $dateTime;
    }
}
