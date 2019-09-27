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

namespace Wszetko\Sitemap\Items\DataTypes;

use Wszetko\Sitemap\Interfaces\DataType;

/**
 * Class FloatType.
 *
 * @package Wszetko\Sitemap\Items\DataTypes
 */
class FloatType extends AbstractDataType
{
    /**
     * @var int
     */
    protected $precision = 0;

    /**
     * @var float|int
     */
    private $minValue;

    /**
     * @var float|int
     */
    private $maxValue;

    /**
     * @return null|float
     */
    public function getMinValue(): ?float
    {
        return $this->minValue;
    }

    /**
     * @param float $minValue
     *
     * @return \Wszetko\Sitemap\Items\DataTypes\FloatType
     */
    public function setMinValue(float $minValue): self
    {
        $this->minValue = $minValue;

        return $this;
    }

    /**
     * @return null|float
     */
    public function getMaxValue(): ?float
    {
        return $this->maxValue;
    }

    /**
     * @param float $maxValue
     *
     * @return \Wszetko\Sitemap\Items\DataTypes\FloatType
     */
    public function setMaxValue(float $maxValue): self
    {
        $this->maxValue = $maxValue;

        return $this;
    }

    /**
     * @return int
     */
    public function getPrecision(): int
    {
        return $this->precision;
    }

    /**
     * @param int $precision
     *
     * @return \Wszetko\Sitemap\Items\DataTypes\FloatType
     */
    public function setPrecision(int $precision): self
    {
        $this->precision = $precision;

        return $this;
    }

    /**
     * @param mixed $value
     * @param array $parameters
     *
     * @return static
     */
    public function setValue($value, $parameters = []): DataType
    {
        if (null === $value) {
            $this->value = null;

            return $this;
        }

        if (is_numeric($value)) {
            $value = (float) $value;
        } else {
            return $this;
        }

        if (null !== $this->getMinValue() && $value < $this->getMinValue()) {
            return $this;
        }

        if (null !== $this->getMaxValue() && $value > $this->getMaxValue()) {
            return $this;
        }

        if (null !== $this->getPrecision()) {
            $value = round($value, $this->getPrecision());
            $value = number_format($value, $this->getPrecision(), '.', '');
        }

        parent::setValue($value, $parameters);

        return $this;
    }
}
