<?php

namespace Sb\View;

use Sb\Helpers\HTTPHelper;
use Sb\Helpers\StringHelper;
use Sb\Templates\Template;

/**
 * Description of Sp_View_BookSearch
 *
 * @author Didier
 */
class BookSearch extends AbstractView
{

    private $shownResults;
    private $pagerLinks;
    private $firstItemIdx;
    private $lastItemIdx;
    private $nbItemsTot;

    function __construct($shownResults, $pagerLinks, $firstItemIdx, $lastItemIdx, $nbItemsTot)
    {
        parent::__construct();
        $this->shownResults = $shownResults;
        $this->pagerLinks = $pagerLinks;
        $this->firstItemIdx = $firstItemIdx;
        $this->lastItemIdx = $lastItemIdx;
        $this->nbItemsTot = $nbItemsTot;
    }

    /**
     * @return mixed|string
     */
    public function get()
    {
        $lineIdx = 0;
        foreach (array_values($this->shownResults) as $book) {
            $lineIdx++;

            $addSep = true;
            if ($lineIdx == 1)
                $addSep = false;

            $bk = $book;

            $language = urlencode($bk->getLanguage());

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

            $desc = StringHelper::tronque($bk->getDescription(), 350);
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
                $viewBookLink = HTTPHelper::Link($bk->getLink());
                $bookInDB = true;
            }

            $resultTpl = new Template('searchBook/resultRow');
            $resultTpl->setVariables(array('addSep' => $addSep,
                'viewBookLink' => $viewBookLink,
                'bookInDB' => $bookInDB,
                'cssClass' => $cssClass . ' ' . ($bookInDB ? 'indb' : ''),
                'title' => $title,
                'publisher' => $pubInfo,
                'author' => $author,
                'id' => $id,
                'isbn10' => $isbn10,
                'isbn13' => $isbn13,
                'asin' => $asin,
                'titleEsc' => $titleEsc,
                'descEsc' => $descEsc,
                'desc' => $desc,
                'smallImg' => $smallImg,
                'img' => $img,
                'largeImg' => $largeImg,
                'imgSrc' => $imgSrc,
                'authorEsc' => $authorEsc,
                'pubEsc' => $pubEsc,
                'pubDtStr' => $pubDtStr,
                'amazonUrl' => $amazonUrl,
                'language' => $language,
                'nbOfPages' => $nbOfPages));
            $resultTplArr[] = $resultTpl;
        }

        $results = Template::merge($resultTplArr);

        $resultsTpl = new Template('searchBook/results');
        $resultsTpl->set("resultRows", $results);
        $links = $this->pagerLinks;
        $resultsTpl->set("links", $links['all']);
        $resultsTpl->set("first", $this->firstItemIdx);
        $resultsTpl->set("last", $this->lastItemIdx);
        $resultsTpl->set("nbItemsTot", $this->nbItemsTot);

        return $resultsTpl->output();
    }
}
