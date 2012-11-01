<?php

use Sb\Config\Model;

namespace Sb\Helpers;

/**
 * Description of BookHelper
 *
 * @author Didier
 */
class BookHelper {

    /**
     *
     * @return Config
     */
    private static function getConfig() {
        global $s1b;
        return $s1b->getConfig();
    }

    private static function getImageSrc($url, $defaultImg) {
        if ($url != "") {
            $src = $url;
        } else {
            $src = $defaultImg;
        }
        return $src;
    }

    public static function getSmallImageTag(\Sb\Db\Model\Book $book, $defaultImg) {
        return sprintf("<img src='%s' border='0' class='image-thumb-small image-frame' title='" . $book->getTitle() . "'/>", self::getImageSrc($book->getSmallImageUrl(), $defaultImg));
    }

    public static function getMediumImageTag(\Sb\Db\Model\Book $book, $defaultImg) {
        return sprintf("<img src='%s' border='0' class='image-thumb image-frame' title='" . $book->getTitle() . "'/>", self::getImageSrc($book->getImageUrl(), $defaultImg));
    }

    public static function getLargeImageTag(\Sb\Db\Model\Book $book, $defaultImg) {
        $src = $book->getLargeImageUrl();
        if ($src == "") {
            $src = self::getImageSrc($book->getImageUrl(), $defaultImg);
        }
        return sprintf("<img src='%s' border='0' class='bookPreview' title='" . $book->getTitle() . "'/>", $src);
    }

    public static function completeInfos(\Sb\Db\Model\Book &$book) {

        try {

            $config = self::getConfig();

            $googleBook = new \Sb\Google\Model\GoogleBook($book->getISBN10(), $book->getISBN13(), $book->getASIN(), $config->getGoogleApiKey());

            if ($googleBook->getVolumeInfo()) {
                \Sb\Trace\Trace::addItem('Le livre a été trouvé sur Google.');
                $bookFromGoogle = new \Sb\Db\Model\Book();
                \Sb\Db\Mapping\BookMapper::mapFromGoogleBookVolumeInfo($bookFromGoogle, $googleBook->getVolumeInfo());

                if ((!$book->getDescription()) && $bookFromGoogle->getDescription()) {
                    \Sb\Trace\Trace::addItem('Utilisation de la description issue de Google');
                    $book->setDescription($bookFromGoogle->getDescription());
                }
                if ((!$book->getImageUrl()) && $bookFromGoogle->getImageUrl()) {
                    \Sb\Trace\Trace::addItem('Utilisation de l\'image issue de Google');
                    $book->setImageUrl($bookFromGoogle->getImageUrl());
                }
                if ((!$book->getSmallImageUrl()) && $bookFromGoogle->getSmallImageUrl()) {
                    \Sb\Trace\Trace::addItem('Utilisation de la petite image issue de Google');
                    $book->setSmallImageUrl($bookFromGoogle->getSmallImageUrl());
                }
                if ((!$book->getPublishingDate()) && $bookFromGoogle->getPublishingDate()) {
                    \Sb\Trace\Trace::addItem('Utilisation de la date de publication issue de Google');
                    $book->setPublishingDate($bookFromGoogle->getPublishingDate());
                }
            } else {
                \Sb\Trace\Trace::addItem('Le livre n\'a pas été trouvé sur Google.');
            }
        } catch (\Exception $exc) {
            \Sb\Trace\Trace::addItem(sprintf("Une erreur s'est produite lors de l'appel à l'api google books : %s", $exc->getMessage()));
        }
    }

}