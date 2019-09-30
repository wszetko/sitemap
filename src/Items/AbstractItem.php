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

namespace Wszetko\Sitemap\Items;

use Error;
use ReflectionClass;
use ReflectionProperty;
use Wszetko\Sitemap\Interfaces\DataType;
use Wszetko\Sitemap\Interfaces\Item;
use Wszetko\Sitemap\Items\DataTypes\ArrayType;
use Wszetko\Sitemap\Traits\Domain;
use Wszetko\Sitemap\Traits\IsAssoc;

/**
 * Class AbstractItem.
 *
 * @package Wszetko\Sitemap\Items
 */
abstract class AbstractItem implements Item
{
    use IsAssoc;
    use Domain;

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

            if (
                is_array($data) &&
                (
                    isset($data['type']) &&
                    '' !== $data['type']
                ) &&
                class_exists($data['type']) &&
                in_array('Wszetko\Sitemap\Interfaces\DataType', class_implements($data['type']), true)
            ) {
                if (
                    isset($data['dataType']) &&
                    '' !== $data['dataType'] &&
                    class_exists($data['dataType'])
                ) {
                    $this->{$property->getName()} = new ArrayType($property->getName(), $data['dataType']);
                    $this->{$property->getName()}->getBaseDataType()->addAttributes($data['attributes']);
                } else {
                    $this->{$property->getName()} = new $data['type']($property->getName());
                    $this->{$property->getName()}->addAttributes($data['attributes']);
                }
            }
        }
    }

    /**
     * @param mixed $name
     * @param mixed $arguments
     *
     * @return mixed
     *
     * @throws \Error
     */
    public function __call($name, $arguments)
    {
        $operation = mb_substr($name, 0, 3);
        $property = lcfirst(mb_substr($name, 3));

        if (
            property_exists($this, $property) &&
            in_array($operation, ['add', 'set', 'get'], true) &&
            ($this->{$property} instanceof DataType)
        ) {
            switch ($operation) {
                case 'add':
                    if (method_exists($this->{$property}, 'addValue')) {
                        $this->{$property}->addValue($arguments[0], array_slice($arguments, 1));

                        return $this;
                    }

                    break;
                case 'set':
                    $this->{$property}->setValue($arguments[0], array_slice($arguments, 1));

                    return $this;
                case 'get':
                    if (
                        method_exists($this->{$property}, 'setDomain') &&
                        null !== $this->getDomain()
                    ) {
                        $this->{$property}->setDomain($this->getDomain());
                    }

                    return $this->{$property}->getValue();
            }
        }

        throw new Error('Call to undefined method ' . __CLASS__ . '::' . $name . '()');
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [];

        if (static::NAMESPACE_NAME && static::ELEMENT_NAME) {
            $array = [
                '_namespace' => static::NAMESPACE_NAME,
                '_element' => static::ELEMENT_NAME,
            ];
        }

        $array[static::ELEMENT_NAME] = [];

        foreach (array_keys(get_object_vars($this)) as $property) {
            if (is_object($this->{$property})) {
                $method = 'get' . ucfirst($property);
                preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $property, $matches);
                $property = $matches[0];

                foreach ($property as &$match) {
                    $match = $match == mb_strtoupper($match) ? mb_strtolower($match) : lcfirst($match);
                }

                $property = implode('_', $property);
                $data = $this->{$method}();

                if (is_array($data)) {
                    if ($this->isAssoc($data)) {
                        $item = array_key_first($data);

                        if (null !== $item) {
                            $array[static::ELEMENT_NAME][$property]['_value'] = $item;

                            if (array_key_exists($item, $data)) {
                                foreach ($data[$item] as $attr => $val) {
                                    $array[static::ELEMENT_NAME][$property]['_attributes'][$attr] = $val;
                                }
                            }
                        }
                    } else {
                        foreach ($data as $element) {
                            $array[static::ELEMENT_NAME][$property][] = $element;
                        }
                    }
                } elseif (null !== $data && '' !== $data) {
                    $array[static::ELEMENT_NAME][$property] = $data;
                }
            }
        }

        return $array;
    }

    /**
     * @param \ReflectionProperty $property
     *
     * @return null|array
     */
    private function grabData(ReflectionProperty $property): ?array
    {
        if (false === $property->getDocComment()) {
            // @codeCoverageIgnoreStart
            return null;
            // @codeCoverageIgnoreEnd
        }

        preg_match_all(
            '/
                        @var\s+(?\'type\'[^\s]+)|
                        @dataType\s+(?\'dataType\'[^\s]+)|
                        @attribute\s+(?\'attribute\'[^\s]+)|
                        @attributeDataType\s+(?\'attributeDataType\'[^\s]+)
                    /mx',
            $property->getDocComment(),
            $matches
        );

        $results = [
            'type' => null,
            'dataType' => null,
            'attributes' => [],
        ];

        foreach ($matches['type'] as $match) {
            if ('' !== $match && null !== $match) {
                $results['type'] = $match;

                break;
            }
        }

        foreach ($matches['dataType'] as $match) {
            if ('' !== $match && null !== $match) {
                $results['dataType'] = $match;

                break;
            }
        }

        foreach ($matches['attribute'] as $key => $match) {
            if (
                '' !== $match &&
                null !== $match &&
                isset($matches['attributeDataType'][$key + 1]) &&
                '' !== $matches['attributeDataType'][$key + 1]
            ) {
                $results['attributes'][$match] = $matches['attributeDataType'][$key + 1];
            }
        }

        return $results;
    }
}
