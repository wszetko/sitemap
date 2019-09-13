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

use InvalidArgumentException;
use Wszetko\Sitemap\Interfaces\DataType;

/**
 * Class StringType.
 *
 * @package Wszetko\Sitemap\Items\DataTypes
 */
class StringType extends AbstractDataType
{
    /**
     * @var int
     */
    protected $minLength;

    /**
     * @var int
     */
    protected $maxLength;

    /**
     * @var null|array
     */
    protected $allowedValues;

    /**
     * @var string
     */
    protected $regex;

    /**
     * @var string
     */
    protected $regexGroup;

    /**
     * @var string
     */
    protected $conversion;

    /**
     * @return null|int
     */
    public function getMinLength(): ?int
    {
        return $this->minLength;
    }

    /**
     * @param int $minLength
     *
     * @return \Wszetko\Sitemap\Items\DataTypes\StringType
     */
    public function setMinLength(int $minLength): self
    {
        $this->minLength = $minLength;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getMaxLength(): ?int
    {
        return $this->maxLength;
    }

    /**
     * @param int $maxLength
     *
     * @return self
     */
    public function setMaxLength(int $maxLength): self
    {
        $this->maxLength = $maxLength;

        return $this;
    }

    /**
     * @return null|array
     */
    public function getAllowedValues(): ?array
    {
        return $this->allowedValues;
    }

    /**
     * @param null|array|string $allowedValues
     *
     * @return \Wszetko\Sitemap\Items\DataTypes\StringType
     */
    public function setAllowedValues($allowedValues): self
    {
        if (is_string($allowedValues)) {
            $allowedValues = explode(',', $allowedValues);

            foreach ($allowedValues as $allowedValue) {
                $this->allowedValues[] = trim($allowedValue);
            }
        } elseif (is_array($allowedValues)) {
            $this->allowedValues = $allowedValues;
        }

        return $this;
    }

    /**
     * @param string $regex
     * @param string $regexGroup
     *
     * @return \Wszetko\Sitemap\Items\DataTypes\StringType
     */
    public function setValueRegex(string $regex, string $regexGroup): self
    {
        $this->regex = $regex;
        $this->regexGroup = $regexGroup;

        return $this;
    }

    /**
     * @return null|array
     */
    public function getValueRegex(): ?array
    {
        if (!empty($this->regex) && !empty($this->regexGroup)) {
            return [$this->regexGroup => $this->regex];
        }

        return null;
    }

    /**
     * @param string $convertion
     *
     * @return $this
     */
    public function setConversion(string $convertion): self
    {
        if (in_array($convertion, ['upper', 'UPPER', 'Upper'])) {
            $this->conversion = 'upper';
        } elseif (in_array($convertion, ['lower', 'LOWER', 'Lower'])) {
            $this->conversion = 'lower';
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getConversion(): ?string
    {
        return $this->conversion;
    }

    /**
     * @param null|float|int|object|string $value
     * @param array                        $parameters
     *
     * @return self
     */
    public function setValue($value, ...$parameters): DataType
    {
        if (null !== $value) {
            $value = (string) $value;
            $this->checkValue($value);
        }

        if (empty($value) && $this->isRequired()) {
            throw new InvalidArgumentException($this->getName() . ' need to be set.');
        }

        parent::setValue($value, $parameters[0] ?? []);

        return $this;
    }

    /**
     * @param $value
     */
    private function checkValue(&$value)
    {
        $value = trim($value);
        $this->checkValueLength($value);
        $this->convertValue($value);
        $this->checkIfValueIsInAllowedValues($value);
        $this->checkValueRegex($value);
    }

    /**
     * @param $value
     */
    private function checkValueLength(&$value)
    {
        if (null !== $this->getMinLength() && mb_strlen($value) < $this->getMinLength()) {
            $value = null;
        }

        if (null !== $this->getMaxLength() && null !== $value && mb_strlen($value) > $this->getMaxLength()) {
            $value = mb_substr($value, 0, $this->getMaxLength());
        }
    }

    /**
     * @param $value
     */
    private function convertValue(&$value)
    {
        if ($conversion = $this->getConversion()) {
            if ('upper' == $conversion) {
                $value = mb_strtoupper($value);
            } elseif ('lower' == $conversion) {
                $value = mb_strtolower($value);
            }
        }
    }

    /**
     * @param $value
     */
    private function checkIfValueIsInAllowedValues(&$value)
    {
        if (!empty($this->getAllowedValues())) {
            $match = preg_grep("/{$value}/i", $this->getAllowedValues());

            if (empty($match)) {
                $value = null;
            } else {
                $value = array_values($match)[0];
            }
        }
    }

    /**
     * @param $value
     */
    private function checkValueRegex(&$value)
    {
        $regex = $this->getValueRegex();

        if (!empty($regex)) {
            preg_match_all(array_values($regex)[0], $value, $matches);

            if (empty($matches[array_key_first($regex)])) {
                $value = null;
            }
        }
    }
}
