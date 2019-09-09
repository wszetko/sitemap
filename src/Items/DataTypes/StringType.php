<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items\DataTypes;

use InvalidArgumentException;
use Wszetko\Sitemap\Interfaces\DataType;

/**
 * Class StringType
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
     * @var array|null
     */
    protected $allowedValues = null;

    /**
     * @var string
     */
    protected $regex;

    /**
     * @var string
     */
    protected $regexGroup;

    /**
     * @return int|null
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
     * @return int|null
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
     * @return array|null
     */
    public function getAllowedValues(): ?array
    {
        return $this->allowedValues;
    }

    /**
     * @param string|array|null $allowedValues
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
     * @return array|null
     */
    public function getValueRegex(): ?array
    {
        if (!empty($this->regex) && !empty($this->regexGroup)) {
            return [$this->regexGroup => $this->regex];
        }

        return null;
    }

    /**
     * @param string|int|float|object|null $value
     * @param array                        $parameters
     *
     * @return self
     */
    public function setValue($value, ...$parameters): DataType
    {
        if (is_numeric($value)) {
            $value = strval($value);
        } elseif (is_object($value)) {
            if (method_exists($value, '__toString')) {
                $value = strval($value);
            } else {
                $value = null;
            }
        }

        if ($this->getMinLength() !== null && mb_strlen($value) < $this->getMinLength()) {
            $value = null;
        }

        if ($this->getMaxLength() !== null && $value !== null && mb_strlen($value) > $this->getMaxLength()) {
            $value = mb_substr($value, 0, $this->getMaxLength());
        }

        if (!empty($this->getAllowedValues())) {
            $match = preg_grep("/$value/i", $this->getAllowedValues());

            if (empty($match)) {
                $value = null;
            } else {
                $value = array_values($match)[0];
            }
        }

        $regex = $this->getValueRegex();

        if (!empty($regex)) {
            preg_match_all(array_values($regex)[0], $value, $matches);

            if (empty($matches[array_key_first($regex)])) {
                $value = null;
            }
        }

        if (empty($value) && $this->isRequired()) {
            throw new InvalidArgumentException($this->getName() . ' need to be set.');
        }

        $value = is_string($value) ? trim($value) : $value;

        parent::setValue($value, $parameters[0] ?? []);

        return $this;
    }
}
