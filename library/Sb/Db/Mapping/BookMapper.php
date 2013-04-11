<?php

namespace Sb\Db\Mapping;

/**
 * Description of BookMapper
 *
 * @author Didier
 */
class BookMapper implements \Sb\Db\Mapping\Mapper {

    /**
     * Créé un obj Book à partir du formulaire d'ajout
     * @param \Sb\Db\Model\Book $book
     * @param array $properties
     */
    public static function map(\Sb\Db\Model\Model &$book, array $properties, $prefix = "") {

        if (array_key_exists($prefix . 'Author', $properties)) {
            $authorsVal = urldecode($properties[$prefix . 'Author']);
            // testing if the book has more than one author
            $hasMany = (strpos($authorsVal, ",") !== false);
            // adding an array of contributor
            $contributors = new \Doctrine\Common\Collections\ArrayCollection();
            if ($hasMany) {
                $authors = explode(",", $authorsVal);
                foreach ($authors as $author) {

                    $contributor = \Sb\Db\Dao\ContributorDao::getInstance()->getByFullName($author);
                    if (!$contributor) {
                        $contributor = new \Sb\Db\Model\Contributor();
                        $contributor->setCreationDate(new \DateTime());
                        $contributor->setLastModificationDate(new \DateTime());
                        $contributor->setFullName($author);
                    }

                    $contributors->add($contributor);
                }
                $book->setContributors($contributors);
            } else { // adding just one contributor
                $contributor = \Sb\Db\Dao\ContributorDao::getInstance()->getByFullName($authorsVal);
                if (!$contributor) {
                    $contributor = new \Sb\Db\Model\Contributor();
                    $contributor->setCreationDate(new \DateTime());
                    $contributor->setLastModificationDate(new \DateTime());
                    $contributor->setFullName($authorsVal);
                }

                $contributors->add($contributor);
                $book->setContributors($contributors);
            }
        }

        if (array_key_exists($prefix . 'Language', $properties)) {
            $book->setLanguage(urldecode($properties[$prefix . 'Language']));
        }
        if (array_key_exists($prefix . 'Description', $properties)) {
            $book->setDescription(urldecode($properties[$prefix . 'Description']));
        }
        if (array_key_exists($prefix . 'ISBN10', $properties)) {
            $book->setISBN10($properties[$prefix . 'ISBN10']);
        }
        if (array_key_exists($prefix . 'ISBN13', $properties)) {
            $book->setISBN13($properties[$prefix . 'ISBN13']);
        }
        if (array_key_exists($prefix . 'ASIN', $properties)) {
            $book->setASIN($properties[$prefix . 'ASIN']);
        }
        if (array_key_exists($prefix . 'Id', $properties)) {
            $book->setId($properties[$prefix . 'Id']);
        }
        if (array_key_exists($prefix . 'ImageBinary', $properties)) {
            $book->setImageBinary($properties[$prefix . 'ImageBinary']);
        }
        if (array_key_exists($prefix . 'ImageUrl', $properties)) {
            $book->setImageUrl($properties[$prefix . 'ImageUrl']);
        }
        if (array_key_exists($prefix . 'LargeImageUrl', $properties)) {
            $book->setLargeImageUrl($properties[$prefix . 'LargeImageUrl']);
        }
        if (array_key_exists($prefix . 'SmallImageUrl', $properties)) {
            $book->setSmallImageUrl($properties[$prefix . 'SmallImageUrl']);
        }

        if (array_key_exists($prefix . 'Publisher', $properties)) {
            $publisherVal = urldecode($properties[$prefix . 'Publisher']);
            $publisher = \Sb\Db\Dao\PublisherDao::getInstance()->getByName($publisherVal);
            if (!$publisher) {
                $publisher = new \Sb\Db\Model\Publisher;
                $publisher->setCreationDate(new \DateTime());
                $publisher->setLastModificationDate(new \DateTime());
                $publisher->setName($publisherVal);
            }

            $book->setPublisher($publisher);
        }

        if (array_key_exists($prefix . 'Title', $properties)) {
            $book->setTitle(urldecode($properties[$prefix . 'Title']));
        }
        if (array_key_exists($prefix . 'CreationDate', $properties)) {
            $book->setCreationDate(\Sb\Helpers\DateHelper::createDateTime($properties[$prefix . 'CreationDate']));
        }
        if (array_key_exists($prefix . 'LastModificationDate', $properties)) {
            $book->setLastModificationDate(\Sb\Helpers\DateHelper::createDateTime($properties[$prefix . 'LastModificationDate']));
        }
        if (array_key_exists($prefix . 'PublishingDate', $properties)) {
            $book->setPublishingDate(\Sb\Helpers\DateHelper::createDateTime($properties[$prefix . 'PublishingDate']));
        }
        if (array_key_exists($prefix . 'AmazonUrl', $properties)) {
            $book->setAmazonUrl(urldecode($properties[$prefix . 'AmazonUrl']));
        }

        if (array_key_exists($prefix . 'NbOfPages', $properties)) {
            $book->setNb_of_pages($properties[$prefix . 'NbOfPages']);
        }
    }

