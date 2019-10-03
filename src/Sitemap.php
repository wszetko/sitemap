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

namespace Wszetko\Sitemap;

use Exception;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use Wszetko\Sitemap\Drivers\DataCollectors\AbstractDataCollector;
use Wszetko\Sitemap\Drivers\Output\OutputXMLWriter;
use Wszetko\Sitemap\Helpers\Directory;
use Wszetko\Sitemap\Interfaces\DataCollector;
use Wszetko\Sitemap\Interfaces\XML;
use Wszetko\Sitemap\Traits\Domain;

/**
 * Sitemap
 * This class used for generating Google Sitemap files.
 *
 * @package    Sitemap
 *
 * @author     Paweł Kłopotek-Główczewski <pawelkg@pawelkg.com>
 * @copyright  2019 Paweł Kłopotek-Głowczewski (https://pawelkg.com/)
 * @license    https://opensource.org/licenses/MIT MIT License
 *
 * @see       https://github.com/wszetko/sitemap
 */
class Sitemap
{
    use Domain;

    /**
     * Avaliable values for changefreq tag.
     *
     * @var array
     */
    public const CHANGEFREQ = [
        'always',
        'hourly',
        'daily',
        'weekly',
        'monthly',
        'yearly',
        'never',
    ];

    /**
     * Extension for sitemap file.
     *
     * @var string
     */
    public const EXT = '.xml';

    /**
     * Extension for gzipped sitemap file.
     *
     * @var string
     */
    public const GZ_EXT = '.xml.gz';

    /**
     * URL to Sitemap Schema.
     *
     * @var string
     */
    public const SCHEMA = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    /**
     * Limit of items in Sitemap files.
     *
     * @var int
     */
    public const ITEM_PER_SITEMAP = 50000;

    /**
     * Limit of Sitmeaps in SitemapsIndex.
     *
     * @var int
     */
    public const SITEMAP_PER_SITEMAPINDEX = 1000;

    /**
     * Limit of single files size.
     *
     * @var int
     */
    public const SITEMAP_MAX_SIZE = 52000000; // ~49,6MB - to have some limit to close file

    /**
     * Path on disk to public directory.
     *
     * @var string
     */
    private $publicDirectory = '';

    /**
     * Path related to public directory to dir where sitemaps will be.
     *
     * @var string
     */
    private $sitemapsDirectory = '';

    /**
     * Path to temporary directory.
     *
     * @var string
     */
    private $sitemapTempDirectory = '';

    /**
     * Default filename for sitemap file.
     *
     * @var string
     */
    private $defaultFilename = 'sitemap';

    /**
     * Name of index file.
     *
     * @var string
     */
    private $indexFilename = 'index';

    /**
     * DataCollector instance.
     *
     * @var null|DataCollector
     */
    private $dataCollector = null;

    /**
     * Use compression.
     *
     * @var bool
     */
    private $useCompression = false;

    /**
     * XML Writer object.
     *
     * @var null|XML
     */
    private $xml = null;

    /**
     * Separator to be used in Sitemap filenames.
     *
     * @var string
     */
    private $separator = '-';

    /**
     * Class constructor.
     *
     * @param string $domain
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $domain = null)
    {
        if (null !== $domain) {
            $this->setDomain($domain);
        }
    }

    /**
     * Add URL to specific group.
     *
     * @param Items\Url   $item
     * @param null|string $group
     *
     * @throws \Exception
     *
     * @return \Wszetko\Sitemap\Sitemap
     */
    public function addItem(Items\Url $item, ?string $group = null): self
    {
        if (is_string($group)) {
            $group = preg_replace('/\W+/', '', $group);
        }

        if ('' === $group || null === $group) {
            $group = $this->getDefaultFilename();
        }

        $group = mb_strtolower($group);
        $item->setDomain($this->getDomain());
        $this->getDataCollector()->add($item, $group);

        return $this;
    }

    /**
     * Add multiple URLs to specific group.
     *
     * @param array       $items
     * @param null|string $group
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function addItems(array $items, ?string $group = null): self
    {
        foreach ($items as $item) {
            $this->addItem($item, $group);
        }

        return $this;
    }

    /**
     * Return DataCollecotr Object.
     *
     * @return DataCollector
     *
     * @throws \Exception
     */
    public function getDataCollector(): DataCollector
    {
        if (null === $this->dataCollector) {
            throw new Exception('DataCollector is not set.');
        }

        return $this->dataCollector;
    }

