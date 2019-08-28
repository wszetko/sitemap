<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Drivers\XML;

use Exception;
use Wszetko\Sitemap\Interfaces\XML;
use Wszetko\Sitemap\Sitemap;

/**
 * Class XMLWriter
 *
 * @package Wszetko\Sitemap\Drivers\XML
 */
class XMLWriter implements XML
{
    /**
     * @var \XMLWriter
     */
    private $XMLWriter;

    /**
     * @var string
     */
    private $currentSitemap;

    /**
     * @var string
     */
    private $workDir;

    /**
     * @var string
     */
    private $domain;

    /**
     * XMLWriter constructor.
     *
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        $this->XMLWriter = new \XMLWriter();

        if (!isset($config['domain'])) {
            throw new Exception('Domain is not set.');
        }

        $this->setDomain($config['domain']);
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
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
     *
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
     */
    private function endFile(): void
    {
        $sitemapFile = fopen($this->getSitemapFileFullPath(), 'r+');
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
        $this->getXMLWriter()->startElement('url');

        foreach ($element as $el => $val) {
            $this->addElement($el, $val);
        }

        $this->getXMLWriter()->endElement();
        $this->flushData();
    }

    private function addElement(string $element, $value, string $namespace = null): void
    {
        if ($namespace) {
            $begin = $namespace . ':';
        } else {
            $begin = '';
        }
        if (!is_array($value)) {
            $this->getXMLWriter()->writeElement($begin . $element, (string)$value);
        } else {
            if (isset($value['_namespace'])) {
                $this->getXMLWriter()->startElementNS($value['_namespace'], $value['_element'], null);

                if (isset($value[$value['_element']]['_attributes'])) {
                    foreach ($value[$value['_element']]['_attributes'] as $attribute => $val) {
                        $this->getXMLWriter()->writeAttribute($attribute, $val);
                    }
                    if (isset($value[$value['_element']]['_value'])) {
                        $this->getXMLWriter()->text($value[$value['_element']]['_value']);
                    }
                } else {
                    if (!empty($value[$value['_element']])) {
                        foreach ($value[$value['_element']] as $el => $val) {
                            $this->addElement($el, $val, $value['_namespace']);
                        }
                    }
                }
                $this->getXMLWriter()->endElement();
            } else {
                if ($this->isAssoc($value)) {
                    $this->getXMLWriter()->startElement($begin . $element);

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
                                $this->getXMLWriter()->text((string)$value['_value']);
                            }
                        }
                    } else {
                        foreach ($value as $el => $val) {
                            if (is_array($val)) {
                                $this->addElement($el, $val, $namespace);
                            } else {
                                $this->getXMLWriter()->writeElement($begin . $el, $val);
                            }
                        }
                    }

                    $this->getXMLWriter()->endElement();
                } else {
                    foreach ($value as $val) {
                        $this->getXMLWriter()->writeElement($begin . $element, $val);
                    }
                }
            }
        }
    }

    private function isAssoc(array $array): bool
    {
        foreach ($array as $key => $val) {
            if (!is_integer($key)) {
                return true;
            }
        }

        return false;
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
     *
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
     * @param ?string $lastmod
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
