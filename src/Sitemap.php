<?php
declare(strict_types=1);

namespace Wszetko\Sitemap;

use Exception;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use Wszetko\Sitemap\Drivers\XML\XMLWriter;
use Wszetko\Sitemap\Helpers\Url;
use Wszetko\Sitemap\Interfaces\DataCollector;
use Wszetko\Sitemap\Interfaces\XML;

/**
 * Sitemap
 * This class used for generating Google Sitemap files
 *
 * @package    Sitemap
 * @author     Paweł Kłopotek-Główczewski <pawelkg@pawelkg.com>
 * @copyright  2019 Paweł Kłopotek-Głowczewski (https://pawelkg.com/)
 * @license    https://opensource.org/licenses/MIT MIT License
 * @link       https://github.com/wszetko/sitemap
 */
class Sitemap
{
    /**
     * Avaliable values for changefreq tag
     *
     * @var array
     */
    const CHANGEFREQ = [
        'always',
        'hourly',
        'daily',
        'weekly',
        'monthly',
        'yearly',
        'never'
    ];

    /**
     * Extension for sitemap file
     *
     * @var string
     */
    const EXT = '.xml';

    /**
     * Extension for gzipped sitemap file
     *
     * @var string
     */
    const GZ_EXT = '.xml.gz';

    /**
     * URL to Sitemap Schema
     *
     * @var string
     */
    const SCHEMA = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    /**
     * Limit of items in Sitemap files
     *
     * @var int
     */
    const ITEM_PER_SITEMAP = 50000;

    /**
     * Limit of Sitmeaps in SitemapsIndex
     *
     * @var int
     */
    const SITEMAP_PER_SITEMAPINDEX = 1000;

    /**
     * Limit of single files size
     *
     * @var int
     */
    const SITEMAP_MAX_SIZE = 52000000;

    /**
     * Domain name of site
     *
     * @var string
     */
    private $domain = '';

    /**
     * Path to sitemap in domain
     *
     * @var string
     */
    private $path = '';

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
     * Default filename for sitemap file
     *
     * @var string
     */
    private $defaultFilename = 'sitemap';

    /**
     * Name of index file
     *
     * @var string
     */
    private $indexFilename = 'index';

    /**
     * DataCollector instance
     *
     * @var  DataCollector
     */
    private $dataCollector = null;

    /**
     * Use compression
     *
     * @var bool
     */
    private $useCompression = false;

    /**
     * XML Writer object
     *
     * @var XML
     */
    private $xml;

    /**
     * Separator to be used in Sitemap filenames
     *
     * @var string
     */
    private $separator = '-'; // ~49,6MB - to have some limit to close file

    /**
     * Construktor
     *
     * @param string $domain
     */
    public function __construct(string $domain = null)
    {
        if ($domain) {
            $this->setDomain($domain);
        }
    }

    /**
     * Set path to sitemap
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get path to sitemap
     *
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @param Items\Url   $item
     * @param string|null $group
     */
    public function addItem(Items\Url $item, ?string $group = null)
    {
        if ($group === null) {
            $group = $this->getDefaultFilename();
        }
        $group = strtolower(preg_replace('/\W+/', '', $group));
        $item->setDomain($this->getDomain());
        $this->getDataCollector()->add($item, $group);
    }

    /**
     * Get default filename for sitemap file
     *
     * @return string
     */
    public function getDefaultFilename(): string
    {
        return $this->defaultFilename;
    }

    /**
     * Set default filename for sitemap file
     *
     * @param string $defaultFilename
     */
    public function setDefaultFilename(string $defaultFilename): void
    {
        $this->defaultFilename = $defaultFilename;
    }

    /**
     * Get domain name
     *
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * Set domain name
     *
     * @param string $domain
     */
    public function setDomain(string $domain): void
    {
        if ($domain = Url::normalizeUrl($domain)) {
            $this->domain = rtrim($domain, '/');
        } else {
            throw new InvalidArgumentException('Parameter $domain need to be valid domain name.');
        }
    }

    /**
     * Get DataCollecotr Object
     *
     * @return DataCollector|null
     */
    public function getDataCollector(): ?DataCollector
    {
        return $this->dataCollector;
    }

