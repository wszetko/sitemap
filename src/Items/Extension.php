<?php
declare(strict_types=1);

namespace Wszetko\Sitemap\Items;

use Wszetko\Sitemap\Interfaces\Item;
use Wszetko\Sitemap\Traits\Domain;

/**
 * Class Extension
 *
 * @package Wszetko\Sitemap\Items
 */
abstract class Extension extends AbstractItem implements Item
{
    use Domain;
}