    public static function mapFromGoogleBookVolumeInfo(\Sb\Db\Model\Model &$book, $googleBookVolumeinfo) {

        $book->setDescription(\Sb\Helpers\ArrayHelper::getSafeFromArray($googleBookVolumeinfo, 'description', ''));
        $book->setTitle(\Sb\Helpers\ArrayHelper::getSafeFromArray($googleBookVolumeinfo, 'title', ''));

        $publishedDateStr = \Sb\Helpers\ArrayHelper::getSafeFromArray($googleBookVolumeinfo, 'publishedDate', '');
        if ($publishedDateStr) {
            $book->setPublishingDate(\Sb\Helpers\DateHelper::createDate($publishedDateStr));
        }

        $imageLinks = \Sb\Helpers\ArrayHelper::getSafeFromArray($googleBookVolumeinfo, 'imageLinks', null);
        if ($imageLinks) {
            $book->setImageUrl(\Sb\Helpers\ArrayHelper::getSafeFromArray($imageLinks, 'thumbnail', null));
            $book->setSmallImageUrl(\Sb\Helpers\ArrayHelper::getSafeFromArray($imageLinks, 'smallThumbnail', null));
        }
    }

    public static function mapFromAmazonResult(\Sb\Db\Model\Book &$book, \Zend_Service_Amazon_Item $amazonResult) {

        if (isset($amazonResult->Language))
            $book->setLanguage($amazonResult->Language);

        if (isset($amazonResult->Author)) {
            $contributors = new \Doctrine\Common\Collections\ArrayCollection();
            if (is_array($amazonResult->Author)) {
                foreach ($amazonResult->Author as $author) {
                    $contributor = new \Sb\Db\Model\Contributor;
                    $contributor->setFullName($author);
                    $contributors->add($contributor);
                }
            } else {
                $contributor = new \Sb\Db\Model\Contributor;
                $contributor->setFullName($amazonResult->Author);
                $contributors->add($contributor);
            }
            $book->setContributors($contributors);
        }

        if (isset($amazonResult->EditorialReviews)) {
            if (count($amazonResult->EditorialReviews) > 0) {
                //$book->setDescription($amazonResult->EditorialReviews[0]->Content);
                // Replace all HTML in description by ' ' to prevent bad formatting HTML
                $book->setDescription(preg_replace('/<[^>]*>/', ' ', $amazonResult->EditorialReviews[0]->Content));
            }
        }

        if (isset($amazonResult->ISBN)) {
            $book->setISBN10($amazonResult->ISBN);
        }
        if (isset($amazonResult->EAN)) {
            $book->setISBN13($amazonResult->EAN);
        }
        if (isset($amazonResult->ASIN)) {
            $book->setASIN($amazonResult->ASIN);
        }

        if (isset($amazonResult->SmallImage)) {
            if (($amazonResult->SmallImage) && ($amazonResult->SmallImage->Url)) {
                $book->setSmallImageUrl($amazonResult->SmallImage->Url->getUri());
            }
        }

        if (isset($amazonResult->MediumImage)) {
            if (($amazonResult->MediumImage) && ($amazonResult->MediumImage->Url)) {
                $book->setImageUrl($amazonResult->MediumImage->Url->getUri());
            }
        }

        if (isset($amazonResult->LargeImage)) {
            if (($amazonResult->LargeImage) && ($amazonResult->LargeImage->Url)) {
                $book->setLargeImageUrl($amazonResult->LargeImage->Url->getUri());
            }
        }

        //Publisher
        if (isset($amazonResult->PublicationDate)) {
            $book->setPublishingDate(\Sb\Helpers\DateHelper::createDate($amazonResult->PublicationDate));
        }

        if (isset($amazonResult->Publisher)) {
            $publisher = new \Sb\Db\Model\Publisher;
            $publisher->setName($amazonResult->Publisher);
            $book->setPublisher($publisher);
        }

        if (isset($amazonResult->Title)) {
            $book->setTitle($amazonResult->Title);
        }

        if (isset($amazonResult->DetailPageURL)) {
            $book->setAmazonUrl($amazonResult->DetailPageURL);
        }

        if (isset($amazonResult->NumberOfPages)) {
            $book->setNb_of_pages($amazonResult->NumberOfPages);
        }
    }

    /**
     * Créé un tableau associatif à partir de l'objet Book
     * @param \Sb\Db\Model\Book $book
     * @param array $properties
     */
    public static function reverseMap(\Sb\Db\Model\Model $book, array &$properties) {
        $properties['author'] = $book->getAuthor();
        $properties['description'] = $book->getDescription();
        $properties['isbn10'] = $book->getISBN10();
        $properties['isbn13'] = $book->getISBN13();
        $properties['asin'] = $book->getASIN();
        $properties['id'] = $book->getId();
        $properties['image_binary'] = $book->getImageBinary();
        $properties['image_url'] = $book->getImageUrl();
        $properties['small_image_url'] = $book->getSmallImageUrl();
        $properties['large_image_url'] = $book->getLargeImageUrl();
        $properties['publisher'] = $book->getPublisher();
        $properties['publisher_id'] = $book->getPublisherId();
        $properties['title'] = $book->getTitle();
        if ($book->getCreationDate()) {
            $properties['creation_date'] = \Sb\Helpers\DateHelper::getDateForDB($book->getCreationDate());
        }
        if ($book->getLastModificationDate()) {
            $properties['last_modification_date'] = \Sb\Helpers\DateHelper::getDateForDB($book->getLastModificationDate());
        }
        if ($book->getPublishingDate()) {
            $properties['publishing_date'] = \Sb\Helpers\DateHelper::getDateForDB($book->getPublishingDate());
        }
        $properties['amazon_url'] = $book->getAmazonUrl();
    }

}

?>
