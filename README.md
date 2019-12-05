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

### Requirements

PHP 7.2 or newer is recommended.

Testing
-------

The library has a :

- a [PHPUnit](https://phpunit.de) test suite,
- other tools using in project developement:
    - [PHP CS Fixer](http://cs.sensiolabs.org/),
    - [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer),
    - [PHP Static Analysis Tool](https://github.com/phpstan/phpstan),
    - [Copy/Paste Detector](https://github.com/sebastianbergmann/phpcpd),
    - [PHP Mess Detector](https://phpmd.org),
    - [PhpMetrics](https://www.phpmetrics.org/),
    - [PHP Parallel Lint](https://github.com/JakubOnderka/PHP-Parallel-Lint),
    - [Psalm](https://github.com/vimeo/psalm),
    - [PHP Magic Number Detector](https://github.com/povils/phpmnd),
    - [PhpCodeAnalyzer](https://github.com/wapmorgan/PhpCodeAnalyzer),
    - [phploc](https://github.com/sebastianbergmann/phploc),
    - [SensioLabs Security Checker](https://github.com/sensiolabs/security-checker).

There are prepared composer scripts to run. To see full list please type in console:

```bash
$ composer list
```

To run the *full* tests, run the following command from the project folder:

```bash
$ composer test
```
> Please note that those test should run on PHP 7.1 or higher.

If You want to run just PHPUnit tests please run:
```bash
$ composer phpunit
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

## License

This library is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.