    /**
     * Set DataCollector driver with configuration.
     *
     * @param string $driver
     * @param array  $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \Wszetko\Sitemap\Sitemap
     */
    public function setDataCollector(string $driver, $config = []): self
    {
        if (class_exists($driver)) {
            $dataCollector = new $driver($config);

            if ($dataCollector instanceof AbstractDataCollector) {
                $this->dataCollector = $dataCollector;
            }
        }

        if (null === $this->dataCollector) {
            throw new InvalidArgumentException($driver . ' data collector does not exists.');
        }

        return $this;
    }

    /**
     * Return XML driver object.
     *
     * @return XML
     *
     * @throws \Exception
     */
    public function getXml(): XML
    {
        if (null === $this->xml) {
            throw new Exception('XML writer class is not set.');
        }

        return $this->xml;
    }

    /**
     * Set XML driver with configuration.
     *
     * @param string $driver
     * @param array  $config
     *
     * @return \Wszetko\Sitemap\Sitemap
     *
     * @throws \Exception
     */
    public function setXml(string $driver, array $config = []): self
    {
        if (class_exists($driver)) {
            if (!isset($config['domain'])) {
                $config['domain'] = $this->getDomain();
            }

            $xml = new $driver($config);

            if ($xml instanceof XML) {
                $this->xml = $xml;
            }
        }

        if (null === $this->xml) {
            throw new Exception('XML writer class is not set.');
        }

        return $this;
    }

    /**
     * Generate sitemaps, sitemaps index and publish them.
     *
     * @throws Exception
     */
    public function generate(): void
    {
        if ('' === $this->getDomain()) {
            throw new Exception('Domain is not set.');
        }

        if (null === $this->xml) {
            $this->setXml(OutputXMLWriter::class, ['domain' => $this->getDomain()]);
        }

        Directory::removeDir($this->getTempDirectory());
        $this->getXml()->setWorkDir($this->getSitepamsTempDirectory());
        $sitemaps = $this->generateSitemaps();
        $this->getXml()->setWorkDir($this->getTempDirectory());
        $this->generateSitemapsIndex($sitemaps);
        $this->publishSitemap();
    }

    /**
     * Generates sitemaps based on collected data.
     *
     * @throws Exception
     *
     * @return array
     */
    public function generateSitemaps(): array
    {
        if (0 == $this->getDataCollector()->getCount()) {
            return [];
        }

        $groups = $this->getDataCollector()->getGroups();
        $currentGroup = 0;
        $files = [];

        foreach ($groups as $group) {
            $groupNo = 0;
            $filesInGroup = 0;
            ++$currentGroup;

            if ($this->getDataCollector()->getGroupCount($group) > 0) {
                $this->getXml()->openSitemap(
                    $group . $this->getSeparator() . $groupNo . self::EXT,
                    $this->getDataCollector()->getExtensions()
                );
                $files[$group . $this->getSeparator() . $groupNo . self::EXT] = null;

                while ($element = $this->getDataCollector()->fetch($group)) {
                    $this->getXml()->addUrl($element);
                    ++$filesInGroup;

                    if (isset($element['lastmod'])) {
                        if ($files[$group . $this->getSeparator() . $groupNo . self::EXT]) {
                            if (
                                strtotime($element['lastmod']) >
                                    strtotime($files[$group . $this->getSeparator() . $groupNo . self::EXT])
                            ) {
                                $files[$group . $this->getSeparator() . $groupNo . self::EXT] = $element['lastmod'];
                            }
                        } else {
                            $files[$group . $this->getSeparator() . $groupNo . self::EXT] = $element['lastmod'];
                        }
                    }

                    // self::SITEMAP_MAX_SIZE - 20 for buffer for close tag
                    if (
                        $filesInGroup >= self::ITEM_PER_SITEMAP ||
                        $this->getXml()->getSitemapSize() >= (self::SITEMAP_MAX_SIZE - 20)
                    ) {
                        $this->getXml()->closeSitemap();

                        if (!$this->getDataCollector()->isLast($group)) {
                            ++$groupNo;
                            $filesInGroup = 0;
                            $this->getXml()->openSitemap(
                                $group . $this->getSeparator() . $groupNo . self::EXT,
                                $this->getDataCollector()->getExtensions()
                            );
                            $files[$group . $this->getSeparator() . $groupNo . self::EXT] = null;
                        }
                    }
                }

                $this->getXml()->closeSitemap();
            }
        }

        if ($this->isUseCompression() && [] !== $files) {
            $this->compressFiles($this->getSitepamsTempDirectory(), $files);
        }

        return $files;
    }

