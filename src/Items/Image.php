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

/**
 * Class Image.
 *
 * @package Wszetko\Sitemap\Items
 *
 * @method setLoc($vloc)
 * @method getLoc()
 * @method setCaption($caption)
 * @method getCaption()
 * @method setGeoLocation($geoLocation)
 * @method getGeoLocation()
 * @method setTitle($title)
 * @method getTitle()
 * @method setLicense($licence)
 * @method getLicense()
 */
class Image extends Extension
{
    /**
     * Name of Namescapce.
     */
    public const NAMESPACE_NAME = 'image';

    /**
     * Namespace URL.
     */
    public const NAMESPACE_URL = 'http://www.google.com/schemas/sitemap-image/1.1';

    /**
     * Element name.
     */
    public const ELEMENT_NAME = 'image';

    /**
     * Location.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\URLType
     */
    protected $loc;

    /**
     * The caption of the image.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\StringType
     */
    protected $caption;

    /**
     * The geographic location of the image.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\StringType
     */
    protected $geoLocation;

    /**
     * The title of the image.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\StringType
     */
    protected $title;

    /**
     * A URL to the license of the image.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\ExternalURLType
     */
    protected $license;

    /**
     * Image constructor.
     *
     * @param string $loc
     *
     * @throws \ReflectionException
     * @throws \Error
     */
    public function __construct(string $loc)
    {
        parent::__construct();

        $this->loc->setRequired(true);
        $this->setLoc($loc);
    }
}
