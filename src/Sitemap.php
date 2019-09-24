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
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use Wszetko\Sitemap\Drivers\XML\XMLWriter;
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
    public const SITEMAP_MAX_SIZE = 52000000;

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
    private $sitepamsDirectory = '';

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
     * @var DataCollector
     */
    private $dataCollector;

    /**
     * Use compression.
     *
     * @var bool
     */
    private $useCompression = false;

    /**
     * XML Writer object.
     *
     * @var XML
     */
    private $xml;

    /**
     * Separator to be used in Sitemap filenames.
     *
     * @var string
     */
    private $separator = '-'; // ~49,6MB - to have some limit to close file

    /**
     * Construktor.
     *
     * @param string $domain
     */
    public function __construct(string $domain = null)
    {
        if (null !== $domain) {
            $this->setDomain($domain);
        }
    }

    /**
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

        if (empty($group)) {
            $group = $this->getDefaultFilename();
        }

        $group = mb_strtolower($group);
        $item->setDomain($this->getDomain());

        if (null === $this->getDataCollector()) {
            throw new \Exception('DataCollector is not set.');
        }

        $this->getDataCollector()->add($item, $group);

        return $this;
    }

    /**
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
     * Get default filename for sitemap file.
     *
     * @return string
     */
    public function getDefaultFilename(): string
    {
        return $this->defaultFilename;
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
     * Get DataCollecotr Object.
     *
     * @return null|DataCollector
     */
    public function getDataCollector(): ?DataCollector
    {
        return $this->dataCollector;
    }

    /**
     * @param string $driver
     * @param array  $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \Wszetko\Sitemap\Sitemap
     */
    public function setDataCollector(string $driver, $config = []): self
    {
        $driver = '\Wszetko\Sitemap\Drivers\DataCollectors\\' . $driver;

        if (class_exists($driver)) {
            $this->dataCollector = new $driver($config);
        } else {
            throw new \InvalidArgumentException($driver . ' data collector does not exists.');
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function generate(): void
    {
        if ('' === $this->getPublicDirectory()) {
            throw new Exception('Public directory is not set.');
        }

        if ('' === $this->getDomain()) {
            throw new Exception('Domain is not set.');
        }

        if (null === $this->getDataCollector()) {
            throw new Exception('DataCollector is not set.');
        }

        if (null === $this->getXml()) {
            $this->setXml(XMLWriter::class, ['domain' => $this->getDomain()]);
        }

        if (null === $this->getXml()) {
            throw new Exception('XML Driver is not set.');
        }

        $this->removeDir($this->getTempDirectory());
        $this->getXml()->setWorkDir($this->getSitepamsTempDirectory());
        $sitemaps = $this->generateSitemaps();
        $this->getXml()->setWorkDir($this->getTempDirectory());
        $this->generateSitemapsIndex($sitemaps);
        $this->publishSitemap();
    }

    /**
     * @return string
     */
    public function getPublicDirectory(): string
    {
        return $this->publicDirectory;
    }

    /**
     * @param string $publicDirectory
     *
     * @throws Exception
     *
     * @return \Wszetko\Sitemap\Sitemap
     */
    public function setPublicDirectory(string $publicDirectory): self
    {
        if (!($publicDirectory = realpath($publicDirectory))) {
            throw new Exception('Sitemap directory does not exists.');
        }

        $this->publicDirectory = $publicDirectory;

        return $this;
    }

    /**
     * @return null|XML
     */
    public function getXml(): ?XML
    {
        return $this->xml;
    }

    /**
     * @param string $driver
     * @param array  $config
     *
     * @return \Wszetko\Sitemap\Sitemap
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

        return $this;
    }

    /**
     * @throws \Exception
     *
     * @return string
     */
    public function getTempDirectory(): string
    {
        if (empty($this->sitemapTempDirectory)) {
            $hash = md5(microtime());

            if (!is_dir(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sitemap' . $hash)) {
                mkdir(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sitemap' . $hash);
            }

            if ($tempDir = realpath(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sitemap' . $hash)) {
                $this->sitemapTempDirectory = $tempDir;
            } else {
                throw new Exception('Can\'t get temporary directory.');
            }
        }

        return $this->sitemapTempDirectory;
    }

    /**
     * @throws \Exception
     *
     * @return string
     */
    public function getSitepamsTempDirectory(): string
    {
        if (!($directory = realpath($this->getTempDirectory() . DIRECTORY_SEPARATOR . $this->sitepamsDirectory))) {
            mkdir(
                $this->getTempDirectory() . DIRECTORY_SEPARATOR . $this->sitepamsDirectory,
                0777,
                true
            );
            $directory = realpath($this->getTempDirectory() . DIRECTORY_SEPARATOR . $this->sitepamsDirectory);
        }

        if (!$directory) {
            throw new Exception('Can\'t get temporary directory.');
        }

        return $directory;
    }

    /**
     * @throws Exception
     *
     * @return array
     */
    public function generateSitemaps(): array
    {
        if (null === $this->getDataCollector()) {
            throw new Exception('DataCollector is not set.');
        }

        if (null === $this->getXml()) {
            throw new Exception('XML Driver is not set.');
        }

        $totalItems = $this->getDataCollector()->getCount();

        if (0 == $totalItems) {
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
                            if (strtotime($element['lastmod']) > strtotime($files[$group . $this->getSeparator() . $groupNo . self::EXT])) {
                                $files[$group . $this->getSeparator() . $groupNo . self::EXT] = $element['lastmod'];
                            }
                        } else {
                            $files[$group . $this->getSeparator() . $groupNo . self::EXT] = $element['lastmod'];
                        }
                    }

                    if ($filesInGroup >= self::ITEM_PER_SITEMAP ||
                        $this->getXml()->getSitemapSize() >= (self::SITEMAP_MAX_SIZE - 20)) { // 20 chars buffer for close tag
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

        if ($this->isUseCompression() && !empty($files)) {
            $this->compressFiles($this->getSitepamsTempDirectory(), $files);
        }

        return $files;
    }

    /**
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
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
     * Check if compression is used.
     *
     * @return bool
     */
    public function isUseCompression(): bool
    {
        return $this->useCompression;
    }

    /**
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

        if (null === $this->getXml()) {
            throw new Exception('XML Driver is not set.');
        }

        $counter = 0;
        $file = $this->getIndexFilename() . self::EXT;
        $files = [$file => null];
        $this->getXml()->openSitemapIndex($file);
        $lastItem = array_key_last($sitemaps);

        foreach ($sitemaps as $sitemap => $lastmod) {
            $this->getXml()->addSitemap($this->getDomain() . '/' . ltrim(str_replace(
                $this->getPublicDirectory(),
                '',
                $this->getSitepamsDirectory()
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

        if ($this->isUseCompression() && !empty($files)) {
            $this->compressFiles($this->getTempDirectory(), $files);
        }

        return $files;
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
     * @throws \Exception
     *
     * @return string
     */
    public function getSitepamsDirectory(): string
    {
        if (!($directory = realpath($this->getPublicDirectory() . DIRECTORY_SEPARATOR . $this->sitepamsDirectory))) {
            mkdir($this->getPublicDirectory() . DIRECTORY_SEPARATOR . $this->sitepamsDirectory, 0777, true);
            $directory = realpath($this->getPublicDirectory() . DIRECTORY_SEPARATOR . $this->sitepamsDirectory);
        }

        if (!$directory) {
            throw new Exception('Can\'t get sitemap directory.');
        }

        return $directory;
    }

    /**
     * @param string $sitepamsDirectory
     *
     * @return \Wszetko\Sitemap\Sitemap
     */
    public function setSitepamsDirectory(string $sitepamsDirectory): self
    {
        $this->sitepamsDirectory = $sitepamsDirectory;

        return $this;
    }

    /**
     * @param string $dir
     *
     * @return void
     */
    private function removeDir($dir): void
    {
        if (is_dir($dir) && $objects = scandir($dir)) {
            foreach ($objects as $object) {
                if ('.' != $object && '..' != $object) {
                    if ('dir' == filetype($dir . '/' . $object)) {
                        $this->removeDir($dir . '/' . $object);
                    } else {
                        unlink($dir . '/' . $object);
                    }
                }
            }

            rmdir($dir);
        }
    }

    /**
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

            if (!$out) {
                throw new Exception('Can\'t create GZip archive.');
            }

            if (!$in) {
                throw new Exception('Can\'t open xml file.');
            }

            while (!feof($in)) {
                if ($content = fread($in, 524288)) {
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
     * @throws \Exception
     *
     * @return void
     */
    private function publishSitemap(): void
    {
        // Clear previous sitemaps
        $this->removeDir($this->getSitepamsDirectory());

        if ($publicDir = scandir($this->getPublicDirectory())) {
            foreach ($publicDir as $file) {
                if (preg_match_all(
                    '/^(' . $this->getIndexFilename() . ')((-)[\d]+)?(' . $this->getExt() . ')$/',
                    $file
                )) {
                    unlink($this->getPublicDirectory() . DIRECTORY_SEPARATOR . $file);
                }
            }
        }

        $this->getSitepamsDirectory(); //To create sitemaps directory
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

        $this->removeDir($this->getTempDirectory());
    }

    /**
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
