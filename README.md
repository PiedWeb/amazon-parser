# Amazon Parser/Scraper in PHP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/piedweb/amazon-parser.svg?style=flat-square)](https://packagist.org/packages/piedweb/amazon-parser)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/piedweb/amazon-parser/run-tests?label=tests)](https://github.com/piedweb/amazon-parser/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/piedweb/amazon-parser.svg?style=flat-square)](https://packagist.org/packages/piedweb/amazon-parser)

## Installation

You can install the package via composer:

```bash
composer require piedweb/amazon-parser
```

## Usage

```php
use Piedweb\AmazonParser\SearchResults;
use Piedweb\AmazonParser\ProductPage;
use Piedweb\AmazonParser\ProductPageUk;

SearchResults::getUrls(file_get_contents('https://www.amazon.com/s?i=specialty-aps&bbn=16225009011&rh=n:!16225009011,n:541966&ref=nav_em__nav_desktop_sa_intl_computers_and_accessories_0_2_5_6'))

$manager = ProductPage::parse(file_get_contents('https://www.amazon.com/HP-24mh-FHD-Monitor-Built/dp/B08BF4CZSV/'));

$manager->getPrice()
```

## Credits

-   Robin from [Pied Web](https://piedweb.com)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
