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
        $img = BookHelper::getSmallImageTag($this->book, $this->defImg);
        $bookTitle = $this->book->getTitle();
        $bookAuthors = "";
        if ($this->book->getContributors())
            $bookAuthors = sprintf("Auteur(s) : %s", $this->book->getOrderableContributors());
        $isOffered = ($this->userbook->getActiveGiftRelated() != null);
        $offerer = null;
        $deactivateGiftOptionLink = "";
        if ($isOffered) {
            $deactivateGiftOptionLink = HTTPHelper::Link(Urls::USERBOOK_GIFT_DISABLE, array("ubgid" => $this->userbook->getActiveGiftRelated()->getId()));
            $offerer = $this->userbook->getActiveGiftRelated()->getOfferer();
        }
        $bookDescription = $this->book->getDescription();
        $buyOnAmazonLink = $this->book->getAmazonUrl();

        $buyOnFnacLink = null;
        if ($this->book->getISBN13()) {
            $buyOnFnacLink = 'http://track.effiliation.com/servlet/effi.redir?id_compteur=13362685&url=http%3A%2F%2Frecherche.fnac.com%2FSearchResult%2FResultList.aspx%3FSCat%3D2%211%26Search%3D'
                . $this->book->getISBN13() . '%26Origin%3Deffinity1395061830';
        }

        $setAsOfferedLink = HTTPHelper::Link(Urls::WISHED_USERBOOK_SET_AS_OFFERED, array("ubid" => $this->userbook->getId()));
        $subscribeLink = HTTPHelper::Link(Urls::SUBSCRIBE);

        // Set variables
        $tplBook->setVariables(array(
            "bookTitle" => $bookTitle,
            "bookAuthors" => $bookAuthors,
            "viewBookLink" => $viewBookLink,
            "image" => $img,
            "isOffered" => $isOffered,
            "buyOnAmazonLink" => $buyOnAmazonLink,
            "buyOnFnacLink" => $buyOnFnacLink,
            "offerer" => $offerer,
            "setAsOfferedLink" => $setAsOfferedLink,
            "subscribeLink" => $subscribeLink,
            "deactivateGiftOptionLink" => $deactivateGiftOptionLink,
            "isConnected" => $this->getContext()->getConnectedUser()));

        return $tplBook->output();
    }

}