    /**
     * @param string $driver
     * @param mixed
     */
    public function setDataCollector(string $driver, $config = null): void
    {
        $driver = '\Wszetko\Sitemap\Drivers\DataCollectors\\' . $driver;

        if (class_exists($driver)) {
            $this->dataCollector = new $driver($config);
        }
    }

    /**
     * @throws Exception
     */
    public function generate()
    {
        if ($this->getPublicDirectory() === '') {
            throw new Exception('Public directory is not set.');
        }

        if ($this->getDomain() === '') {
            throw new Exception('Domain is not set.');
        }

        if ($this->getDataCollector() === null) {
            throw new Exception('DataCollector is not set.');
        }

        if (empty($this->getXml())) {
            $this->setXml(XMLWriter::class, ['domain' => $this->getDomain()]);
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
     */
    public function setPublicDirectory(string $publicDirectory): void
    {
        if (!($publicDirectory = realpath($publicDirectory))) {
            throw new Exception('Sitemap directory does not exists.');
        }

        $this->publicDirectory = $publicDirectory;
    }

    /**
     * @return XML|null
     */
    public function getXml(): ?XML
    {
        return $this->xml;
    }

    /**
     * @param string $driver
     * @param array  $config
     */
    public function setXml(string $driver, array $config = []): void
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
    }

    /**
     * @param string $dir
     */
    private function removeDir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);

            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        $this->removeDir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }

            rmdir($dir);
        }
    }

    /**
     * @return string
     */
    public function getTempDirectory(): string
    {
        if (empty($this->sitemapTempDirectory)) {
            $hash = md5(microtime());
            if (!is_dir(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sitemap' . $hash)) {
                mkdir(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sitemap' . $hash);
            }

            $this->sitemapTempDirectory = realpath(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sitemap' . $hash);
        }

        return $this->sitemapTempDirectory;
    }

    /**
     * @return string
     */
    public function getSitepamsTempDirectory(): string
    {
        if (!($directory = realpath($this->getTempDirectory() . DIRECTORY_SEPARATOR . $this->sitepamsDirectory))) {
            mkdir($this->getTempDirectory() . DIRECTORY_SEPARATOR . $this->sitepamsDirectory,
                0777, true);
            $directory = realpath($this->getTempDirectory() . DIRECTORY_SEPARATOR . $this->sitepamsDirectory);
        }

        return $directory;
    }

    /**
     * @throws Exception
     */
    public function generateSitemaps(): array
    {
        $totalItems = $this->getDataCollector()->getCount();

        if ($totalItems == 0) {
            return [];
        }

        $groups = $this->getDataCollector()->getGroups();
        $currentGroup = 0;
        $files = [];

        foreach ($groups as $group) {
            $groupNo = 0;
            $filesInGroup = 0;
            $currentGroup++;

            if ($this->getDataCollector()->getGroupCount($group) > 0) {
                $this->getXml()->openSitemap($group . $this->getSeparator() . $groupNo . self::EXT,
                    $this->getDataCollector()->getExtensions());
                $files[$group . $this->getSeparator() . $groupNo . self::EXT] = null;

                while ($element = $this->getDataCollector()->fetch($group)) {
                    $this->getXml()->addUrl($element);
                    $filesInGroup++;

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
                            $groupNo++;
                            $filesInGroup = 0;
                            $this->getXml()->openSitemap($group . $this->getSeparator() . $groupNo . self::EXT,
                                $this->getDataCollector()->getExtensions());
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
     */
    public function setSeparator(string $separator): void
    {
        $this->separator = $separator;
    }

    /**
     * Check if compression is used
     *
     * @return bool
     */
    public function isUseCompression(): bool
    {
        return $this->useCompression;
    }

    /**
     * Set whether to use compression or not
     *
     * @param bool $useCompression
     */
    public function setUseCompression(bool $useCompression): void
    {
        if ($useCompression && !extension_loaded('zlib')) {
            return;
        }
        $this->useCompression = $useCompression;
    }

    /**
     * @param string $dir
     * @param array  $files
     *
     * @throws Exception
     */
    private function compressFiles(string $dir, array &$files)
    {
        $newFiles = [];

        foreach ($files as $file => $lastmod) {
            $source = $dir . DIRECTORY_SEPARATOR . $file;
            $gzFile = substr($file, 0, strlen($file) - 4) . self::GZ_EXT;
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
                gzwrite($out, fread($in, 524288));
            }

            fclose($in);
            gzclose($out);
            unlink($source);
            $newFiles[$gzFile] = $lastmod;
        }
        $files = $newFiles;
    }

    /**
     * @param array $sitemaps
     *
     * @return array
     * @throws Exception
     */
    public function generateSitemapsIndex(array $sitemaps): array
    {
        if (count($sitemaps) === 0) {
            return [];
        }

        $counter = 0;
        $files = [$this->getIndexFilename() . self::EXT => null];
        $this->getXml()->openSitemapIndex(array_key_last($files));
        $lastItem = array_key_last($sitemaps);

        foreach ($sitemaps as $sitemap => $lastmod) {
            $this->getXml()->addSitemap($this->getDomain() . '/' . ltrim(str_replace($this->getPublicDirectory(), '',
                    $this->getSitepamsDirectory()), DIRECTORY_SEPARATOR) . '/' . $sitemap, $lastmod);
            $counter++;

            if ($counter >= self::SITEMAP_PER_SITEMAPINDEX) {
                $this->getXml()->closeSitemapIndex();
                $counter = 0;
                $filesCount = count($files);

                if ($sitemap != $lastItem) {
                    $files[$this->getIndexFilename() . $this->getSeparator() . $filesCount . self::EXT] = null;
                    $this->getXml()->openSitemapIndex(array_key_last($files));
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
     * Get filename of sitemap index file
     *
     * @return string
     */
    public function getIndexFilename(): string
    {
        return $this->indexFilename;
    }

    /**
     * Set filename of sitemap index file
     *
     * @param string $indexFilename
     */
    public function setIndexFilename(string $indexFilename): void
    {
        $this->indexFilename = $indexFilename;
    }

    /**
     * @return string
     */
    public function getSitepamsDirectory(): string
    {
        if (!($directory = realpath($this->getPublicDirectory() . DIRECTORY_SEPARATOR . $this->sitepamsDirectory))) {
            mkdir($this->getPublicDirectory() . DIRECTORY_SEPARATOR . $this->sitepamsDirectory, 0777, true);
            $directory = realpath($this->getPublicDirectory() . DIRECTORY_SEPARATOR . $this->sitepamsDirectory);
        }

        return $directory;
    }

    /**
     * @param string $sitepamsDirectory
     */
    public function setSitepamsDirectory(string $sitepamsDirectory): void
    {
        $this->sitepamsDirectory = $sitepamsDirectory;
    }

    private function publishSitemap()
    {
        // Clear previous sitemaps
        $this->removeDir($this->getSitepamsDirectory());
        $publicDir = scandir($this->getPublicDirectory());

        foreach ($publicDir as $file) {
            if (preg_match_all('/^(' . $this->getIndexFilename() . ')((-)[\d]+)?(' . $this->getExt() . ')$/',
                $file)) {
                unlink($this->getPublicDirectory() . DIRECTORY_SEPARATOR . $file);
            }
        }

        $this->getSitepamsDirectory(); //To create sitemaps directory
        $dir = new RecursiveDirectoryIterator($this->getTempDirectory());
        $iterator = new RecursiveIteratorIterator($dir);
        $files = new RegexIterator($iterator,
            "/^(?'path'(([a-zA-Z]:)|((\\\\|\/){1,2}\w+)?)((\\\\|\/)(\w[\w ]*.*))+({$this->getExt()}){1})$/",
            RegexIterator::GET_MATCH);
        $fileList = [];

        foreach ($files as $file) {
            if (isset($file['path'])) {
                $fileList[] = $file['path'];
            }
        }

        $currentFile = 0;

        foreach ($fileList as $file) {
            $currentFile++;
            $destination = str_replace($this->getTempDirectory(), $this->getPublicDirectory(), $file);
            rename($file, $destination);
        }

        $this->removeDir($this->getTempDirectory());
    }

    /**
     * @return string
     */
    private function getExt()
    {
        if ($this->isUseCompression()) {
            return self::GZ_EXT;
        } else {
            return self::EXT;
        }
    }
}