    /**
     * Generates sitemap index for generated sitemaps.
     *
     * @param array $sitemaps
     *
     * @throws Exception
     *
     * @return array
     */
    public function generateSitemapsIndex(array $sitemaps): array
    {
        if (0 === count($sitemaps)) {
            return [];
        }

        $counter = 0;
        $file = $this->getIndexFilename() . self::EXT;
        $files = [$file => null];
        $this->getXml()->openSitemapIndex($file);
        $lastItem = array_key_last($sitemaps);

        foreach ($sitemaps as $sitemap => $lastmod) {
            $this->getXml()->addSitemap((string) $this->getDomain() . '/' . ltrim(str_replace(
                $this->getPublicDirectory(),
                '',
                $this->getSitemapsDirectory()
            ), DIRECTORY_SEPARATOR) . '/' . $sitemap, $lastmod);
            ++$counter;

            if ($counter >= self::SITEMAP_PER_SITEMAPINDEX) {
                $this->getXml()->closeSitemapIndex();
                $counter = 0;
                $filesCount = count($files);

                if ($sitemap != $lastItem) {
                    $file = $this->getIndexFilename() . $this->getSeparator() . $filesCount . self::EXT;
                    $files[$file] = null;
                    $this->getXml()->openSitemapIndex($file);
                }
            }
        }

        $this->getXml()->closeSitemapIndex();

        if ($this->isUseCompression() && [] !== $files) {
            $this->compressFiles($this->getTempDirectory(), $files);
        }

        return $files;
    }

    /**
     * Gzip sitemap files and put them in specified directory.
     *
     * @param string $dir
     * @param array  $files
     *
     * @throws Exception
     *
     * @return void
     */
    private function compressFiles(string $dir, array &$files): void
    {
        if (!extension_loaded('zlib')) {
            throw new Exception('Extension zlib is not loaded.');
        }

        $newFiles = [];

        foreach ($files as $file => $lastmod) {
            $source = $dir . DIRECTORY_SEPARATOR . $file;
            $gzFile = mb_substr($file, 0, mb_strlen($file) - 4) . self::GZ_EXT;
            $output = $dir . DIRECTORY_SEPARATOR . $gzFile;
            $out = gzopen($output, 'wb9');
            $in = fopen($source, 'rb');

            if (false === $out) {
                throw new Exception('Can\'t create GZip archive.');
            }

            if (false === $in) {
                throw new Exception('Can\'t open xml file.');
            }

            while (!feof($in)) {
                $content = fread($in, 524288);

                if (false !== $content) {
                    gzwrite($out, $content);
                }
            }

            fclose($in);
            gzclose($out);
            unlink($source);
            $newFiles[$gzFile] = $lastmod;
        }

        $files = $newFiles;
    }

    /**
     * Copy generated sitemaps to their destination.
     *
     * @throws \Exception
     *
     * @return void
     */
    private function publishSitemap(): void
    {
        $this->clearPreviousSitemaps();
        $dir = new RecursiveDirectoryIterator($this->getTempDirectory());
        $iterator = new RecursiveIteratorIterator($dir);
        $files = new RegexIterator(
            $iterator,
            "/^(?'path'(([a-zA-Z]:)|((\\\\|\\/){1,2}\\w+)?)((\\\\|\\/)(\\w[\\w ]*.*))+({$this->getExt()}){1})$/",
            RegexIterator::GET_MATCH
        );
        $fileList = [];

        foreach ($files as $file) {
            if (isset($file['path'])) {
                $fileList[] = $file['path'];
            }
        }

        $currentFile = 0;

        foreach ($fileList as $file) {
            ++$currentFile;
            $destination = str_replace($this->getTempDirectory(), $this->getPublicDirectory(), $file);
            rename($file, $destination);
        }

        Directory::removeDir($this->getTempDirectory());
    }

