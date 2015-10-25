<?php

namespace Sb\Helpers;

/**
 * @author Didier
 */
class BooksHelper {

    const PICTO_OWNED = "PICTO_OWNED";
    const PICTO_WISHED = "PICTO_WISHED";
    const PICTO_LENT = "PICTO_LENT";
    const PICTO_BORROWED = "PICTO_BORROWED";
    const PICTO_BORROWED_ONCE = "PICTO_WAS_BORROWED";
    const PICTO_LENDING = "PICTO_LENDING";
    const PICTO_RETURN_TO_VALIDATE = "PICTO_RETURN_TO_VALIDATE";
    const SORTING_FIELD_TITLE = "title";
    const SORTING_FIELD_AUTHOR = "author";
    const SORTING_FIELD_RATING = "rating";
    const SORTING_FIELD_STATE = "state";
    const SORTING_FIELD_PUBLISHING_DATE = "publishing_date";

    private static $searchValue;
    private static $filteringValue;
    private static $filteringType;

    public static function sort(&$books, \Sb\Lists\Sorting $sorting) {
        $className = get_class();
        $sortingField = $sorting->getField();
        $sortingDirection = $sorting->getDirection();
        switch ($sortingField) {
            case self::SORTING_FIELD_RATING:
                switch ($sortingDirection) {
                    case EntityHelper::ASC:
                        \Sb\Trace\Trace::addItem($className . "::compareByRatingAsc");
                        usort($books, $className . "::compareByRatingAsc");
                        break;
                    case EntityHelper::DESC:
                        \Sb\Trace\Trace::addItem($className . "::compareByRatingDesc");
                        usort($books, $className . "::compareByRatingDesc");
                        break;
                }
                break;
            case self::SORTING_FIELD_TITLE:
                switch ($sortingDirection) {
                    case EntityHelper::ASC:
                        \Sb\Trace\Trace::addItem($className . "::compareByTitleAsc");
                        usort($books, $className . "::compareByTitleAsc");
                        break;
                    case EntityHelper::DESC:
                        \Sb\Trace\Trace::addItem($className . "::compareByTitleDesc");
                        usort($books, $className . "::compareByTitleDesc");
                        break;
                }
                break;
            case self::SORTING_FIELD_AUTHOR:
                switch ($sortingDirection) {
                    case EntityHelper::ASC:
                        \Sb\Trace\Trace::addItem($className . "::compareByAuthorAsc");
                        usort($books, $className . "::compareByAuthorAsc");
                        break;
                    case EntityHelper::DESC:
                        \Sb\Trace\Trace::addItem($className . "::compareByAuthorDesc");
                        usort($books, $className . "::compareByAuthorDesc");
                        break;
                }
                break;
            case self::SORTING_FIELD_STATE:
                switch ($sortingDirection) {
                    case EntityHelper::ASC:
                        \Sb\Trace\Trace::addItem($className . "::compareByStateAsc");
                        usort($books, $className . "::compareByStateAsc");
                        break;
                    case EntityHelper::DESC:
                        \Sb\Trace\Trace::addItem($className . "::compareByStateDesc");
                        usort($books, $className . "::compareByStateDesc");
                        break;
                }
                break;
            case self::SORTING_FIELD_PUBLISHING_DATE:
                switch ($sortingDirection) {
                    case EntityHelper::ASC:
                        \Sb\Trace\Trace::addItem($className . "::compareByPublishingDateAsc");
                        usort($books, $className . "::compareByPublishingDateAsc");
                        break;
                    case EntityHelper::DESC:
                        \Sb\Trace\Trace::addItem($className . "::compareByPublishingDateDesc");
                        usort($books, $className . "::compareByPublishingDateDesc");
                        break;
                }
                break;
        }
    }

    public static function compareByTitleAsc(\Sb\Db\Model\UserBook $book1, \Sb\Db\Model\UserBook $book2) {
        return EntityHelper::compareBy($book1->getBook(), $book2->getBook(), EntityHelper::ASC, "getTitle");
    }

    public static function compareByTitleDesc(\Sb\Db\Model\UserBook $book1, \Sb\Db\Model\UserBook $book2) {
        return EntityHelper::compareBy($book1->getBook(), $book2->getBook(), EntityHelper::DESC, "getTitle");
    }

    public static function compareByAuthorAsc(\Sb\Db\Model\UserBook $userBook1, \Sb\Db\Model\UserBook $userBook2) {
        return EntityHelper::compareBy($userBook1->getBook(), $userBook2->getBook(), EntityHelper::ASC, "getOrderableContributors");
    }

