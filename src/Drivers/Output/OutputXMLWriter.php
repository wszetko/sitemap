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

namespace Wszetko\Sitemap\Drivers\Output;

use Exception;
use InvalidArgumentException;
use Wszetko\Sitemap\Sitemap;
use XMLWriter;

/**
 * Class XMLWriter.
 *
 * @package Wszetko\Sitemap\Drivers\XML
 */
class OutputXMLWriter extends AbstractOutput
{
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
            throw new InvalidArgumentException('Domain is not set.');
        }

        $this->XMLWriter = new XMLWriter();
        $this->setDomain($config['domain']);
    }

    /**
     * @param string $sitemap
     * @param array  $extensions
     */
    public function openSitemap(string $sitemap, array $extensions = []): void
    {
        $this->setCurrentSitemap($sitemap);
        /** @var \XMLWriter $xmlWriter */
        $xmlWriter = $this->getXMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->setIndent(true);
        $xmlWriter->startElement('urlset');
        $xmlWriter->writeAttribute('xmlns', Sitemap::SCHEMA);

        foreach ($extensions as $extension => $urlset) {
            $this->getXMLWriter()->writeAttribute('xmlns:' . $extension, $urlset);
        }

        $this->flushData();
    }

    /**
     * @throws \Exception
     */
    public function closeSitemap(): void
    {
        /** @var \XMLWriter $xmlWriter */
        $xmlWriter = $this->getXMLWriter();
        $xmlWriter->endElement();
        $xmlWriter->endDocument();
        $this->flushData();
        $this->endFile();
    }

    /**
     * @return int
     */
    public function getSitemapSize(): int
    {
        clearstatcache(true, $this->getSitemapFileFullPath());

        return file_exists($this->getSitemapFileFullPath()) ? (int) filesize($this->getSitemapFileFullPath()) : 0;
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
     * @param string $sitemap
     */
    public function openSitemapIndex(string $sitemap): void
    {
        $this->setCurrentSitemap($sitemap);
        /** @var \XMLWriter $xmlWriter */
        $xmlWriter = $this->getXMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->setIndent(true);
        $xmlWriter->startElement('sitemapindex');
        $xmlWriter->writeAttribute('xmlns', Sitemap::SCHEMA);
        $this->flushData();
    }

    /**
     * @throws \Exception
     */
    public function closeSitemapIndex(): void
    {
        /** @var \XMLWriter $xmlWriter */
        $xmlWriter = $this->getXMLWriter();
        $xmlWriter->endElement();
        $xmlWriter->endDocument();
        $this->flushData();
        $this->endFile();
    }

    /**
     * @param string      $sitemap
     * @param null|string $lastmod
     */
    public function addSitemap(string $sitemap, string $lastmod = null): void
    {
        /** @var \XMLWriter $xmlWriter */
        $xmlWriter = $this->getXMLWriter();
        $xmlWriter->startElement('sitemap');
        $xmlWriter->writeElement('loc', $sitemap);
        if (isset($lastmod)) {
            $xmlWriter->writeElement('lastmod', $lastmod);
        }
        $xmlWriter->endElement();
        $this->flushData();
    }

    /**
     * Save from buffer to file.
     *
     * @return void
     */
    private function flushData(): void
    {
        file_put_contents($this->getSitemapFileFullPath(), $this->getXMLWriter()->flush(true), FILE_APPEND);
    }

    /**
     * Remove whitespace chars from end of file (Google don't like them).
     *
     * @throws Exception
     *
     * @return void
     */
    private function endFile(): void
    {
        $sitemapFile = fopen($this->getSitemapFileFullPath(), 'r+');

        if (false !== $sitemapFile) {
            fseek($sitemapFile, -1, SEEK_END);
            $truncate = 0;
            $length = $this->getSitemapSize();
            $char = fread($sitemapFile, 1);

            while (is_string($char) && ctype_space($char)) {
                ++$truncate;
                fseek($sitemapFile, -2, SEEK_CUR);
                $char = fread($sitemapFile, 1);
            }

            ftruncate($sitemapFile, $length - $truncate);
            fclose($sitemapFile);
        }
    }

    /**
     * @param string      $element
     * @param mixed       $value
     * @param null|string $namespace
     */
    private function addElement(string $element, $value, ?string $namespace = null): void
    {
        if (!is_array($value)) {
            /** @var \XMLWriter $xmlWriter */
            $xmlWriter = $this->getXMLWriter();
            $xmlWriter->writeElement(($namespace !== null ? $namespace . ':' : '') . $element, (string) $value);

            return;
        }

        if (isset($value['_namespace'])) {
            $this->addElement($value['_element'], $value[$value['_element']], $value['_namespace']);
        } else {
            $this->addElementArray($element, $value, $namespace);
        }
    }

    /**
     * @param string      $element
     * @param array       $value
     * @param null|string $namespace
     */
    private function addElementArray(string $element, array $value, ?string $namespace = null): void
    {
        if (!$this->isAssoc($value)) {
            $this->addElementArrayNonAssoc($element, $value, $namespace);
        } else {
            $this->addElementArrayAssoc($element, $value, $namespace);
        }
    }

    /**
     * @param string      $element
     * @param mixed       $value
     * @param string|null $namespace
     */
    private function addElementArrayAssoc(string $element, $value, ?string $namespace = null): void
    {
        /** @var \XMLWriter $xmlWriter */
        $xmlWriter = $this->getXMLWriter();
        $xmlWriter->startElement(($namespace !== null ? $namespace . ':' : '') . $element);

        if (isset($value['_attributes'])) {
            foreach ($value['_attributes'] as $attribute => $val) {
                $xmlWriter->writeAttribute($attribute, $val);
            }

            if (isset($value['_value']) && is_string($value['_value'])) {
                $xmlWriter->text((string) $value['_value']);
            }
        } else {
            foreach ($value as $el => $val) {
                if (is_array($val)) {
                    $this->addElement($el, $val, $namespace);
                } else {
                    $xmlWriter->writeElement(($namespace !== null ? $namespace . ':' : '') . $el, (string) $val);
                }
            }
        }

        $xmlWriter->endElement();
    }

    /**
     * @param string      $element
     * @param mixed       $value
     * @param null|string $namespace
     */
    private function addElementArrayNonAssoc(string $element, $value, ?string $namespace = null): void
    {
        foreach ($value as $val) {
            $this->addElement($element, $val, $namespace);
        }
    }
}
