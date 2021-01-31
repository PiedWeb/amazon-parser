<?php

namespace Piedweb\AmazonParser;

use IntlDateFormatter;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Made for https://www.amazon.fr
 */
class ProductPage
{
    protected $crawler;

    // cache some data
    protected $rawDescription;
    protected $title;

    const REGEX_STARS = '/ s.*$/';
    const CURRENCY = '€';

    public function __construct($rawHtml)
    {
        $this->crawler = new Crawler($rawHtml);
    }

    public static function parse($rawHtml)
    {
        return new self($rawHtml);
    }

    public function getPrice($onlyAmz = true)
    {
        $price = $this->crawler->filter('#priceblock_ourprice');

        if (! $price->count()) {
            if ($onlyAmz) {
                return null;
            }
            $price = $this->crawler->filter('.a-color-price');
        }

        if (! $price->count()
            || strpos($price->eq(0)->text(), self::CURRENCY) === false) {
            return null;
        }

        return str_replace([' ', self::CURRENCY], '', $price->eq(0)->text());
    }

    public function getTitle()
    {
        if ($this->title !== null) {
            return $this->title;
        }

        $title = $this->crawler->filter('#productTitle');


        if (! $title->count()) {
            return null;
        }

        $this->title = $title->eq(0)->text();

        return $this->title;
    }

    public function getRawDescription()
    {
        if ($this->rawDescription !== null) {
            return $this->rawDescription;
        }

        $this->rawDescription = '';
        $descriptionAlongThePage = [
            '#productOverview_feature_div',
            '#featurebullets_feature_div',
            '#prodDetails table',
            '#productDescription_feature_div',
        ];

        foreach ($descriptionAlongThePage as $selector) {
            $text = $this->crawler->filter($selector);

            if (! $text->count()) {
                continue;
            }
            $this->rawDescription .= ' '. $text->eq(0)->text();
        }

        return $this->rawDescription;
    }

    public function getTechDetailsFromMultipleLabel()
    {
        $arg_list = func_get_args();
        for ($i = 0; $i < func_num_args(); $i++) {
            $name = $arg_list[$i];
            $result = $this->getTechDetails($name);
            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    public function getTechDetails($name)
    {
        $techDetails = $this->crawler->filter('#prodDetails table');

        if (! $techDetails->count()) {
            return null;
        }

        $xpath = '//*/th[normalize-space(text())="'.$name.'"]';
        $line = $techDetails->filterXPath($xpath);

        if (! $line->count()) {
            return null;
        }

        return $line->eq(0)->parents()->filter('td')->first()->text();
    }

    public function find()
    {
        $arg_list = func_get_args();
        for ($i = 0; $i < func_num_args(); $i++) {
            $text = $arg_list[$i];
            if (
                stripos($this->getRawDescription().' '.$this->getTitle(), ' '.$text.' ') !== false
                || stripos($this->getRawDescription().' '.$this->getTitle(), '/'.$text.' ') !== false
                || stripos($this->getRawDescription().' '.$this->getTitle(), '/'.$text.'/') !== false
                || stripos($this->getRawDescription().' '.$this->getTitle(), ' '.$text.'/') !== false
            ) {
                return true;
            }
        }

        return false;
    }

    public function getStars()
    {
        $stars = $this->crawler->filter('.reviewCountTextLinkedHistogram');

        if (! $stars->count()) {
            return null;
        }

        return preg_replace(self::REGEX_STARS, '', $stars->eq(0)->attr('title'));
    }

    public function getReviews()
    {
        $reviews = $this->crawler->filter('[data-hook=review]');

        if (! $reviews->count()) {
            return null;
        }

        return array_filter($reviews->each(function (Crawler $node, $i) {
            if ($node->filter('[data-hook=review-star-rating]')->count()) {
                return [
                'stars' => preg_replace(self::REGEX_STARS, '', $node->filter('[data-hook=review-star-rating]')->eq(0)->text()),
                'date' => self::dateFormatter(preg_replace('/^[^0-9]*/i', '', $node->filter('[data-hook=review-date]')->eq(0)->text())),
                'title' => $node->filter('[data-hook=review-title]')->eq(0)->text(),
                'body' => $node->filter('[data-hook=review-body] span')->eq(0)->text(),
            ];
            }
        }));
    }

    public static function dateFormatter($date)
    {
        $fmt = new IntlDateFormatter(
            "fr-FR",
            IntlDateFormatter::FULL,
            IntlDateFormatter::FULL,
            'Etc/UTC',
            IntlDateFormatter::GREGORIAN,
            'dd MMMM y'
        );

        return $fmt->parse($date);
    }

    public function getCanonical()
    {
        $canonical = $this->crawler->filter('[rel=canonical]');

        if (! $canonical->count()) {
            return null;
        }

        return $canonical->eq(0)->attr('href');
    }

    public function getImage()
    {
        $image = $this->crawler->filter('[data-old-hires]');

        if (! $image->count()) {
            return null;
        }

        return $image->eq(0)->attr('data-old-hires');
    }
}
