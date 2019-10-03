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
use Wszetko\Sitemap\Traits\Domain;
use Wszetko\Sitemap\Traits\Required;

/**
 * Class AbstractDataType.
 *
 * @package Wszetko\Sitemap\Items\DataTypes
 */
abstract class AbstractDataType implements DataType
{
    use Required;
    use Domain;

    /**
     * Property that handle value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Property that handle attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * DataType name.
     *
     * @var string
     */
    private $name;

    /**
     * AbstractDataType constructor.
     *
     * @param mixed $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * To clone properties of object with no reference.
     */
    public function __clone()
    {
        foreach ($this->attributes as $attribute => $value) {
            if ('object' == gettype($value)) {
                $this->attributes[$attribute] = clone $this->attributes[$attribute];
            }
        }
    }

    /**
     * Return name od DataType.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        $value = $this->value;

        try {
            $attributes = $this->getAttributes();

            if ([] !== $attributes) {
                return [$value => $attributes];
            }
        } catch (InvalidArgumentException $e) {
            return null;
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function setValue($value, $parameters = []): DataType
    {
        $this->value = $value;

        foreach ($parameters as $key => $attribute) {
            if (null !== $attribute && '' !== $attribute) {
                $attr = array_keys($this->attributes)[$key];

                if ('' !== $attr) {
                    $this->attributes[$attr]->setValue($attribute);
                }
            }
        }

        return $this;
    }

    /**
     * Add multiple attrubutes do DataType.
     *
     * @param mixed $attributes
     *
     * @return static
     */
    public function addAttributes($attributes): self
    {
        foreach ($attributes as $name => $dataType) {
            $this->attributes[$name] = new $dataType($name);
        }

        return $this;
    }

    /**
     * Return object used to handle provided attribute.
     *
     * @param mixed $name
     *
     * @return null|AbstractDataType
     */
    public function getAttribute($name): ?AbstractDataType
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * Return all attributes values.
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function getAttributes(): array
    {
        $attributes = [];

        foreach ($this->attributes as $name => $value) {
            $this->propagateDomain($value);

            if (null !== $value->getValue() && '' !== $value->getValue()) {
                $attributes[$name] = $value->getValue();
            } elseif ($value->isRequired()) {
                throw new InvalidArgumentException('Lack of required value');
            }
        }

        return $attributes;
    }

    /**
     * Pass domain name to attributes if necessary.
     *
     * @param mixed $target
     */
    public function propagateDomain(&$target): void
    {
        if (method_exists($target, 'setDomain') && null !== $this->getDomain()) {
            $target->setDomain($this->getDomain());
        }
    }
}
