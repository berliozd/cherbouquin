<?php

namespace Sb\View;

/**
 * Description of BookList
 *
 * @author Didier
 */
class BookTable extends \Sb\View\AbstractView {

    private $mainPageName;
    private $currentLibraryPageName;
    private $shownResults;
    private $listOptions;
    private $pagerLinks;
    private $firstItemIdx;
    private $lastItemIdx;
    private $nbItemsTot;
    private $friendLibrary;
    private $searchValue;
    private $authorsFirstLetters;
    private $titlesFirstLetters;
    private $filteringType;
    private $filter;
    private $key;

    function __construct($key, $mainPageName, $friendLibraryPageName, $shownResults, $pagerLinks, $firstItemIdx, $lastItemIdx, $nbItemsTot, $listOptions, $friendLibrary, $searchValue, $authorsFirstLetters, $titlesFirstLetters, $filteringType, $filter) {
        parent::__construct();

        $this->key = $key;
        $this->mainPageName = $mainPageName;
        if ($friendLibrary)
            $this->currentLibraryPageName = $friendLibraryPageName;
        else
            $this->currentLibraryPageName = $mainPageName;
        $this->shownResults = $shownResults;
        $this->listOptions = $listOptions;
        $this->pagerLinks = $pagerLinks;
        $this->firstItemIdx = $firstItemIdx;
        $this->lastItemIdx = $lastItemIdx;
        $this->nbItemsTot = $nbItemsTot;
        $this->friendLibrary = $friendLibrary;
        $this->searchValue = $searchValue;
        $this->authorsFirstLetters = $authorsFirstLetters;
        $this->titlesFirstLetters = $titlesFirstLetters;
        $this->filteringType = $filteringType;
        $this->filter = $filter;
    }

    public function get() {

        $lineIdx = 0;
        $userBooks = $this->shownResults;
        $booksTemplates = array();
        if ($userBooks) {
            $i = 0;
            foreach ($userBooks as $userBook) {

                $i++;

                $addSep = true;
                if ($i == 1) {
                    $addSep = false;
                }

                //$book = new \Sb\Db\Model\Book;
                $book = $userBook->getBook();
                // Get row template
                $rowTpl = new \Sb\Templates\Template("bookList/bookTableRow");

                $pictos = \Sb\Helpers\UserBookHelper::getStatusPictos($userBook, $this->friendLibrary);

                $rowTpl->set("pictos", $pictos);

                $cssClass = (($lineIdx % 2) ? "lineA" : "lineB");
                $rowTpl->set("cssClass", $cssClass);

                $img = "";
                if ($book->getSmallImageUrl()) {
                    $img = sprintf("<img src = '%s' class = 'image-thumb-small image-frame'/>", $book->getSmallImageUrl());
                } else {
                    $img = sprintf("<img src = '%s' border = '0' class = 'image-thumb-small image-frame'/>", $this->defImg);
                }
                $rowTpl->set("img", $img);

                $rowTpl->set("title", $book->getTitle());
                if ($book->getPublisher()) {
                    $rowTpl->set("publicationInfo", $book->getPublicationInfo());
                } else {
                    $rowTpl->set("publicationInfo", "");
                }

                $rowTpl->set("author", $book->getOrderableContributors());

                $status = "";
                if ($userBook->getReadingState())
                    $status = $userBook->getReadingState()->getLabel();

                $readingStateSvc = \Sb\Db\Service\ReadingStateSvc::getInstance();
                $readState = $readingStateSvc->getReadSate();
                if ($userBook->getReadingState() && ($userBook->getReadingState()->getId() == $readState->getId())) {
                    if ($userBook->getReadingDate()) {
                        $status = sprintf(__("%s le %s", "s1b"), $status, $userBook->getReadingDate()->format(__("d/m/Y", "s1b")));
                    }
                }

                $rowTpl->set("status", $status);

                $rating = $userBook->getRating();
                if ($rating || $rating == 0) {
                    $ratingCssClass = "rating-" . $rating;
                    $rowTpl->set("ratingCssClass", "stars " . $ratingCssClass);
                } else {
                    $rowTpl->set("ratingCssClass", "");
                }

                if ($userBook->getIsBlowOfHeart()) {
                    $rowTpl->set("bohCssClass", "boh");
                } else {
                    $rowTpl->set("bohCssClass", "");
                }

                $editLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_LIBRARY_DETAIL, array("page" => \Sb\Entity\LibraryPages::USERBOOK_EDIT, "ubid" => $userBook->getId()));

                $deleteLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_LIBRARY_DETAIL, array("page" => \Sb\Entity\LibraryPages::USERBOOK_DELETE, "ubid" => $userBook->getId()));

                //$viewLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_LIBRARY_DETAIL, array("page" => \Sb\Entity\LibraryPages::BOOK_VIEW, "bid" => $book->getId()));
                $viewLink = \Sb\Helpers\HTTPHelper::Link($book->getLink());

                // Showing "Borrow this book" link only if:
                // - friend ownes the book
                // - book is not lent
                $borrowLink = null;
                if ($this->friendLibrary && $userBook->getIsOwned() && !$userBook->getActiveLending())
                    $borrowLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_LIBRARY_DETAIL, array("page" => \Sb\Entity\LibraryPages::LENDING_BORROW, "ubid" => $userBook->getId()));