    /**
     * Remove previous sitemap files.
     *
     * @throws \Exception
     *
     * @return void
     */
    private function clearPreviousSitemaps(): void
    {
        $sitemapDir = str_replace($this->getPublicDirectory(), '', $this->getSitemapsDirectory());

        if ('' !== $sitemapDir) {
            Directory::removeDir($this->getSitemapsDirectory());
        }

        $publicDir = scandir($this->getPublicDirectory());

        if (is_array($publicDir)) {
            foreach ($publicDir as $file) {
                if (
                    1 === preg_match(
                        '/^(' . $this->getIndexFilename() . ')((-)[\d]+)?(' . self::EXT . '|' . self::GZ_EXT . ')$/',
                        $file
                    )
                ) {
                    unlink($this->getPublicDirectory() . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
    }

    /**
     * Get filename of sitemap index file.
     *
     * @return string
     */
    public function getIndexFilename(): string
    {
        return $this->indexFilename;
    }

    /**
     * Set filename of sitemap index file.
     *
     * @param string $indexFilename
     *
     * @return \Wszetko\Sitemap\Sitemap
     */
    public function setIndexFilename(string $indexFilename): self
    {
        $this->indexFilename = $indexFilename;

        return $this;
    }

    /**
     * Get public directory path.
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getPublicDirectory(): string
    {
        if ('' === $this->publicDirectory) {
            throw new Exception('Public directory is not set.');
        }

        return $this->publicDirectory;
    }

    /**
     * Set public directory path.
     *
     * @param string $publicDirectory
     *
     * @throws Exception
     *
     * @return \Wszetko\Sitemap\Sitemap
     */
    public function setPublicDirectory(string $publicDirectory): self
    {
        $this->publicDirectory = Directory::checkDirectory($publicDirectory);

        return $this;
    }



    /**
     * Get sitemaps directory path.
     *
     * @throws \Exception
     *
     * @return string
     */
    public function getSitemapsDirectory(): string
    {
        if ('' === $this->sitemapsDirectory) {
            $this->setSitemapsDirectory('');
        }

        return $this->sitemapsDirectory;
    }

    /**
     * Set sitemaps directory path.
     *
     * @param string $sitemapsDirectory
     *
     * @return \Wszetko\Sitemap\Sitemap
     * @throws \Exception
     */
    public function setSitemapsDirectory(string $sitemapsDirectory): self
    {
        $this->sitemapsDirectory = Directory::checkDirectory(
            $this->getPublicDirectory() . DIRECTORY_SEPARATOR . $sitemapsDirectory
        );

        return $this;
    }

    /**
     * Get temporary directory path.
     *
     * @throws \Exception
     *
     * @return string
     */
    public function getTempDirectory(): string
    {
        if (null === $this->sitemapTempDirectory || '' == $this->sitemapTempDirectory) {
            $hash = md5(microtime());
            $this->setTempDirectory(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sitemap' . $hash);
        }

        return $this->sitemapTempDirectory;
    }

    /**
     * Set temporary directory path.
     *
     * @param string $tempDirectory
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setTempDirectory(string $tempDirectory): self
    {
        $this->sitemapTempDirectory = Directory::checkDirectory($tempDirectory);

        return $this;
    }

    /**
     * Get temporary sitemaps directory path.
     *
     * @throws \Exception
     *
     * @return string
     */
    public function getSitepamsTempDirectory(): string
    {
        $sitemapsDirectory = str_replace($this->getPublicDirectory(), '', $this->getSitemapsDirectory());

        return Directory::checkDirectory($this->getTempDirectory() . DIRECTORY_SEPARATOR . $sitemapsDirectory);
    }

    /**
     * Get separator for filenames.
     *
     * @param string $separator
     *
     * @return \Wszetko\Sitemap\Sitemap
     */
    public function setSeparator(string $separator): self
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * Set separator for filenames.
     *
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * Set if sitemaps files should be GZiped.
     *
     * Set whether to use compression or not.
     *
     * @param bool $useCompression
     *
     * @return \Wszetko\Sitemap\Sitemap
     */
    public function setUseCompression(bool $useCompression): self
    {
        if ($useCompression && extension_loaded('zlib')) {
            $this->useCompression = $useCompression;
        }

        return $this;
    }

    /**
     * Checi fi sitemaps files should be GZiped.
     *
     * Check if compression is used.
     *
     * @return bool
     */
    public function isUseCompression(): bool
    {
        return $this->useCompression;
    }

    /**
     * Set default filename for sitemap file.
     *
     * @param string $defaultFilename
     *
     * @return \Wszetko\Sitemap\Sitemap
     */
    public function setDefaultFilename(string $defaultFilename): self
    {
        $this->defaultFilename = $defaultFilename;

        return $this;
    }

    /**
     * Get default filename for sitemap file.
     *
     * @return string
     */
    public function getDefaultFilename(): string
    {
        return $this->defaultFilename;
    }

    /**
     * Get extension for sitemap files.
     *
     * @return string
     */
    private function getExt(): string
    {
        if ($this->isUseCompression()) {
            return self::GZ_EXT;
        }

        return self::EXT;
    }
}
