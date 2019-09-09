<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Drivers\XML;

use Exception;
use Wszetko\Sitemap\Interfaces\XML;
use Wszetko\Sitemap\Sitemap;
use Wszetko\Sitemap\Traits\Domain;
use Wszetko\Sitemap\Traits\IsAssoc;

/**
 * Class XMLWriter
 *
 * @package Wszetko\Sitemap\Drivers\XML
 */
class XMLWriter implements XML
{
    use IsAssoc;
    use Domain;

    /**
     * @var \XMLWriter
     */
    private $XMLWriter;

    /**
     * @var string
     */
    private $currentSitemap;

    /**
     * @var string|null
     */
    private $workDir;

    /**
     * XMLWriter constructor.
     *
     * @param array $config
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $config)
    {
        if (!isset($config['domain'])) {
            throw new \InvalidArgumentException('Domain is not set.');
        }

        $this->XMLWriter = new \XMLWriter();
        $this->setDomain($config['domain']);
    }

    /**
     * @return string
     */
    public function getCurrentSitemap(): string
    {
        return $this->currentSitemap;
    }

    /**
     * @param string $currentSitemap
     */
    public function setCurrentSitemap(string $currentSitemap): void
    {
        $this->currentSitemap = $currentSitemap;
    }

    /**
     * @param string $sitemap
     * @param array  $extensions
     */
    public function openSitemap(string $sitemap, array $extensions = []): void
    {
        $this->setCurrentSitemap($sitemap);
        $this->getXMLWriter()->openMemory();
        $this->getXMLWriter()->startDocument('1.0', 'UTF-8');
        $this->getXMLWriter()->setIndent(true);
        $this->getXMLWriter()->startElement('urlset');
        $this->getXMLWriter()->writeAttribute('xmlns', Sitemap::SCHEMA);

        foreach ($extensions as $extension => $urlset) {
            $this->getXMLWriter()->writeAttribute('xmlns:' . $extension, $urlset);
        }

        $this->flushData();
    }

    /**
     * @return \XMLWriter
     */
    private function getXMLWriter(): \XMLWriter
    {
        return $this->XMLWriter;
    }

    /**
     * Save from buffer to file
     *
     * @return void
     */
    private function flushData(): void
    {
        file_put_contents($this->getSitemapFileFullPath(), $this->getXMLWriter()->flush(true), FILE_APPEND);
    }

    /**
     * @return string
     */
    private function getSitemapFileFullPath(): string
    {
        return $this->getWorkDir() . DIRECTORY_SEPARATOR . $this->currentSitemap;
    }

    /**
     * @return string
     */
    public function getWorkDir(): string
    {
        return $this->workDir;
    }

    /**
     * @param string $dir
     */
    public function setWorkDir(string $dir): void
    {
        $this->workDir = $dir;
    }

    /**
     * @throws \Exception
     */
    public function closeSitemap(): void
    {
        $this->getXMLWriter()->endElement();
        $this->getXMLWriter()->endDocument();
        $this->flushData();
        $this->endFile();
    }

    /**
     * Remove whitespace chars from end of file (Google don't like them)
     *
     * @return void
     * @throws Exception
     */
    private function endFile(): void
    {
        $sitemapFile = fopen($this->getSitemapFileFullPath(), 'r+');

        if ($sitemapFile === false) {
            throw new Exception("Unable to open file.");
        }

        fseek($sitemapFile, -1, SEEK_END);
        $truncate = 0;
        $length = $this->getSitemapSize();
        $end = false;

        do {
            $s = fread($sitemapFile, 1);
            if (ctype_space($s)) {
                $truncate++;
                fseek($sitemapFile, -2, SEEK_CUR);
            } else {
                $end = true;
            }
        } while (!$end);

        ftruncate($sitemapFile, $length - $truncate);
        fclose($sitemapFile);
    }

