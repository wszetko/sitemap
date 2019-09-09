<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items\DataTypes;

use Wszetko\Sitemap\Helpers\Url;

/**
 * Class URLType
 *
 * @package Wszetko\Sitemap\Items\DataTypes
 */
class URLType extends StringType
{
    /**
     * Determine if URL CAN be external
     *
     * @var bool
     */
    protected $external = false;

    /**
     * @return mixed|string|null
     */
    public function getValue()
    {
        if ($this->value === null) {
            return null;
        }

        if ($this->isExternal() && Url::normalizeUrl($this->value)) {
            $value = $this->value;
        } else {
            $value = str_replace($this->getDomain(), '', $this->value);

            if (!empty($value)) {
                $value = $this->getDomain() . '/' . ltrim($value, '/');
            }
        }

        $attributes = $this->getAttributes();

        if (!empty($value) && !empty($attributes)) {
            return [$value => $attributes];
        } else {
            return $value;
        }
    }

    /**
     * @return bool
     */
    public function isExternal(): bool
    {
        return $this->external;
    }
}
