<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items\DataTypes;

use Wszetko\Sitemap\Interfaces\DataType;
use Wszetko\Sitemap\Traits\Domain;

/**
 * Class ArrayType
 *
 * @package Wszetko\Sitemap\Items\DataTypes
 */
class ArrayType extends AbstractDataType
{
    /**
     * @var \Wszetko\Sitemap\Items\DataTypes\AbstractDataType
     */
    private $baseDataType;

    /**
     * @var int
     */
    protected $maxElements;

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
     * @return int|null
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
     * @param       $value
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
    public function addValue($value, ...$parameters)
    {
        if (is_array($value)) {
            foreach ($value as $val) {
                $this->addValue($val, $parameters[0]);
            }
        } else {
            $var = clone $this->getBaseDataType();
            $var->setValue($value, $parameters[0]);

            if (!is_null($var->getValue())) {
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

        foreach ($values as $element) {
            $this->propagateDomain($element);
            $value = $element->getValue();

            if (is_array($value) && !empty(array_values($value)[0])) {
                $result[array_key_first($value)] = array_values($value)[0];
            } elseif (!empty($value)) {
                $result[] = $value;
            }
        }

        if ($this->getMaxElements()) {
            $result = array_slice($result, 0, $this->getMaxElements());
        }

        return $result;
    }
}