    /**
     * @return int
     */
    public function getSitemapSize(): int
    {
        clearstatcache(true, $this->getSitemapFileFullPath());

        return file_exists($this->getSitemapFileFullPath()) ? filesize($this->getSitemapFileFullPath()) : 0;
    }

    /**
     * @param array $element
     */
    public function addUrl(array $element): void
    {
        foreach ($element as $el => $val) {
            $this->addElement($el, $val);
        }

        $this->flushData();
    }

    /**
     * @param string      $element
     * @param             $value
     * @param string|null $namespace
     */
    private function addElement(string $element, $value, ?string $namespace = null): void
    {
        if (!is_array($value)) {
            $this->getXMLWriter()->writeElement(($namespace ? $namespace . ':' : '') . $element, (string) $value);
        } else {
            if (isset($value['_namespace'])) {
                $this->addElement($value['_element'], $value[$value['_element']], $value['_namespace']);
            } else {
                $this->addElementArray($element, $value, $namespace);
            }
        }
    }

    /**
     * @param string      $element
     * @param             $value
     * @param string|null $namespace
     */
    private function addElementArray(string $element, $value, ?string $namespace = null): void
    {
        if (!$this->isAssoc($value)) {
            if (!empty($value)) {
                $this->addElementArrayNonAssoc($element, $value, $namespace);
            } else {
                $this->getXMLWriter()->writeElement(($namespace ? $namespace . ':' : '') . $element);
            }
        } else {
            $this->addElementArrayAssoc($element, $value, $namespace);
        }
    }

    private function addElementArrayAssoc(string $element, $value, ?string $namespace = null): void
    {
        $this->getXMLWriter()->startElement(($namespace ? $namespace . ':' : '') . $element);
        if (isset($value['_attributes'])) {
            foreach ($value['_attributes'] as $attribute => $val) {
                $this->getXMLWriter()->writeAttribute($attribute, $val);
            }

            if (isset($value['_value'])) {
                if (is_array($value['_value'])) {
                    foreach ($value['_value'] as $el => $val) {
                        $this->addElement($el, $val);
                    }
                } else {
                    $this->getXMLWriter()->text((string) $value['_value']);
                }
            }
        } else {
            foreach ($value as $el => $val) {
                if (is_array($val)) {
                    $this->addElement($el, $val, $namespace);
                } else {
                    $this->getXMLWriter()->writeElement(($namespace ? $namespace . ':' : '') . $el, (string) $val);
                }
            }
        }
        $this->getXMLWriter()->endElement();
    }

    /**
     * @param string      $element
     * @param             $value
     * @param string|null $namespace
     */
    private function addElementArrayNonAssoc(string $element, $value, ?string $namespace = null): void
    {
        foreach ($value as $val) {
            $this->addElement($element, $val, $namespace);
        }
    }

    /**
     * @param string $sitemap
     */
    public function openSitemapIndex(string $sitemap): void
    {
        $this->setCurrentSitemap($sitemap);
        $this->getXMLWriter()->openMemory();
        $this->getXMLWriter()->startDocument('1.0', 'UTF-8');
        $this->getXMLWriter()->setIndent(true);
        $this->getXMLWriter()->startElement('sitemapindex');
        $this->getXMLWriter()->writeAttribute('xmlns', Sitemap::SCHEMA);
        $this->flushData();
    }

    /**
     * @throws \Exception
     */
    public function closeSitemapIndex(): void
    {
        $this->getXMLWriter()->endElement();
        $this->getXMLWriter()->endDocument();
        $this->flushData();
        $this->endFile();
    }

    /**
     * @param string $sitemap
     * @param string|null $lastmod
     */
    public function addSitemap(string $sitemap, string $lastmod = null): void
    {
        $this->getXMLWriter()->startElement('sitemap');
        $this->getXMLWriter()->writeElement('loc', $sitemap);
        if (isset($lastmod)) {
            $this->getXMLWriter()->writeElement('lastmod', $lastmod);
        }
        $this->getXMLWriter()->endElement();
        $this->flushData();
    }
}
