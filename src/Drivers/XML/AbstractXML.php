<?php

namespace Wszetko\Sitemap\Drivers\XML;

use Wszetko\Sitemap\Interfaces\XML;
use Wszetko\Sitemap\Traits\Domain;
use Wszetko\Sitemap\Traits\IsAssoc;

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
     * @var string|null
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
}