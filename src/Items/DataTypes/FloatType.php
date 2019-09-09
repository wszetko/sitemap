<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items\DataTypes;

use Wszetko\Sitemap\Interfaces\DataType;

/**
 * Class FloatType
 *
 * @package Wszetko\Sitemap\Items\DataTypes
 */
class FloatType extends AbstractDataType
{
    /**
     * @var int|float
     */
    private $minValue;

    /**
     * @var int|float
     */
    private $maxValue;

    /**
     * @var int
     */
    protected $precision = null;

    /**
     * @return float|null
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
     * @return float|null
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
     * @return int|null
     */
    public function getPrecision(): ?int
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
     * @param int|float|string|null $value
     * @param array                 $parameters
     *
     * @return \Wszetko\Sitemap\Interfaces\DataType
     */
    public function setValue($value, ...$parameters): DataType
    {
        if (is_null($value)) {
            $this->value = null;

            return $this;
        }

        if (is_string($value)) {
            if (is_numeric($value)) {
                $value = floatval($value);
            } else {
                return $this;
            }
        } elseif (!is_numeric($value)) {
            return $this;
        }

        if ($this->getMinValue() !== null && $value < $this->getMinValue()) {
            return $this;
        }

        if ($this->getMaxValue() !== null && $value > $this->getMaxValue()) {
            return $this;
        }

        if ($this->getPrecision()) {
            $value = round($value, $this->getPrecision());
            $value = number_format($value, $this->getPrecision(), '.', '');
        }

        parent::setValue($value, $parameters[0]);

        return $this;
    }
}