                $rowTpl->setVariables(array("addSep" => $addSep,
                    "friendLibrary" => $this->friendLibrary,
                    "editLink" => $editLink,
                    "deleteLink" => $deleteLink,
                    "viewLink" => $viewLink,
                    "borrowLink" => $borrowLink,
                    "bookId" => $book->getId()));

                $booksTemplates[] = $rowTpl;

                $lineIdx++;

                unset($addSep);
                unset($book);
                unset($rowTpl);
                unset($pictos);
                unset($cssClass);
                unset($img);
                unset($status);
                unset($rating);
                unset($ratingCssClass);
                unset($editLink);
                unset($deleteLink);
                unset($viewLink);
                unset($borrowLink);
                unset($userBook);
            }
        }


        $bookListTpl = new \Sb\Templates\Template("bookList/bookTable");
        if ($booksTemplates) {
            
            // Get row header template
            $headerTpl = new \Sb\Templates\Template("bookList/bookTableHeader");

            // Assignation des classe pour afficher le petit picto indiquant le sens de tri
            if ($this->listOptions) {
                if ($this->listOptions->getSorting()) {
                    if ($this->listOptions->getSorting()->getField() == \Sb\Helpers\BooksHelper::SORTING_FIELD_AUTHOR) {
                        $headerTpl->set("titlesortingdirection", "");
                        $headerTpl->set("ratingsortingdirection", "");
                        $headerTpl->set("authorsortingdirection", $this->listOptions->getSorting()->getDirection());
                        $headerTpl->set("statesortingdirection", "");
                    }
                    if ($this->listOptions->getSorting()->getField() == \Sb\Helpers\BooksHelper::SORTING_FIELD_RATING) {
                        $headerTpl->set("titlesortingdirection", "");
                        $headerTpl->set("ratingsortingdirection", $this->listOptions->getSorting()->getDirection());
                        $headerTpl->set("authorsortingdirection", "");
                        $headerTpl->set("statesortingdirection", "");
                    }
                    if ($this->listOptions->getSorting()->getField() == \Sb\Helpers\BooksHelper::SORTING_FIELD_TITLE) {
                        $headerTpl->set("titlesortingdirection", $this->listOptions->getSorting()->getDirection());
                        $headerTpl->set("ratingsortingdirection", "");
                        $headerTpl->set("authorsortingdirection", "");
                        $headerTpl->set("statesortingdirection", "");
                    }
                    if ($this->listOptions->getSorting()->getField() == \Sb\Helpers\BooksHelper::SORTING_FIELD_STATE) {
                        $headerTpl->set("titlesortingdirection", "");
                        $headerTpl->set("ratingsortingdirection", "");
                        $headerTpl->set("authorsortingdirection", "");
                        $headerTpl->set("statesortingdirection", $this->listOptions->getSorting()->getDirection());
                    }
                }
            }

            $headerTpl->setVariables(array("friendLibrary" => $this->friendLibrary));

            $bookListTpl->set("tableHeader", $headerTpl->output());
            $booksToShow = \Sb\Templates\Template::merge($booksTemplates);
            $bookListTpl->set("booksToShow", $booksToShow);
            $links = $this->pagerLinks;
            $bookListTpl->set("links", $links['all']);
            $bookListTpl->set("first", $this->firstItemIdx);
            $bookListTpl->set("last", $this->lastItemIdx);
            $bookListTpl->set("nbItemsTot", $this->nbItemsTot);
            $listSearchDefValue = __("Un titre, un auteur, ISBN dans ma bibliothèque", "s1b");
            $bookListTpl->set("listSearchDefValue", $listSearchDefValue);
            if ($this->searchValue) {
                $bookListTpl->set("listSearchValue", $this->searchValue);
            } else {
                $bookListTpl->set("listSearchValue", $listSearchDefValue);
            }
            $key = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, "key", null);
            $bookListTpl->set("key", $key);
            $bookListTpl->set("reinitUrl", \Sb\Helpers\HTTPHelper::Link($this->currentLibraryPageName, array("key" => $key,
                        "page" => \Sb\Entity\LibraryPages::BOOK_LIST)));


            $selectedAuthorLetter = null;
            $selectedTitleLetter = null;
            $filtertype = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, "filtertype", null);
            if ($filtertype == \Sb\Lists\FilteringType::AUTHOR_FIRST_LETTER)
                $selectedAuthorLetter = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, "filter", null);
            elseif ($filtertype == \Sb\Lists\FilteringType::TITLE_FIRST_LETTER)
                $selectedTitleLetter = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, "filter", null);

            $bookListTpl->setVariables(array("authorsFirstLetters" => $this->authorsFirstLetters,
                "titlesFirstLetters" => $this->titlesFirstLetters,
                "selectedTitleLetter" => $selectedTitleLetter,
                "selectedAuthorLetter" => $selectedAuthorLetter,
                "emptyList" => false));            
        } else
            $bookListTpl->setVariables(array("emptyList" => true));
        
        return $bookListTpl->output();
    }

}