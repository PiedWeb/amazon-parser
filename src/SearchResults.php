<?php

namespace Piedweb\AmazonParser;

use Symfony\Component\DomCrawler\Crawler;

class SearchResults
{
    public static function getUrls($rawHtml)
    {
        $crawler = new Crawler($rawHtml);


        return $crawler->filter('h2 > a')
            ->reduce(function (Crawler $node, $i) {
                return  strpos($node->attr('href'), 'slredirect') === false;
            })
            ->each(function (Crawler $node, $i) {
                return self::cleanUrl($node->attr('href'));
            });
    }

    public static function cleanUrl($url)
    {
        return substr($url, 0, strrpos($url, '/') + 1);
    }
}
