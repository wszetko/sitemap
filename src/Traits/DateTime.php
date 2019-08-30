<?php


namespace Wszetko\Sitemap\Traits;


use DateTimeInterface;
use InvalidArgumentException;

/**
 * Trait DateTime
 * @package Wszetko\Sitemap\Traits
 */
trait DateTime
{
    /**
     * @param DateTimeInterface|string $dateTime
     *
     * @return string|null
     */
    private function processDateTime($dateTime, $required = FALSE): ?string
    {
        if (is_string($dateTime)) {
            $dateTime = date_create($dateTime);
        }

        if ($dateTime && (int) $dateTime->format("Y") < 0) {
            $dateTime = null;
        }

        if (!empty($dateTime)) {
            if ($dateTime->format('H') == 0 &&
                $dateTime->format('i') == 0 &&
                $dateTime->format('s') == 0) {
                $dateTime = $dateTime->format("Y-m-d");
            } else {
                $dateTime = $dateTime->format(DateTimeInterface::W3C);
            }
        } elseif ($required) {
            throw new InvalidArgumentException('Invalid date parameter.');
        }

        return $dateTime;
    }
}