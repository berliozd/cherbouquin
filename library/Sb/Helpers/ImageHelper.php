<?php

namespace Sb\Helpers;

/**
 * Description of ImageHelper
 * @author Didier
 */
class ImageHelper {

    /**
     *
     * @return Config
     */
    private static function getConfig() {
        return new \Sb\Config\Model\Config();
    }

    private static function getImageSrc($url, $defaultImg) {

        $src = ($url != "" ? $url : $defaultImg);
        return $src;
    }

    public static function getSmallImageTag($url, $title, $defaultImg) {

        return sprintf("<img src='%s' border='0' class='image-thumb-small' title=\"%s\" alt=\"%s\"/>", self::getImageSrc($url, $defaultImg), $title, $title);
    }

    public static function getSmallSquareImageTag($url, $title, $defaultImg) {

        return sprintf("<img src='%s' border='0' class='image-thumb-square-small' title=\"%s\" alt=\"%s\"/>", self::getImageSrc($url, $defaultImg), $title, $title);
    }

    public static function getMediumImageTag($url, $title, $defaultImg, $addItemProd = false) {

        return sprintf("<img src='%s' border='0' class='image-thumb' title=\"%s\" alt=\"%s\"/ " . ($addItemProd ? "itemprop=\"image\"" : "") . ">", self::getImageSrc($url, $defaultImg), $title, $title);
    }

    public static function getLargeImageTag($largeImageUrl, $imageUrl, $title, $defaultImg) {

        $src = ($largeImageUrl != "" ? $largeImageUrl : self::getImageSrc($imageUrl, $defaultImg));
        return sprintf("<img src='%s' border='0' class='bookPreview' title=\"%s\" alt=\"%s\"/>", $src, $title, $title);
    }

    public static function getMediumImageTagForFlipCarousel($url, $href, $title, $defaultImg) {

        return sprintf("<img src='%s' href='%s' border='0' class='image-thumb' title=\"%s\" alt=\"%s\" />", self::getImageSrc($url, $defaultImg), $href, $title, $title);
    }

}