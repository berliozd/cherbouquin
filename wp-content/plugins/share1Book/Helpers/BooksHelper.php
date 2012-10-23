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
    const DESC = "DESC";
    const ASC = "ASC";
    const SORTING_FIELD_TITLE = "title";
    const SORTING_FIELD_AUTHOR = "author";
    const SORTING_FIELD_RATING = "rating";
    const SORTING_FIELD_STATE = "state";

    private static $searchValue;
    private static $filteringValue;
    private static $filteringType;

    public static function sort(&$books, \Sb\Lists\Sorting $sorting) {
//        var_dump($sorting);
        $className = get_class();
        $sortingField = $sorting->getField();
        $sortingDirection = $sorting->getDirection();
        switch ($sortingField) {
            case self::SORTING_FIELD_RATING:
                switch ($sortingDirection) {
                    case self::ASC:
                        \Sb\Trace\Trace::addItem($className . "::compareByRatingAsc");
                        usort($books, $className . "::compareByRatingAsc");
                        break;
                    case self::DESC:
                        \Sb\Trace\Trace::addItem($className . "::compareByRatingDesc");
                        usort($books, $className . "::compareByRatingDesc");
                        break;
                }
                break;
            case self::SORTING_FIELD_TITLE:
                switch ($sortingDirection) {
                    case self::ASC:
                        \Sb\Trace\Trace::addItem($className . "::compareByTitleAsc");
                        usort($books, $className . "::compareByTitleAsc");
                        break;
                    case self::DESC:
                        \Sb\Trace\Trace::addItem($className . "::compareByTitleDesc");
                        usort($books, $className . "::compareByTitleDesc");
                        break;
                }
                break;
            case self::SORTING_FIELD_AUTHOR:
                switch ($sortingDirection) {
                    case self::ASC:
                        \Sb\Trace\Trace::addItem($className . "::compareByAuthorAsc");
                        usort($books, $className . "::compareByAuthorAsc");
                        break;
                    case self::DESC:
                        \Sb\Trace\Trace::addItem($className . "::compareByAuthorDesc");
                        usort($books, $className . "::compareByAuthorDesc");
                        break;
                }
                break;
            case self::SORTING_FIELD_STATE:
                switch ($sortingDirection) {
                    case self::ASC:
                        \Sb\Trace\Trace::addItem($className . "::compareByStateAsc");
                        usort($books, $className . "::compareByStateAsc");
                        break;
                    case self::DESC:
                        \Sb\Trace\Trace::addItem($className . "::compareByStateDesc");
                        usort($books, $className . "::compareByStateDesc");
                        break;
                }
                break;
        }
    }

    public static function compareBy(\Sb\Db\Model\Model $book1, \Sb\Db\Model\Model $book2, $direction, $sortingFunction) {
        $val1 = strtoupper(call_user_func(array(&$book1, $sortingFunction)));
        $val2 = strtoupper(call_user_func(array(&$book2, $sortingFunction)));
        if ($val1 == $val2) {
            return 0;
        }
        if ($direction == self::ASC) {
            return ($val1 < $val2) ? -1 : 1;
        } else {
            return ($val1 > $val2) ? -1 : 1;
        }
    }

    public static function compareByTitleAsc(\Sb\Db\Model\UserBook $book1, \Sb\Db\Model\UserBook $book2) {
        return self::compareBy($book1->getBook(), $book2->getBook(), self::ASC, "getTitle");
    }

    public static function compareByTitleDesc(\Sb\Db\Model\UserBook $book1, \Sb\Db\Model\UserBook $book2) {
        return self::compareBy($book1->getBook(), $book2->getBook(), self::DESC, "getTitle");
    }

    public static function compareByAuthorAsc(\Sb\Db\Model\UserBook $userBook1, \Sb\Db\Model\UserBook $userBook2) {
        return self::compareBy($userBook1->getBook(), $userBook2->getBook(), self::ASC, "getOrderableContributors");
    }

    public static function compareByAuthorDesc(\Sb\Db\Model\UserBook $userBook1, \Sb\Db\Model\UserBook $userBook2) {
        return self::compareBy($userBook1->getBook(), $userBook2->getBook(), self::DESC, "getOrderableContributors");
    }

    public static function compareByRatingDesc(\Sb\Db\Model\UserBook $book1, \Sb\Db\Model\UserBook $book2) {
        return self::compareBy($book1, $book2, self::DESC, "getRating");
    }

    public static function compareByRatingAsc(\Sb\Db\Model\UserBook $book1, \Sb\Db\Model\UserBook $book2) {
        return self::compareBy($book1, $book2, self::ASC, "getRating");
    }

    public static function compareByStateDesc(\Sb\Db\Model\UserBook $book1, \Sb\Db\Model\UserBook $book2) {
        return self::compareBy($book1->getReadingState(), $book2->getReadingState(), self::DESC, "getId");
    }

    public static function compareByStateAsc(\Sb\Db\Model\UserBook $book1, \Sb\Db\Model\UserBook $book2) {
        return self::compareBy($book1->getReadingState(), $book2->getReadingState(), self::ASC, "getId");
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
//
//        <div class="picto-small picto-owned-small"></div>
//        <div class="picto-small picto-lent-small"></div>
//        <div class="picto-small picto-borrowed-small"></div>
//        <div class="picto-small picto-returntovalidate-small"></div>
//        <div class="picto-small picto-lendable-small"></div>
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
