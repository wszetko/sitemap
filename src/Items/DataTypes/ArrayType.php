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
 * Class ArrayType.
 *
 * @package Wszetko\Sitemap\Items\DataTypes
 */
class ArrayType extends AbstractDataType
{
    /**
     * @var null|int
     */
    protected $maxElements;

    /**
     * @var \Wszetko\Sitemap\Items\DataTypes\AbstractDataType
     */
    private $baseDataType;

    /**
     * ArrayType constructor.
     *
     * @param $name
     * @param $dataType
     */
    public function __construct($name, $dataType)
    {
        parent::__construct($name);

        $this->baseDataType = new $dataType($this->getName());
        $this->value = [];
    }

    /**
     * @return \Wszetko\Sitemap\Items\DataTypes\AbstractDataType
     */
    public function getBaseDataType(): AbstractDataType
    {
        return $this->baseDataType;
    }

    /**
     * @return null|int
     */
    public function getMaxElements(): ?int
    {
        return $this->maxElements;
    }

    /**
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
     * @param mixed $value
     * @param mixed ...$parameters
     *
     * @return \Wszetko\Sitemap\Interfaces\DataType
     */
    public function setValue($value, ...$parameters): DataType
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
     * @param       $value
     * @param mixed ...$parameters
     */
    public function addValue($value, ...$parameters): void
    {
        if (is_array($value)) {
            foreach ($value as $val) {
                $this->addValue($val, $parameters[0]);
            }
        } else {
            $var = clone $this->getBaseDataType();
            $var->setValue($value, $parameters[0]);

            if (null !== $var->getValue()) {
                $this->value[] = $var;
            }
        }
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        $values = parent::getValue();

        if (empty($values)) {
            return null;
        }

        $result = [];

        if (is_array($values)) {
            /** @var DataType $element */
            foreach ($values as $element) {
                $this->propagateDomain($element);
                $value = $element->getValue();

                if (is_array($value) && !empty(array_values($value)[0])) {
                    $result[array_key_first($value)] = array_values($value)[0];
                } elseif (!empty($value)) {
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
