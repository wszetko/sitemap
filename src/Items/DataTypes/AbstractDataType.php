<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items\DataTypes;

use Wszetko\Sitemap\Interfaces\DataType;
use Wszetko\Sitemap\Traits\Domain;
use Wszetko\Sitemap\Traits\Required;

/**
 * Class AbstractDataType
 *
 * @package Wszetko\Sitemap\Items\DataTypes
 */
abstract class AbstractDataType implements DataType
{
    use Required;
    use Domain;

    /**
     * @var string
     */
    private $name;

    /**
     * @var
     */
    protected $value;

    /**
     * @var array
     */
    protected $attributes = [];

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
        } else {
            return $value;
        }
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

    public function addAttributes($attributes): self
    {
        foreach ($attributes as $name => $dataType) {
            $this->attributes[$name] = new $dataType($name);
        }

        return $this;
    }

    public function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

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

    public function propagateDomain(object &$target): void
    {
        if ($this->getDomain() !== null) {
            $target->setDomain($this->getDomain());
        }
    }

    /**
     * To clone properties of object with no reference
     */
    public function __clone()
    {
        foreach ($this->attributes as $attribute => $value) {
            if (gettype($value) == 'object') {
                $this->attributes[$attribute] = clone $this->attributes[$attribute];
            }
        }
    }
}