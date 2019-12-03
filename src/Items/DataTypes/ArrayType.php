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
 * Class ArrayType.
 *
 * @package Wszetko\Sitemap\Items\DataTypes
 */
class ArrayType extends AbstractDataType
{
    /**
     * Number of maximum elements to keep.
     *
     * @var null|int
     */
    protected $maxElements;

    /**
     * Base object that are used to create elements.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\AbstractDataType
     */
    private $baseDataType;

    /**
     * @inheritDoc
     *
     * @var string $dataType
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $name, string $dataType)
    {
        parent::__construct($name);

        $baseDataType = new $dataType($this->getName());

        if (($baseDataType instanceof AbstractDataType) === false) {
            // @codeCoverageIgnoreStart
            throw new InvalidArgumentException('Provided DataType is invalid.');
            // @codeCoverageIgnoreEnd
        }

        $this->baseDataType = $baseDataType;
        $this->value = [];
    }

    /**
     * Return object to create new element.
     *
     * @return \Wszetko\Sitemap\Items\DataTypes\AbstractDataType
     */
    public function getBaseDataType(): AbstractDataType
    {
        return $this->baseDataType;
    }

    /**
     * Return maximum number of elements to handle.
     *
     * @return null|int
     */
    public function getMaxElements(): ?int
    {
        return $this->maxElements;
    }

    /**
     * Set maximum number of elements to handle.
     *
     * @param int $maxElements
     *
     * @return self
     */
    public function setMaxElements(int $maxElements): self
    {
        $this->maxElements = $maxElements;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setValue($value, $parameters = []): DataType
    {
        if (is_array($value)) {
            foreach ($value as $val) {
                $this->addValue($val, $parameters);
            }
        } else {
            $this->addValue($value, $parameters);
        }

        return $this;
    }

    /**
     * Add single element to collection.
     *
     * @param mixed $value
     * @param array $parameters
     *
     * @return \Wszetko\Sitemap\Interfaces\DataType
     */
    public function addValue($value, $parameters = []): DataType
    {
        if (is_array($value)) {
            foreach ($value as $val) {
                $this->addValue($val, $parameters);
            }
        } else {
            $var = clone $this->getBaseDataType();
            $var->setValue($value, $parameters);

            if (null !== $var->getValue()) {
                $this->value[] = $var;
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        $values = parent::getValue();

        if (null === $values || [] === $values) {
            return null;
        }

        $result = [];

        if (is_array($values)) {
            /** @var DataType $element */
            foreach ($values as $element) {
                $this->propagateDomain($element);
                $value = $element->getValue();

                if (
                    is_array($value) &&
                    isset(array_values($value)[0]) &&
                    '' !== array_values($value)[0]
                ) {
                    $key = array_key_first($value);

                    if (null !== $key) {
                        $result[$key] = array_values($value)[0];
                    }
                } elseif (null !== $value && '' !== $value) {
                    $result[] = $value;
                }
            }

            if (null !== $this->getMaxElements()) {
                $result = array_slice($result, 0, $this->getMaxElements());
            }
        }

        return $result;
    }
}
