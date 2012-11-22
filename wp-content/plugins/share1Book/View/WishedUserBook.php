<?php

namespace Sb\View;

use \Sb\Helpers\HTTPHelper;
use \Sb\Db\Model\UserBook;
use \Sb\Templates\Template;
use \Sb\Helpers\BookHelper;
use \Sb\Entity\Urls;

/**
 *
 * @author Didier
 */
class WishedUserBook extends AbstractView {

    private $book;
    private $userbook;

    function __construct(UserBook $userbook) {
        parent::__construct();
        $this->book = $userbook->getBook();
        $this->userbook = $userbook;
    }

    public function get() {

        $tplBook = new Template("wishedUserBook");

        // Prepare variables        
        $viewBookLink = HTTPHelper::Link($this->book->getLink());
        $img = BookHelper::getMediumImageTag($this->book, $this->defImg);
        $bookTitle = $this->book->getTitle();
        $bookPublication = $this->book->getPublicationInfo();
        $bookAuthors = "";
        if ($this->book->getContributors())
            $bookAuthors = sprintf("Auteur(s) : %s", $this->book->getOrderableContributors());
        $isOffered = ($this->userbook->getActiveGiftRelated() != null);
        if ($isOffered)
            $giftOptionFromMe = ($this->userbook->getActiveGiftRelated()->getOfferer()->getId() == $this->getContext()->getConnectedUser()->getId());
        $buyOnAmazonLink = $this->book->getAmazonUrl();
        $buyOnFnacLink = null;
        if ($this->book->getISBN13())
            $buyOnFnacLink = "http://ad.zanox.com/ppc/?23404800C471235779T&ULP=[[http://recherche.fnac.com/search/quick.do?text=" . $this->book->getISBN13() . "]]";
        $setAsOfferedLink = HTTPHelper::Link(Urls::WISHED_USERBOOK_SET_AS_OFFERED, array("ubid" => $this->userbook->getId()));
        if ($giftOptionFromMe)
            $deactivateGiftOptionLink = HTTPHelper::Link(Urls::USERBOOK_GIFT_DISABLE, array("ubgid" => $this->userbook->getActiveGiftRelated()->getId()));;

        // Set variables
        $tplBook->setVariables(array(
            "bookTitle" => $bookTitle,
            "bookPublication" => $bookPublication,
            "bookAuthors" => $bookAuthors,
            "viewBookLink" => $viewBookLink,
            "image" => $img,
            "isOffered" => $isOffered,
            "buyOnAmazonLink" => $buyOnAmazonLink,
            "buyOnFnacLink" => $buyOnFnacLink,
            "giftOptionFromMe" =>$giftOptionFromMe, 
            "setAsOfferedLink" => $setAsOfferedLink,
            "deactivateGiftOptionLink" => $deactivateGiftOptionLink));

        return $tplBook->output();
    }

}