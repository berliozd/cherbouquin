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

        global $globalConfig;
        return $globalConfig;
    }

    private static function getImageSrc($url, $defaultImg) {

        $src = ($url != "" ? $url : $defaultImg);
        return $src;
    }

    public static function getSmallImageTag($url, $title, $defaultImg) {

        return sprintf("<img src='%s' border='0' class='image-thumb-small image-frame' title=\"%s\" alt=\"%s\"/>", self::getImageSrc($url, $defaultImg), $title, $title);
    }

    public static function getMediumImageTag($url, $title, $defaultImg) {

        return sprintf("<img src='%s' border='0' class='image-thumb image-frame' title=\"%s\" alt=\"%s\"/>", self::getImageSrc($url, $defaultImg), $title, $title);
    }

    public static function getLargeImageTag($largeImageUrl, $imageUrl, $title, $defaultImg) {

        $src = ($largeImageUrl != "" ? $largeImageUrl : self::getImageSrc($imageUrl, $defaultImg));
        return sprintf("<img src='%s' border='0' class='bookPreview' title=\"%s\" alt=\"%s\"/>", $src, $title, $title);
    }

    public static function getMediumImageTagForFlipCarousel($url, $title, $defaultImg) {

        return sprintf("<img src='%s' border='0' class='image-thumb' title=\"%s\" alt=\"%s\" />", self::getImageSrc($url, $defaultImg), $title, $title);
    }

}