<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items;

use DateTimeInterface;
use InvalidArgumentException;

/**
 * Class Video
 *
 * @package Wszetko\Sitemap\Items
 */
class Video extends Extension
{
    /**
     * Name of Namescapce
     */
    const NAMESPACE_NAME = 'video';

    /**
     * Namespace URL
     */
    const NAMESPACE_URL = 'http://www.google.com/schemas/sitemap-video/1.1';

    /**
     * URL pointing to an image thumbnail.
     *
     * @var string
     */
    protected $thumbnailLoc;

    /**
     * Title of the video, max 100 characters.
     *
     * @var string
     */
    protected $title;

    /**
     * Description of the video, max 2048 characters.
     *
     * @var string
     */
    protected $description;

    /**
     * URL pointing to the actual media file (mp4).
     *
     * @var string
     */
    protected $contentLoc;

    /**
     * URL pointing to the player file (normally a SWF).
     *
     * @var string|array
     */
    protected $playerLoc;

    /**
     * Indicates whether the video is live.
     *
     * @var string
     */
    protected $live;

    /**
     * Duration of the video in seconds.
     *
     * @var integer
     */
    protected $duration;

    /**
     * String of space delimited platform values.
     *
     * Allowed values are web, mobile, and tv.
     *
     * @var array
     */
    protected $platform;

    /**
     * Does the video require a subscription?
     *
     * @var string
     */
    protected $requiresSubscription;

    /**
     * Information about price
     *
     * @var array
     */
    protected $price;

    /**
     * The currency used for the price.
     *
     * @var string
     */
    protected $currency;

    /**
     * Link to gallery of which this video appears in.
     *
     * @var string
     */
    protected $galleryLoc;

    /**
     * A space-delimited list of countries where the video may or may not be played.
     *
     * @var array
     */
    protected $restriction;

    /**
     * A tag associated with the video.
     *
     * @var array
     */
    protected $tags;

    /**
     * The video's category. For example, cooking.
     *
     * @var string
     */
    protected $category;

    /**
     * No if the video should be available only to users with SafeSearch turned off.
     *
     * @var string
     */
    protected $familyFriendly;

    /**
     * The date the video was first published.
     *
     * @var \DateTimeInterface
     */
    protected $publicationDate;

    /**
     * The number of times the video has been viewed.
     *
     * @var integer
     */
    protected $viewCount;

    /**
     * The video uploader's name. Only one <video:uploader> is allowed per video.
     *
     * @var string|array
     */
    protected $uploader;

    /**
     * The rating of the video. Allowed values are float numbers in the range 0.0 to 5.0.
     *
     * @var float
     */
    protected $rating;

    /**
     * The date after which the video will no longer be available
     *
     * @var \DateTimeInterface
     */
    protected $expirationDate;

    /**
     * Video constructor.
     *
     * @param string $thumbnailLoc
     * @param string $title
     * @param string $description
     */
    public function __construct($thumbnailLoc, $title, $description)
    {
        $this->thumbnailLoc = '/' . ltrim($thumbnailLoc, '/');
        $this->title = mb_substr($title, 0, 100);
        $this->description = mb_substr($description, 0, 2048);
    }

