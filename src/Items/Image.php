<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items;

/**
 * Class Image
 *
 * @package Wszetko\Sitemap\Items
 */
class Image extends Extension
{
    /**
     * Name of Namescapce
     */
    const NAMESPACE_NAME = 'image';

    /**
     * Namespace URL
     */
    const NAMESPACE_URL = 'http://www.google.com/schemas/sitemap-image/1.1';

    /**
     * Location
     *
     * @var string
     */
    protected $loc;

    /**
     * The caption of the image.
     *
     * @var string
     */
    protected $caption;

    /**
     * The geographic location of the image.
     *
     * @var string
     */
    protected $geoLocation;

    /**
     * The title of the image.
     *
     * @var string
     */
    protected $title;

    /**
     * A URL to the license of the image.
     *
     * @var string
     */
    protected $license;

    /**
     * Image constructor
     *
     * @param string $loc
     */
    public function __construct(string $loc)
    {
        $this->loc = '/' . ltrim($loc, '/');
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [
            '_namespace' => static::NAMESPACE_NAME,
            '_element' => 'image',
            'image' => [
                'loc' => $this->getLoc()
            ]
        ];

        if ($this->getCaption()) {
            $array['image']['caption'] = $this->getCaption();
        }

        if ($this->getGeoLocation()) {
            $array['image']['geo_location'] = $this->getGeoLocation();
        }

        if ($this->getTitle()) {
            $array['image']['title'] = $this->getTitle();
        }

        if ($this->getLicense()) {
            $array['image']['license'] = $this->getLicense();
        }

        return $array;
    }

    /**
     * Location (URL).
     *
     * @return string
     */
    public function getLoc()
    {
        return $this->getDomain() . $this->loc;
    }

    /**
     * The caption of the image.
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Set the caption of the image.
     *
     * @param string $caption
     *
     * @return $this
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * The geographic location of the image.
     *
     * @return string
     */
    public function getGeoLocation()
    {
        return $this->geoLocation;
    }

    /**
     * Set the geographic location of the image.
     *
     * @param string $geoLocation
     *
     * @return self
     */
    public function setGeoLocation($geoLocation): self
    {
        $this->geoLocation = $geoLocation;

        return $this;
    }

    /**
     * The title of the image.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the title of the image.
     *
     * @param string $title
     *
     * @return self
     */
    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * A URL to the license of the image.
     *
     * @return string
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * Set a URL to the license of the image.
     *
     * @param string $license
     *
     * @return self
     */
    public function setLicense($license): self
    {
        if ($license = \Wszetko\Sitemap\Helpers\Url::normalizeUrl($license)) {
            $this->license = $license;
        } else {
            $this->license = null;
        }

        return $this;
    }
}
