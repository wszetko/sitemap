<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items;

use ReflectionClass;
use ReflectionProperty;
use Wszetko\Sitemap\Interfaces\DataType;
use Wszetko\Sitemap\Interfaces\Item;
use Wszetko\Sitemap\Items\DataTypes\ArrayType;
use Wszetko\Sitemap\Traits\IsAssoc;

/**
 * Class AbstractItem
 *
 * @package Wszetko\Sitemap\Items
 */
abstract class AbstractItem implements Item
{
    use IsAssoc;

    /**
     * AbstractItem constructor.
     *
     * @throws \ReflectionException
     */
    public function __construct()
    {
        $class = new ReflectionClass($this);
        $properties = $class->getProperties(ReflectionProperty::IS_PROTECTED);

        foreach ($properties as $property) {
            $data = $this->grabData($property);

            if (!empty($data['type']) &&
                class_exists($data['type']) &&
                in_array('Wszetko\Sitemap\Interfaces\DataType', class_implements($data['type'])))
            {
                if (!empty($data['dataType']) && class_exists($data['dataType'])) {
                    $this->{$property->getName()} = new ArrayType($property->getName(), $data['dataType']);
                    $this->{$property->getName()}->getBaseDataType()->addAttributes($data['attributes']);
                } else {
                    $this->{$property->getName()} = new $data['type']($property->getName());
                    $this->{$property->getName()}->addAttributes($data['attributes']);
                }
            }
        }
    }

    private function grabData(ReflectionProperty $property): array
    {
        preg_match_all('/@var\s+(?\'type\'[^\s]+)|@dataType\s+(?\'dataType\'[^\s]+)|@attribute\s+(?\'attribute\'[^\s]+)|@attributeDataType\s+(?\'attributeDataType\'[^\s]+)/m', $property->getDocComment(), $matches);

        $results = [
            'type' => null,
            'dataType' => null,
            'attributes' => []
        ];

        foreach ($matches['type'] as $match) {
            if (!empty($match)) {
                $results['type'] = $match;
                break;
            }
        }

        foreach ($matches['dataType'] as $match) {
            if (!empty($match)) {
                $results['dataType'] = $match;
                break;
            }
        }

        foreach ($matches['attribute'] as $key => $match) {
            if (!empty($match) && !empty($matches['attributeDataType'][$key + 1])) {
                $results['attributes'][$match] = $matches['attributeDataType'][$key + 1];
            }
        }

        return $results;
    }

    public function toArray(): array
    {
        if (static::NAMESPACE_NAME && static::ELEMENT_NAME) {
            $array = [
                '_namespace' => static::NAMESPACE_NAME,
                '_element' => static::ELEMENT_NAME,
            ];
        }

        $array[static::ELEMENT_NAME] = [];

        foreach (get_object_vars($this) as $property => $value) {
            if (is_object($this->$property)) {
                $method = 'get' . ucfirst($property);
                preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $property, $matches);
                $property = $matches[0];

                foreach ($property as &$match) {
                    $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
                }

                $property = implode('_', $property);
                $data = $this->$method();

                if ($data) {
                    if (is_array($data)) {
//                        $array[static::ELEMENT_NAME][$property] = [];

                        if ($this->isAssoc($data)) {
                            $item = array_key_first($data);
                            $array[static::ELEMENT_NAME][$property]['_value'] = $item;
                            foreach ($data[$item] as $attr => $val) {
                                $array[static::ELEMENT_NAME][$property]['_attributes'][$attr] = $val;
                            }
                        } else {
                            foreach ($data as $element) {
                                $array[static::ELEMENT_NAME][$property][] = $element;
                            }
                        }

                    } else {
                        $array[static::ELEMENT_NAME][$property] = $data;
                    }
                }
            }
        }

        return $array;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $operation = substr($name, 0 ,3);
        $property = lcfirst(substr($name, 3));

        if (property_exists($this, $property) &&
            in_array($operation, ['add', 'set', 'get']) &&
            ($this->$property instanceof DataType)) {
            switch ($operation) {
                case 'add':
                    $this->$property->addValue($arguments[0], array_slice($arguments, 1));
                    return $this;
                    break;
                case 'set':
                    $this->$property->setValue($arguments[0], array_slice($arguments, 1));
                    return $this;
                    break;
                case 'get':
                    if (method_exists($this, 'getDomain') &&
                        method_exists($this->$property, 'setDomain') &&
                        $this->getDomain() !== null
                        ) {
                        $this->$property->setDomain($this->getDomain());
                    }

                    return $this->$property->getValue();
                    break;
            }
        }
    }
}