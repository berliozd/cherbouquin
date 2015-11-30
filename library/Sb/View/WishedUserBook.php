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
            $buyOnFnacLink = 'http://clic.reussissonsensemble.fr/click.asp?ref=751772&site=14485&type=text&tnb=3&diurl=http%3A%2F%2Feultech.fnac.com%2Fdynclick%2Ffnac%2F%3Feseg-name%3DaffilieID%26eseg-item%3D%24ref%24%26eaf-publisher%3DAFFILINET%26eaf-name%3Dg%3Fn%3Frique%26eaf-creative%3D%24affmt%24%26eaf-creativetype%3D%24affmn%24%26eurl%3Dhttp%253A%252F%252Frecherche.fnac.com%252FSearchResult%252FResultList.aspx%253FSCat%253D0%2525211%2526Search%253D'
                . $this->book->getISBN13() . '%2526Origin%253Daffilinet%2524ref%2524';
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