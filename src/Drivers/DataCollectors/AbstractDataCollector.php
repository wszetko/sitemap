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

namespace Wszetko\Sitemap\Drivers\DataCollectors;

use Wszetko\Sitemap\Interfaces\DataCollector;

/**
 * Class AbstractDataCollector.
 *
 * @package Wszetko\Sitemap\Drivers\DataCollectors
 */
abstract class AbstractDataCollector implements DataCollector
{
    /**
     * AbstractDataCollector constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (method_exists($this, 'setConfig')) {
            $this->setConfig($config);
        }
    }
}
