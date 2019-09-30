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

use InvalidArgumentException as InvalidArgumentException;
use Wszetko\Sitemap\Traits\DateTime;
use Wszetko\Sitemap\Traits\IsAssoc;

/**
 * Class Video.
 *
 * @todo    : add support for content_segment_loc tag
 * @todo    : add support for tvshow tag
 * @todo    : add support for id tag
 *
 * @package Wszetko\Sitemap\Items
 *
 * @method setThumbnailLoc($thumbnail)
 * @method getThumbnailLoc()
 * @method setTitle($title)
 * @method getTitle()
 * @method setDescription($description)
 * @method getDescription()
 * @method setRating($rating)
 * @method getRating()
 * @method setViewCount($viewCount)
 * @method getViewCount()
 * @method setExpirationDate($expirationDate)
 * @method getExpirationDate()
 * @method setPublicationDate($publicationDate)
 * @method getPublicationDate()
 * @method setDuration($duration)
 * @method getDuration()
 * @method setGalleryLoc($galleryLoc)
 * @method getGalleryLoc()
 * @method setCategory($category)
 * @method getCategory()
 * @method setLive($live)
 * @method getLive()
 * @method setRequiresSubscription($subscription)
 * @method getRequiresSubscription()
 * @method setFamilyFriendly($familyFriendly)
 * @method getFamilyFriendly()
 * @method setContentLoc($contentLoc)
 * @method getContentLoc()
 * @method setPlayerLoc($player, $allow_embed = null, $autoplay = null)
 * @method getPlayerLoc()
 * @method addTag($tags)
 * @method setTag($tag)
 * @method getTag()
 * @method setUploader($uploader, $info = null)
 * @method getUploader()
 * @method setRestriction($countries, $relationship)
 * @method getRestriction()
 * @method setPlatform($platform, $relationship)
 * @method getPlatform()
 * @method setPrice($price, $currency, $type = null, $resolution = null)
 * @method getPrice()
 */
class Video extends Extension
{
    use DateTime;
    use IsAssoc;

    /**
     * Name of Namescapce.
     */
    public const NAMESPACE_NAME = 'video';

    /**
     * Namespace URL.
     */
    public const NAMESPACE_URL = 'http://www.google.com/schemas/sitemap-video/1.1';

    /**
     * Element name.
     */
    public const ELEMENT_NAME = 'video';

    /**
     * URL pointing to an image thumbnail.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\URLType
     */
    protected $thumbnailLoc;

    /**
     * Title of the video, max 100 characters.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\StringType
     */
    protected $title;

    /**
     * Description of the video, max 2048 characters.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\StringType
     */
    protected $description;

    /**
     * URL pointing to the actual media file (mp4).
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\URLType
     */
    protected $contentLoc;

    /**
     * URL pointing to the player file (normally a SWF).
     *
     * @attribute allow_embed
     * @attributeDataType \Wszetko\Sitemap\Items\DataTypes\YesNoType
     * @attribute autoplay
     * @attributeDataType \Wszetko\Sitemap\Items\DataTypes\StringType
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\URLType
     */
    protected $playerLoc;

    /**
     * Indicates whether the video is live.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\YesNoType
     */
    protected $live;

    /**
     * Duration of the video in seconds.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\IntegerType
     */
    protected $duration;

    /**
     * String of space delimited platform values.
     * Allowed values are web, mobile, and tv.
     *
     * @attribute relationship
     * @attributeDataType \Wszetko\Sitemap\Items\DataTypes\StringType
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\StringType
     */
    protected $platform;

    /**
     * Does the video require a subscription?
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\YesNoType
     */
    protected $requiresSubscription;

    /**
     * @attribute currency
     * @attributeDataType \Wszetko\Sitemap\Items\DataTypes\StringType
     * @attribute type
     * @attributeDataType \Wszetko\Sitemap\Items\DataTypes\StringType
     * @attribute resolution
     * @attributeDataType \Wszetko\Sitemap\Items\DataTypes\StringType
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\FloatType
     */
    protected $price;

    /**
     * Link to gallery of which this video appears in.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\URLType
     */
    protected $galleryLoc;

    /**
     * A space-delimited list of countries where the video may or may not be played.
     *
     * @attribute relationship
     * @attributeDataType \Wszetko\Sitemap\Items\DataTypes\StringType
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\StringType
     */
    protected $restriction;

    /**
     * A tag associated with the video.
     *
     * @dataType \Wszetko\Sitemap\Items\DataTypes\StringType
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\ArrayType
     */
    protected $tag;

