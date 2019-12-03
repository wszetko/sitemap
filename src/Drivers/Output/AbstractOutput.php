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

use InvalidArgumentException;
use Wszetko\Sitemap\Interfaces\XML;
use Wszetko\Sitemap\Traits\Domain;
use Wszetko\Sitemap\Traits\IsAssoc;

/**
 * Class AbstractXML.
 *
 * @package Wszetko\Sitemap\Drivers\XML
 */
abstract class AbstractOutput implements XML
{
    use IsAssoc;
    use Domain;

    /**
     * Name of current sitemap.
     *
     * @var string
     */
    private $currentSitemap = '';

    /**
     * Path of current work directory.
     *
     * @var null|string
     */
    private $workDir;

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $config)
    {
        if (!isset($config['domain'])) {
            throw new InvalidArgumentException('Domain is not set.');
        }

        $this->setDomain($config['domain']);
    }

    /**
     * Return current sitemap name.
     *
     * @return string
     */
    public function getCurrentSitemap(): string
    {
        return $this->currentSitemap;
    }

    /**
     * Set current sitemap name.
     *
     * @param string $currentSitemap
     */
    public function setCurrentSitemap(string $currentSitemap): void
    {
        $this->currentSitemap = $currentSitemap;
    }

    /**
     * @inheritDoc
     */
    public function getWorkDir(): ?string
    {
        return $this->workDir;
    }

    /**
     * @inheritDoc
     */
    public function setWorkDir(string $dir): void
    {
        $this->workDir = $dir;
    }

    /**
     * Return full path of current sitemap.
     *
     * @return string
     */
    protected function getSitemapFileFullPath(): string
    {
        return ((string) $this->getWorkDir()) . DIRECTORY_SEPARATOR . $this->currentSitemap;
    }
}