    public static function compareByAuthorDesc(\Sb\Db\Model\UserBook $userBook1, \Sb\Db\Model\UserBook $userBook2) {
        return EntityHelper::compareBy($userBook1->getBook(), $userBook2->getBook(), EntityHelper::DESC, "getOrderableContributors");
    }

    public static function compareByRatingDesc(\Sb\Db\Model\UserBook $book1, \Sb\Db\Model\UserBook $book2) {
        return EntityHelper::compareBy($book1, $book2, EntityHelper::DESC, "getRating");
    }

    public static function compareByRatingAsc(\Sb\Db\Model\UserBook $book1, \Sb\Db\Model\UserBook $book2) {
        return EntityHelper::compareBy($book1, $book2, EntityHelper::ASC, "getRating");
    }

    public static function compareByStateDesc(\Sb\Db\Model\UserBook $book1, \Sb\Db\Model\UserBook $book2) {
        return EntityHelper::compareBy($book1->getReadingState(), $book2->getReadingState(), EntityHelper::DESC, "getId");
    }

    public static function compareByStateAsc(\Sb\Db\Model\UserBook $book1, \Sb\Db\Model\UserBook $book2) {
        return EntityHelper::compareBy($book1->getReadingState(), $book2->getReadingState(), EntityHelper::ASC, "getId");
    }

    public static function compareByPublishingDateAsc(\Sb\Db\Model\Book $book1, \Sb\Db\Model\Book $book2) {
        return EntityHelper::compareBy($book1, $book2, EntityHelper::ASC, "getPublishingDate");
    }

    public static function compareByPublishingDateDesc(\Sb\Db\Model\Book $book1, \Sb\Db\Model\Book $book2) {
        return EntityHelper::compareBy($book1, $book2, EntityHelper::DESC, "getPublishingDate");
    }

    public static function getStatusPicto($pictoType) {
        switch ($pictoType) {
            case self::PICTO_BORROWED:
                $cssClass = "picto-borrowed-small";
                break;
            case self::PICTO_OWNED:
                $cssClass = "picto-owned-small";
                break;
            case self::PICTO_LENT:
                $cssClass = "picto-lent-small";
                break;
            case self::PICTO_WISHED:
                $cssClass = "picto-wished-small";
                break;
            case self::PICTO_LENDING:
                $cssClass = "picto-lendable-small";
                break;
            case self::PICTO_BORROWED_ONCE:
                $cssClass = "pictoBorrowedOnce";
                break;
            case self::PICTO_RETURN_TO_VALIDATE:
                $cssClass = "picto-returntovalidate-small";
                break;            
            default:
                break;
        }
        return sprintf("<div class=\"picto-small %s\"></div>", $cssClass);
    }

    public static function search(&$books, $searchValue) {
        $className = get_class();
        self::$searchValue = $searchValue;
        $books = array_filter($books, $className . "::isBookValidForSearch");
        return $books;
    }

    public static function filter(&$books, $fileringValue, $filteringType) {
        $className = get_class();
        self::$filteringValue = $fileringValue;
        self::$filteringType = $filteringType;
        $books = array_filter($books, $className . "::isBookValidForFiltering");
        return $books;
    }

    private static function isBookValidForFiltering(\Sb\Db\Model\UserBook $userBook) {
        $book = $userBook->getBook();
        if (self::$filteringType == \Sb\Lists\FilteringType::AUTHOR_FIRST_LETTER) {
            if (strpos(strtoupper($book->getOrderableContributors()), self::$filteringValue) === 0)
                return true;
        }

        if (self::$filteringType == \Sb\Lists\FilteringType::TITLE_FIRST_LETTER) {
            if (strpos(strtoupper($book->getTitle()), self::$filteringValue) === 0)
                return true;
        }
        return false;
    }

    private static function isBookValidForSearch(\Sb\Db\Model\UserBook $userBook) {
        $book = $userBook->getBook();
        $ret = false;
        $pattern = "/" . self::$searchValue . "/i";
        if ($book) {
            if (preg_match($pattern, $book->getTitle())) {
                \Sb\Trace\Trace::addItem("trouvé => pattern : " . $pattern . " dans :" . $book->getTitle());
                $ret = true;
            }
            if (preg_match($pattern, $book->getOrderableContributors())) {
                \Sb\Trace\Trace::addItem("trouvé => pattern : " . $pattern . " dans :" . $book->getOrderableContributors());
                $ret = true;
            }
        }
        return $ret;
    }

}

?>
