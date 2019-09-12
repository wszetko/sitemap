Sitemap generator library
=========================

Library to generate XML sitemaps.

[![StyleCI](https://github.styleci.io/repos/202325604/shield?branch=master)](https://github.styleci.io/repos/202325604) [![Build Status](https://travis-ci.org/wszetko/sitemap.svg?branch=master)](https://travis-ci.org/wszetko/sitemap) [![Code Coverage](https://scrutinizer-ci.com/g/wszetko/sitemap/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/wszetko/sitemap/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/wszetko/sitemap/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/wszetko/sitemap/?branch=master)

__Work in progress!__

Example of usage is in example directory.

Installation
-----

```bash
$ composer require wszetko/sitemap
```

Testing
-------

The library has a :

- a [PHPUnit](https://phpunit.de) test suite
- a coding style compliance test suite using [PHP CS Fixer](http://cs.sensiolabs.org/).

To run the tests, run the following command from the project folder.

``` bash
$ composer test
```

To do
-----

* load previous sitemap and compare changes
* Driver XML Text
* Driver DataCollector MySQL
* Driver DataCollector PostgreSQL
* Driver DataCollector MongoDB
* Driver DataCollector CrunchDB (?)
* Ability to generate sitemap without publishing
* Ability to publish sitemap without clearing previous files
* Logging compatible with PSR-3
* Improve URL Normalization

## License

This library is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.