    /**
     * The video's category. For example, cooking.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\StringType
     */
    protected $category;

    /**
     * No if the video should be available only to users with SafeSearch turned off.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\YesNoType
     */
    protected $familyFriendly;

    /**
     * The date the video was first published.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\DateTimeType
     */
    protected $publicationDate;

    /**
     * The number of times the video has been viewed.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\IntegerType
     */
    protected $viewCount;

    /**
     * The video uploader's name. Only one <video:uploader> is allowed per video.
     *
     * @attribute info
     * @attributeDataType \Wszetko\Sitemap\Items\DataTypes\URLType
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\StringType
     */
    protected $uploader;

    /**
     * The rating of the video. Allowed values are float numbers in the range 0.0 to 5.0.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\FloatType
     */
    protected $rating;

    /**
     * The date after which the video will no longer be available.
     *
     * @var \Wszetko\Sitemap\Items\DataTypes\DateTimeType
     */
    protected $expirationDate;

    /**
     * Video constructor.
     *
     * @param string $thumbnailLoc
     * @param string $title
     * @param string $description
     *
     * @throws InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Error
     */
    public function __construct($thumbnailLoc, $title, $description)
    {
        parent::__construct();
        $this->setUpValues();
        $this->setThumbnailLoc($thumbnailLoc);
        $this->setTitle($title);
        $this->setDescription($description);
    }

    /**
     * @return array
     *
     * @throws \InvalidArgumentException
     * @throws \Error
     */
    public function toArray(): array
    {
        if (
            (null === $this->getContentLoc() ||
                '' === $this->getContentLoc()) &&
            (null === $this->getPlayerLoc() ||
                '' === $this->getPlayerLoc())
        ) {
            throw new InvalidArgumentException('Nor content_loc or player_loc parameter is set.');
        }

        return parent::toArray();
    }

    private function setUpValues(): void
    {
        $this->thumbnailLoc
            ->setRequired(true)
        ;
        $this->title
            ->setRequired(true)
            ->setMinLength(1)
            ->setMaxLength(100)
        ;
        $this->description
            ->setRequired(true)
            ->setMinLength(1)
            ->setMaxLength(2048)
        ;
        $this->rating
            ->setMinValue(0)
            ->setMaxValue(5)
            ->setPrecision(1)
        ;
        $this->viewCount
            ->setMinValue(0)
        ;
        $this->duration
            ->setMinValue(0)
            ->setMaxValue(28800)
        ;
        $this->restriction
            ->setConversion('upper')
            ->setValueRegex("/^(?'countries'[A-Z]{2}( +[A-Z]{2})*)?$/", 'countries')
        ;
        /** @var \Wszetko\Sitemap\Items\DataTypes\StringType $restrictionRelationship */
        $restrictionRelationship = $this->restriction
            ->getAttribute('relationship');
        $restrictionRelationship
            ->setConversion('lower')
            ->setAllowedValues('allow, deny')
        ;

        $this->platform
            ->setConversion('lower')
            ->setValueRegex("/^(?'platform'(web|mobile|tv)( (web|mobile|tv))*)?/", 'platform')
        ;
        /** @var \Wszetko\Sitemap\Items\DataTypes\StringType $platformRelationship */
        $platformRelationship = $this->platform
            ->getAttribute('relationship');
        $platformRelationship
            ->setConversion('lower')
            ->setAllowedValues('allow, deny')
        ;

        $this->price
            ->setMinValue(0)
            ->setPrecision(2)
        ;
        /** @var \Wszetko\Sitemap\Items\DataTypes\StringType $priceCurrency */
        $priceCurrency = $this->price
            ->getAttribute('currency');
        $priceCurrency
            ->setConversion('upper')
            ->setRequired(true)
            ->setValueRegex("/^(?'currency'[A-Z]{3})$/", 'currency')
        ;

        /** @var \Wszetko\Sitemap\Items\DataTypes\StringType $priceType */
        $priceType = $this->price
            ->getAttribute('type');
        $priceType
            ->setConversion('lower')
            ->setAllowedValues('rent, purchase');

        /** @var \Wszetko\Sitemap\Items\DataTypes\StringType $priceResolution */
        $priceResolution = $this->price
            ->getAttribute('resolution');
        $priceResolution
            ->setConversion('upper')
            ->setAllowedValues('SD, HD');

        $this->tag
            ->setMaxElements(32)
        ;
        $this->category
            ->setMaxLength(256)
        ;
    }
}
