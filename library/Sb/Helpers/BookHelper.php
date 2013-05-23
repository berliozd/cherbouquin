<?php

namespace Sb\Helpers;

use \Sb\Db\Model\Book;
use Sb\Service\MailSvc;
use \Sb\Entity\Constants;

/**
 * Description of BookHelper
 * @author Didier
 */
class BookHelper {

    /**
     *
     * @return Config
     */
    private static function getConfig() {

        global $globalConfig;
        return $globalConfig;
    }

    public static function getSmallImageTag(Book $book, $defaultImg) {

        return ImageHelper::getSmallImageTag($book->getSmallImageUrl(), $book->getTitle(), $defaultImg);
    }

    public static function getMediumImageTag(Book $book, $defaultImg) {

        return ImageHelper::getMediumImageTag($book->getImageUrl(), $book->getTitle(), $defaultImg);
    }

    public static function getMediumImageTagForFlipCarousel(Book $book, $defaultImg) {

        $href = HTTPHelper::Link($book->getLink());
        
        return ImageHelper::getMediumImageTagForFlipCarousel($book->getImageUrl(), $href, $book->getTitle(), $defaultImg);
    }

    public static function getLargeImageTag(Book $book, $defaultImg) {

        return ImageHelper::getLargeImageTag($book->getLargeImageUrl(), $book->getImageUrl(), $book->getTitle(), $defaultImg);
    }

    public static function getDefaultImage() {

        return BASE_URL . 'Resources/images/nocover.png';
    }

    public static function completeInfos(Book &$book) {

        try {
            
            $config = self::getConfig();
            
            $googleBook = new \Sb\Google\Model\GoogleBook($book->getISBN10(), $book->getISBN13(), $book->getASIN(), $config->getGoogleApiKey());
            
            if ($googleBook->getVolumeInfo()) {
                \Sb\Trace\Trace::addItem('Le livre a été trouvé sur Google.');
                $bookFromGoogle = new Book();
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
            $message = sprintf("Une erreur s'est produite lors de l'appel à l'api google books : %s", $exc->getMessage());
            MailSvc::getInstance()->send(Constants::WEBMASTER_EMAIL, "Erreur interne", $message);
            \Sb\Trace\Trace::addItem($message);
        }
    }

}