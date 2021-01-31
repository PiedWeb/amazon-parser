<?php

namespace Piedweb\AmazonParser;

use IntlDateFormatter;

/**
 * Made for https://www.amazon.co.uk
 */
class ProductPageUk extends ProductPage
{
    const REGEX_STARS = '/ o.*$/'; // out of 5 stars
    const CURRENCY = '£';

    public static function dateFormatter($date)
    {
        $fmt = new IntlDateFormatter(
            "en-UK",
            IntlDateFormatter::FULL,
            IntlDateFormatter::FULL,
            'Etc/UTC',
            IntlDateFormatter::GREGORIAN,
            'dd MMMM y'
        );

        return $fmt->parse($date);
    }
}
