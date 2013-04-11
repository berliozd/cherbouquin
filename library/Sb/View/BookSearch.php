<?php

namespace Sb\View;

/**
 * Description of Sp_View_BookSearch
 *
 * @author Didier
 */
class BookSearch extends \Sb\View\AbstractView {

    private $shownResults;
    private $pagerLinks;
    private $firstItemIdx;
    private $lastItemIdx;
    private $nbItemsTot;

    function __construct($shownResults, $pagerLinks, $firstItemIdx, $lastItemIdx, $nbItemsTot) {
        parent::__construct();
        $this->shownResults = $shownResults;
        $this->pagerLinks = $pagerLinks;
        $this->firstItemIdx = $firstItemIdx;
        $this->lastItemIdx = $lastItemIdx;
        $this->nbItemsTot = $nbItemsTot;
    }

    public function get() {
        $lineIdx = 0;
        foreach (array_values($this->shownResults) as $book) {
            $lineIdx++;

            $addSep = true;
            if ($lineIdx == 1)
                $addSep = false;

            $bk = $book;

            $language = urlencode($bk->getLanguage());

            $imgSrc = "";
            if ($bk->getImageUrl()) {
                $imgSrc = $bk->getImageUrl();
            } else {
                $imgSrc = $this->defImg;
            }

            // Utilisation de urlencode à la place htmlspecialchars car ce dernier pose des pbs qd la valeur est ensuite passée en post

            $title = $bk->getTitle();
            $titleEsc = urlencode($bk->getTitle()); // encodé

            $author = $bk->getOrderableContributors();
            $authorEsc = urlencode($bk->getOrderableContributors()); // encodé

            $id = $bk->getId();
            $isbn10 = $bk->getISBN10();
            $isbn13 = $bk->getISBN13();
            $asin = $bk->getASIN();

            $desc = \Sb\Helpers\StringHelper::tronque($bk->getDescription(), 350);
            $descEsc = urlencode($bk->getDescription()); // encodé

            $smallImg = $bk->getSmallImageUrl();
            $img = $bk->getImageUrl();
            $largeImg = $bk->getLargeImageUrl();

            $pubEsc = "";
            $pubInfo = "";
            if ($bk->getPublisher()) {
                $pubEsc = urlencode($bk->getPublisher()->getName()); // encodé
                $pubInfo = $bk->getPublicationInfo();
            }

            $pubDtStr = "";
            if ($book->getPublishingDate())
                $pubDtStr = $book->getPublishingDate()->format("Y-m-d H:i:s");

            $amazonUrl = $book->getAmazonUrl();

            $nbOfPages = $book->getNb_of_pages();

            $cssClass = (($lineIdx % 2) ? "lineA" : "lineB");

            $viewBookLink = null;
            $bookInDB = false;
            if ($bk->getId()) {
                $viewBookLink = \Sb\Helpers\HTTPHelper::Link($bk->getLink());
                $bookInDB = true;
            }

            $resultTpl = new \Sb\Templates\Template('searchBook/resultRow');
            $resultTpl->set('cssClass', $cssClass);
            $resultTpl->set('title', $title);
            $resultTpl->set('publisher', $pubInfo);
            $resultTpl->set('author', $author);
            $resultTpl->set('id', $id);
            $resultTpl->set('isbn10', $isbn10);
            $resultTpl->set('isbn13', $isbn13);
            $resultTpl->set('asin', $asin);
            $resultTpl->set('titleEsc', $titleEsc);
            $resultTpl->set('descEsc', $descEsc);
            $resultTpl->set('desc', $desc);
            $resultTpl->set('smallImg', $smallImg);
            $resultTpl->set('img', $img);
            $resultTpl->set('largeImg', $largeImg);
            $resultTpl->set('imgSrc', $imgSrc);
            $resultTpl->set('authorEsc', $authorEsc);
            $resultTpl->set('pubEsc', $pubEsc);
            $resultTpl->set('pubDtStr', $pubDtStr);
            $resultTpl->set('amazonUrl', $amazonUrl);
            $resultTpl->set('nbOfPages', $nbOfPages);
            $resultTpl->set('language', $language);

            $resultTpl->setVariables(array('addSep' => $addSep,
                'viewBookLink' => $viewBookLink,
                'bookInDB' => $bookInDB));
            $resultTplArr[] = $resultTpl;
        }

        $results = \Sb\Templates\Template::merge($resultTplArr);

        $resultsTpl = new \Sb\Templates\Template('searchBook/results');
        $resultsTpl->set("resultRows", $results);
        $links = $this->pagerLinks;
        $resultsTpl->set("links", $links['all']);
        $resultsTpl->set("first", $this->firstItemIdx);
        $resultsTpl->set("last", $this->lastItemIdx);
        $resultsTpl->set("nbItemsTot", $this->nbItemsTot);

        return $resultsTpl->output();
    }
}