    /**
     * The currency used for the price.
     *
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * The currency used for the price.
     *
     * @param string $currency
     *
     * @return self
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        if (empty($this->getContentLoc()) && empty($this->getPlayerLoc())) {
            throw new InvalidArgumentException('Nor content_loc or player_loc parameter is set.');
        }

        $array = [
            '_namespace' => static::NAMESPACE_NAME,
            '_element' => 'video',
            'video' => [
                'thumbnail_loc' => $this->getThumbnailLoc(),
                'title' => $this->getTitle(),
                'description' => $this->getDescription()
            ]
        ];

        if (!empty($this->getContentLoc())) {
            $array['video']['content_loc'] = $this->getContentLoc();
        }

        if (!empty($this->getPlayerLoc())) {
            if (is_array($this->getPlayerLoc())) {
                $playerLoc = array_key_first($this->getPlayerLoc());
                $array['video']['player_loc'] = [
                    '_attributes' => ['allow_embed' => $this->getPlayerLoc()[$playerLoc]],
                    '_value' => $playerLoc
                ];
            } else {
                $array['video']['player_loc'] = $this->getPlayerLoc();
            }
        }

        if (!empty($this->getDuration())) {
            $array['video']['duration'] = $this->getDuration();
        }

        if (!empty($this->getExpirationDate())) {
            $array['video']['expiration_date'] = $this->getExpirationDate();
        }

        if (!empty($this->getRating())) {
            $array['video']['rating'] = $this->getRating();
        }

        if (!empty($this->getViewCount())) {
            $array['video']['view_count'] = $this->getViewCount();
        }

        if (!empty($this->getPublicationDate())) {
            $array['video']['publication_date'] = $this->getPublicationDate();
        }

        if (!empty($this->getFamilyFriendly())) {
            $array['video']['family_friendly'] = $this->getFamilyFriendly();
        }

        if (!empty($this->getRestriction())) {
            $restriction = $this->getRestriction();
            $relationship = array_key_first($this->getRestriction());
            $countries = $restriction[$relationship];

            $array['video']['restriction'] = [
                '_attributes' => ['relationship' => $relationship],
                '_value' => $countries
            ];
        }

        if (!empty($this->getPlatform())) {
            $platform = $this->getPlatform();
            $relationship = array_key_first($platform);
            $platform = $platform[$relationship];
            $array['video']['platform'] = [
                '_attributes' => ['relationship' => $relationship],
                '_value' => $platform
            ];
        }

        if (!empty($this->getPrice())) {
            $price = $this->getPrice();
            $array['video']['price'] = [
                '_attributes' => ['currency' => $price['currency']],
                '_value' => $price['price']
            ];

            if (isset($price['type'])) {
                $array['video']['price']['_attributes']['type'] = $price['type'];
            }

            if (isset($price['resolution'])) {
                $array['video']['price']['_attributes']['resolution'] = $price['resolution'];
            }
        }

        if (!empty($this->getRequiresSubscription())) {
            $array['video']['requires_subscription'] = $this->getRequiresSubscription();
        }

        if (!empty($this->getUploader())) {
            if (is_array($this->getUploader())) {
                $uploader = array_key_first($this->getUploader());
                $array['video']['uploader'] = [
                    '_attributes' => ['info' => $this->getUploader()[$uploader]],
                    '_value' => $uploader
                ];
            } else {
                $array['video']['uploader'] = $this->getUploader();
            }
        }

        if (!empty($this->getLive())) {
            $array['video']['live'] = $this->getLive();
        }

        if (!empty($this->getTags())) {
            $tags = $this->getTags();
            $array['video']['tag'] = [];

            foreach ($tags as $tag) {
                $array['video']['tag'][] = $tag;
            }
        }

        if (!empty($this->getCategory())) {
            $array['video']['category'] = $this->getCategory();
        }

        if (!empty($this->getGalleryLoc())) {
            $array['video']['gallery_loc'] = $this->getGalleryLoc();
        }

        return $array;
    }

    /**
     * URL pointing to the actual media file (mp4).
     *
     * @return string
     */
    public function getContentLoc(): ?string
    {
        if ($contentLoc = \Wszetko\Sitemap\Helpers\Url::normalizeUrl($this->getDomain() . $this->contentLoc)) {
            return $contentLoc;
        }

        return null;
    }

    /**
     * URL pointing to the actual media file (mp4).
     *
     * @param string $contentLoc
     *
     * @return self
     */
    public function setContentLoc(string $contentLoc): self
    {
        $this->contentLoc = '/' . ltrim($contentLoc, '/');

        return $this;
    }

    /**
     * Player location information
     *
     * @return string|array
     */
    public function getPlayerLoc()
    {
        return $this->playerLoc;
    }

    /**
     * @param string $playerLoc
     * @param mixed  $allowEmbed
     *
     * @return self
     */
    public function setPlayerLoc(string $playerLoc, $allowEmbed = null): self
    {
        $playerLoc = '/' . ltrim($playerLoc, '/');

        if (!empty($playerLoc)) {
            if ($allowEmbed !== null) {
                $this->playerLoc = [$playerLoc => $allowEmbed];
            } else {
                $this->playerLoc = $playerLoc;
            }
        }

        return $this;
    }

    /**
     * URL pointing to an image thumbnail.
     *
     * @return string
     */
    public function getThumbnailLoc(): string
    {
        if (!($thumbnailLoc = \Wszetko\Sitemap\Helpers\Url::normalizeUrl($this->getDomain() . $this->thumbnailLoc))) {
            throw new InvalidArgumentException('Invalid thumbnail location parameter');
        }

        return $thumbnailLoc;
    }

