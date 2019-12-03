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
     * Minimum length of string.
     *
     * @var null|int
     */
    protected $minLength;

    /**
     * Maximum length of string.
     *
     * @var null|int
     */
    protected $maxLength;

    /**
     * List of allowed values that can be set.
     *
     * @var null|array
     */
    protected $allowedValues;

    /**
     * Regular expression to test value.
     *
     * @var null|string
     */
    protected $regex;

    /**
     * Type of string conversion.
     *
     * @var null|string
     */
    protected $conversion;

    /**
     * Return minimal string length.
     *
     * @return null|int
     */
    public function getMinLength(): ?int
    {
        return $this->minLength;
    }

    /**
     * Set minimal string length.
     *
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
     * Return maximum string length.
     *
     * @return null|int
     */
    public function getMaxLength(): ?int
    {
        return $this->maxLength;
    }

    /**
     * Set maximum string length.
     *
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
     * Return list of allowed values.
     *
     * @return null|array
     */
    public function getAllowedValues(): ?array
    {
        return $this->allowedValues;
    }

    /**
     * Set list of allowed values.
     *
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
     * Set regular expression to test value.
     *
     * @param string $regex
     *
     * @return \Wszetko\Sitemap\Items\DataTypes\StringType
     */
    public function setValueRegex(string $regex): self
    {
        $this->regex = $regex;

        return $this;
    }

    /**
     * Return regular expression to test value.
     *
     * @return null|string
     */
    public function getValueRegex(): ?string
    {
        if (null !== $this->regex && '' !== $this->regex) {
            return $this->regex;
        }

        return null;
    }

    /**
     * Set conversion of value.
     *
     * @param string $conversion
     *
     * @return $this
     */
    public function setConversion(string $conversion): self
    {
        if (in_array($conversion, ['upper', 'UPPER', 'Upper'], true)) {
            $this->conversion = 'upper';
        } elseif (in_array($conversion, ['lower', 'LOWER', 'Lower'], true)) {
            $this->conversion = 'lower';
        }

        return $this;
    }

    /**
     * Return conversion of value.
     *
     * @return null|string
     */
    public function getConversion(): ?string
    {
        return $this->conversion;
    }

    /**
     * @inheritDoc
     *
     * @throws \InvalidArgumentException
     */
    public function setValue($value, $parameters = []): DataType
    {
        if (null !== $value) {
            $value = (string) $value;
            $this->checkValue($value);
        }

        if (
            (null === $value
            || '' === $value)
            && $this->isRequired()
        ) {
            throw new InvalidArgumentException($this->getName() . ' need to be set.');
        }

        parent::setValue($value, $parameters);

        return $this;
    }

    /**
     * Parse value if is valid one.
     *
     * @param string $value
     * @param-out null|string $value
     *
     * @return void
     */
    private function checkValue(string &$value): void
    {
        $value = trim($value);
        $this->checkValueLength($value);
        $this->convertValue($value);
        $this->checkIfValueIsInAllowedValues($value);
        $this->checkValueRegex($value);
    }

    /**
     * Check if value have valid length.
     *
     * @param null|string $value
     * @param-out null|string $value
     *
     * @return void
     */
    private function checkValueLength(?string &$value): void
    {
        if (null !== $value) {
            if (null !== $this->getMinLength() && mb_strlen($value) < $this->getMinLength()) {
                $value = null;
            }

            if (null !== $this->getMaxLength() && null !== $value && mb_strlen($value) > $this->getMaxLength()) {
                $value = mb_substr($value, 0, $this->getMaxLength());
            }
        }
    }

    /**
     * Convert value if needed.
     *
     * @param null|string $value
     * @param-out null|string $value
     *
     * @return void
     */
    private function convertValue(?string &$value): void
    {
        if (null !== $value) {
            $conversion = $this->getConversion();

            if (is_string($conversion)) {
                if ('upper' == $conversion) {
                    $value = mb_strtoupper($value);
                } elseif ('lower' == $conversion) {
                    $value = mb_strtolower($value);
                }
            }
        }
    }

    /**
     * Check if value is in allowed list.
     *
     * @param null|string $value
     * @param-out null|string $value
     *
     * @return void
     */
    private function checkIfValueIsInAllowedValues(?string &$value): void
    {
        if (null !== $value) {
            $allowedValues = $this->getAllowedValues();

            if (null !== $allowedValues) {
                $match = preg_grep("/{$value}/i", $allowedValues);

                if ([] === $match) {
                    $value = null;
                } else {
                    $value = array_values($match)[0];
                }
            }
        }
    }

    /**
     * Test value with provided regular expression.
     *
     * @param null|string $value
     * @param-out null|string $value
     *
     * @return void
     */
    private function checkValueRegex(?string &$value): void
    {
        if (null !== $value) {
            $regex = $this->getValueRegex();

            if (null !== $regex && '' !== $regex) {
                $match = preg_match($regex, $value);

                if (1 !== $match) {
                    $value = null;
                }
            }
        }
    }
}
