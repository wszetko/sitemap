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

namespace Wszetko\Sitemap\Drivers\XML;

use Wszetko\Sitemap\Interfaces\XML;
use Wszetko\Sitemap\Traits\Domain;
use Wszetko\Sitemap\Traits\IsAssoc;

/**
 * Class AbstractXML.
 *
 * @package Wszetko\Sitemap\Drivers\XML
 */
abstract class AbstractXML implements XML
{
    use IsAssoc;
    use Domain;

    /**
     * @var mixed
     */
    protected $XMLWriter;

    /**
     * @var string
     */
    private $currentSitemap;

    /**
     * @var null|string
     */
    private $workDir;

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
     * @return null|string
     */
    public function getWorkDir(): ?string
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
     * @return mixed
     */
    protected function getXMLWriter()
    {
        return $this->XMLWriter;
    }

    /**
     * @return string
     */
    protected function getSitemapFileFullPath(): string
    {
        return $this->getWorkDir() . DIRECTORY_SEPARATOR . $this->currentSitemap;
    }
}