    /**
     * Title of the video, max 100 characters.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Description of the video, max 2048 characters.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Duration of the video in seconds.
     *
     * @return integer|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * Duration of the video in seconds.
     *
     * @param integer $duration
     *
     * @return self
     */
    public function setDuration($duration): self
    {
        if ($duration >= 1 && $duration <= 28800) {
            $this->duration = $duration;
        }

        return $this;
    }

    /**
     * The date after which the video will no longer be available.
     *
     * @return string|null
     */
    public function getExpirationDate(): ?string
    {
        if (!empty($this->expirationDate)) {
            if ($this->expirationDate->format('H') == 0 &&
                $this->expirationDate->format('i') == 0 &&
                $this->expirationDate->format('s') == 0) {
                return $this->expirationDate->format("Y-m-d");
            } else {
                return $this->expirationDate->format(DateTimeInterface::W3C);
            }
        } else {
            return null;
        }
    }

    /**
     * The date after which the video will no longer be available.
     *
     * @param \DateTimeInterface $expirationDate
     *
     * @return self
     */
    public function setExpirationDate(DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * The rating of the video. Allowed values are float numbers in the range 0.0 to 5.0.
     *
     * @return float|null
     */
    public function getRating(): ?float
    {
        return $this->rating;
    }

    /**
     * The rating of the video. Allowed values are float numbers in the range 0.0 to 5.0.
     *
     * @param float $rating
     *
     * @return self
     */
    public function setRating(float $rating): self
    {
        if ($rating >= 0 && $rating <= 5) {
            $this->rating = round($rating, 1);
        }

        return $this;
    }

    /**
     * The number of times the video has been viewed.
     *
     * @return integer|null
     */
    public function getViewCount(): ?int
    {
        return $this->viewCount;
    }

    /**
     * The number of times the video has been viewed.
     *
     * @param integer $viewCount
     *
     * @return self
     */
    public function setViewCount(int $viewCount): self
    {
        $this->viewCount = $viewCount;

        return $this;
    }

    /**
     * The date the video was first published, in W3C format.
     *
     * @return string|null
     */
    public function getPublicationDate(): ?string
    {
        if (!empty($this->publicationDate)) {
            if ($this->publicationDate->format('H') == 0 &&
                $this->publicationDate->format('i') == 0 &&
                $this->publicationDate->format('s') == 0) {
                return $this->publicationDate->format("Y-m-d");
            } else {
                return $this->publicationDate->format(DateTimeInterface::W3C);
            }
        } else {
            return null;
        }
    }

    /**
     * The date the video was first published, in W3C format.
     *
     * @param \DateTimeInterface $publicationDate
     *
     * @return self
     */
    public function setPublicationDate(DateTimeInterface $publicationDate): self
    {
        $this->publicationDate = $publicationDate;
        return $this;
    }

    /**
     * No if the video should be available only to users with SafeSearch turned off.
     *
     * @return string|null
     */
    public function getFamilyFriendly(): ?string
    {
        return $this->familyFriendly;
    }

    /**
     * No if the video should be available only to users with SafeSearch turned off.
     *
     * @param bool $familyFriendly
     *
     * @return self
     */
    public function setFamilyFriendly(bool $familyFriendly): self
    {
        $this->familyFriendly = $familyFriendly ? 'Yes' : 'No';

        return $this;
    }

    /**
     * A space-delimited list of countries where the video may or may not be played.
     *
     * @return array|null
     */
    public function getRestriction(): ?array
    {
        return $this->restriction;
    }

    /**
     * A space-delimited list of countries where the video may or may not be played.
     *
     * @param string $relationship
     * @param string $countries
     *
     * @return self
     */
    public function setRestriction(string $relationship, string $countries): self
    {
        preg_match_all("/^(?'countries'[A-Z]{2}( +[A-Z]{2})*)?$/", $countries, $matches);

        if ($this->validRelationship($relationship) && !empty($matches['countries'])) {
            $this->restriction = [$relationship => $countries];
        }

        return $this;
    }

    /**
     * String of space delimited platform values.
     *
     * Allowed values are web, mobile, and tv.
     *
     * @return array|null
     */
    public function getPlatform(): ?array
    {
        return $this->platform;
    }

    /**
     * String of space delimited platform values.
     *
     * Allowed values are web, mobile, and tv.
     *
     * @param string $relationship
     * @param string $platform
     *
     * @return self
     */
    public function setPlatform(string $relationship, string $platform): self
    {
        preg_match_all("/^(?'platform'(web|mobile|tv)( (web|mobile|tv))*)?/", $platform, $matches);

        if ($this->validRelationship($relationship) && !empty($matches['platform'])) {
            $this->platform = [$relationship => $platform];
        }

        return $this;
    }

    /**
     * The price to download or view the video in ISO 4217 format.
     *
     * @return array|null
     */
    public function getPrice(): ?array
    {
        return $this->price;
    }

    /**
     * The price to download or view the video in ISO 4217 format.
     *
     * @param float  $price
     * @param string $currency
     * @param string $type
     * @param string $resolution
     *
     * @return self
     */
    public function setPrice(float $price, string $currency, string $type = '', string $resolution = ''): self
    {
        preg_match_all("/^(?'currency'[A-Z]{3})$/", $currency, $matches);

        if (!empty($matches['currency'])) {
            $data = [];
            $data['price'] = round($price, 2);
            $data['currency'] = $currency;

            if (in_array($type, ['rent', 'RENT', 'purchase', 'PURCHASE'])) {
                $data['type'] = $type;
            }

            if (in_array($resolution, ['sd', 'hd', 'SD', 'HD'])) {
                $data['resolution'] = $resolution;
            }

            $this->price = $data;
        }

        return $this;
    }

    /**
     * Does the video require a subscription?
     *
     * @return string|null
     */
    public function getRequiresSubscription(): ?string
    {
        return $this->requiresSubscription;
    }

    /**
     * Does the video require a subscription?
     *
     * @param boolean $requiresSubscription
     *
     * @return self
     */
    public function setRequiresSubscription(bool $requiresSubscription): self
    {
        $this->requiresSubscription = $requiresSubscription ? 'Yes' : 'No';

        return $this;
    }

    /**
     * The video uploader's name. Only one <video:uploader> is allowed per video.
     *
     * @return string|array
     */
    public function getUploader()
    {
        if (is_array($this->uploader)) {
            return [array_key_first($this->uploader) => $this->getDomain() . $this->uploader[array_key_first($this->uploader)]];
        }

        return $this->uploader;
    }

    /**
     * The video uploader's name. Only one <video:uploader> is allowed per video.
     *
     * @param string $uploader
     * @param string $info
     *
     * @return self
     */
    public function setUploader(string $uploader, string $info = ''): self
    {
        if (!empty($info)) {
            $this->uploader = [$uploader => $info];
        } else {
            $this->uploader = $uploader;
        }

        return $this;
    }

    /**
     * Indicates whether the video is live.
     *
     * @return string|null
     */
    public function getLive(): ?string
    {
        return $this->live;
    }

    /**
     * Indicates whether the video is live.
     *
     * @param boolean $live
     *
     * @return self
     */
    public function setLive(bool $live): self
    {
        $this->live = $live ? 'Yes' : 'No';

        return $this;
    }

    /**
     * A tag associated with the video.
     *
     * @return array|null
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /**
     * A tag associated with the video.
     *
     * @param array $tags
     *
     * @return self
     */
    public function setTags(array $tags): self
    {
        $this->tags = array_slice($tags, 0, 32);

        return $this;
    }

    /**
     * The video's category. For example, cooking.
     *
     * @return string|null
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * The video's category. For example, cooking.
     *
     * @param string $category
     *
     * @return self
     */
    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Link to gallery of which this video appears in.
     *
     * @return string|null
     */
    public function getGalleryLoc(): ?string
    {
        return $this->galleryLoc;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    private function validRelationship(string $value): bool
    {
        $accepted = ['allow', 'deny'];

        return (bool)in_array($value, $accepted);
    }

    /**
     * Link to gallery of which this video appears in.
     *
     * @param string $galleryLoc
     *
     * @return self
     */
    public function setGalleryLoc($galleryLoc): self
    {
        if (\Wszetko\Sitemap\Helpers\Url::normalizeUrl($galleryLoc)) {
            $this->galleryLoc = $galleryLoc;
        }

        return $this;
    }
}
