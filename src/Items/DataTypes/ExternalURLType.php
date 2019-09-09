<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items\DataTypes;

/**
 * Class ExternalURLType
 *
 * @package Wszetko\Sitemap\Items\DataTypes
 */
class ExternalURLType extends URLType
{
    /**
     * Determine if URL CAN be external
     *
     * @var bool
     */
    protected $external = true;
}
