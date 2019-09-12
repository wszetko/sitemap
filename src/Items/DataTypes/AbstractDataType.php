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
     * @var
     */
    protected $value;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var string
     */
    private $name;

    /**
     * AbstractDataType constructor.
     *
     * @param $name
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        $value = $this->value;
        $attributes = $this->getAttributes();

        if (!empty($attributes)) {
            return [$value => $attributes];
        }

        return $value;
    }

    /**
     * @param       $value
     * @param mixed ...$parameters
     *
     * @return \Wszetko\Sitemap\Interfaces\DataType
     */
    public function setValue($value, ...$parameters): DataType
    {
        $this->value = $value;

        foreach ($parameters[0] as $key => $attribute) {
            if (!empty($attribute)) {
                $attr = array_keys($this->attributes)[$key] ?? null;

                if ($attr) {
                    $this->attributes[$attr]->setValue($attribute);
                }
            }
        }

        return $this;
    }

    /**
     * @param $attributes
     *
     * @return \Wszetko\Sitemap\Items\DataTypes\AbstractDataType
     */
    public function addAttributes($attributes): self
    {
        foreach ($attributes as $name => $dataType) {
            $this->attributes[$name] = new $dataType($name);
        }

        return $this;
    }

    /**
     * @param $name
     *
     * @return null|mixed
     */
    public function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        $attributes = [];

        foreach ($this->attributes as $name => $value) {
            $this->propagateDomain($value);
//            var_dump($this->getDomain(), $value->getDomain());
            if (!empty($value->getValue())) {
                $attributes[$name] = $value->getValue();
            }
        }

        return $attributes;
    }

    /**
     * @param object $target
     */
    public function propagateDomain(object &$target): void
    {
        if (null !== $this->getDomain()) {
            $target->setDomain($this->getDomain());
        }
    }
}
