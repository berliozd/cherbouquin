<?php

use Sb\Db\Dao\BookDao;
use Sb\Db\Service\BookSvc;
use Sb\View\Book;
use Sb\View\Components\ButtonsBar;
use Sb\View\Components\Ad;
use Sb\Helpers\HTTPHelper;
use Sb\Entity\Urls;
use Sb\Service\MailSvc;
use Sb\Entity\Constants;

class Default_BookController extends Zend_Controller_Action {

    public function init() {

        /* Initialize actions called in AJAX mode */
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('get-reviews-page', 'html')
                ->addActionContext('warn-offensive-comment', 'json')
                ->addActionContext('warn-bad-description', 'json')
                ->initContext();
    }

    /**
     * Action showing a book description
     */
    public function indexAction() {

        global $globalContext;

        $noBook = false;
        $bookId = $this->_getParam('bid');

        // Get books with same contributors
        if ($bookId) {
            // Get book
            $book = BookDao::getInstance()->get($bookId);

            if ($book) {

                // Get book view
                $bookView = new Book($book, true, true, true, false, true);
                $this->view->bookView = $bookView;

                // Get book buttonbar
                $buttonsBar = new ButtonsBar(false);
                $this->view->buttonsBar = $buttonsBar;

                // Books with same contributors
                $this->view->sameAuthorBooks = BookSvc::getInstance()->getBooksWithSameContributors($bookId);

                $this->view->placeholder('footer')->append("<script src=\"" . $globalContext->getBaseUrl() . 'Resources/js/waterwheel-carousel/jquery.waterwheelCarousel.min.js' . "\"></script>\n");
                $this->view->placeholder('footer')->append("<script>$(function () {initCoverFlip('sameAuthorBooks', 30)});</script>\n");

                // Get amazon and fnac buy link
                $this->view->buyOnAmazonLink = $book->getAmazonUrl();
                $this->view->buyOnFnacLink = null;
                if ($book->getISBN13())
                    $this->view->buyOnFnacLink = "http://ad.zanox.com/ppc/?23404800C471235779T&ULP=[[http://recherche.fnac.com/search/quick.do?text=" . $book->getISBN13() . "]]";

                // Get share links
                $this->view->shareLink = HTTPHelper::Link(Urls::USER_MAILBOX_RECOMMAND, array("id" => $book->getId()));
                $this->view->facebookShareLink = HTTPHelper::Link(Urls::RECOMMAND_ON_FACEBOOK, array("id" => $book->getId()));

                // Get ad
                $ad = new Ad("bibliotheque", "1223994660");
                $this->view->ad = $ad;

                $this->view->tagTitle = $book->getTitle() . " - " . $book->getOrderableContributors();
                $this->view->metaDescription = htmlspecialchars($book->getDescription());

                // Get last read userbooks for the book
                $this->view->lastlyReadUserbooks = Sb\Db\Service\UserBookSvc::getInstance()->getLastlyReadUserbookByBookId($bookId, 5);
                if (count($this->view->lastlyReadUserbooks) > 1) {
                    $this->view->placeholder('footer')->append("<script src=\"" . $globalContext->getBaseUrl() . 'Resources/js/simple-carousel/simple.carousel.js' . "\"></script>\n");
                    $this->view->placeholder('footer')->append("<script>$(function() {initCarousel('carousel-lastUsersWhoReadThatBook', 298, 85)});</script>\n");
                }
            } else
                $noBook = true;
        }else
            $noBook = true;

        if ($noBook)
            $this->_forward("error", "error", "default");
    }

    /**
     * Action called exclusively throught AJAX, show a book review page
     */
    public function getReviewsPageAction() {
        $bid = $this->_getParam('key');
        $pageId = $this->_getParam('param');

        $book = Sb\Db\Dao\BookDao::getInstance()->get($bid);
        $userBooks = $book->getNotDeletedUserBooks();
        $reviewedUserBooks = array_filter($userBooks, array(&$this, "isReviewed"));

        if ($reviewedUserBooks && count($reviewedUserBooks) > 0) {
            $paginatedList = new \Sb\Lists\PaginatedList($reviewedUserBooks, 5, 'pagenumber', $pageId);
            $this->view->paginatedList = $paginatedList;
            $this->view->bookId = $bid;
            $this->view->pageNumber = $pageId;
        }
    }

    public function warnOffensiveCommentAction() {

        global $globalContext;

        $bookId = $this->_getParam('bid');
        if ($globalContext->getConnectedUser()) {
            if ($bookId) {

                $userId = $globalContext->getConnectedUser()->getId();

                $mailSvc = MailSvc::getNewInstance(null, $globalContext->getConnectedUser()->getEmail());
                $body = "Un commentaire injurieux a été signalé pour le livre $bookId par l'utilisateur $userId";

                if ($mailSvc->send(Constants::WEBMASTER_EMAIL, "signalisation de commentaire injurieux", $body))
                    $this->view->message = __("Le commentaire injurieux a été signalé à l'administrateur du site.", "s1b");
                else
                    $this->view->message = __("Le mail n'a pa pu être envoyé.", "s1b");
            } else
                $this->view->message = __("Requête incorrecte.", "s1b");
        } else
            $this->view->message = __("Vous devez être connecté pour effectuer cette action.", "s1b");
    }
    
    public function warnBadDescriptionAction() {

        global $globalContext;

        $bookId = $this->_getParam('bid');
        if ($globalContext->getConnectedUser()) {
            if ($bookId) {

                $userId = $globalContext->getConnectedUser()->getId();

                $mailSvc = MailSvc::getNewInstance(null, $globalContext->getConnectedUser()->getEmail());
                $body = "Une description erronée a été signalée pour le livre $bookId par l'utilisateur $userId";

                if ($mailSvc->send(Constants::WEBMASTER_EMAIL, "Signalisation de description erronée", $body))
                    $this->view->message = __("L'administrateur du site a été averti. Nous vous remerçions pour votre aide", "s1b");
                else
                    $this->view->message = __("Le mail n'a pa pu être envoyé.", "s1b");
            } else
                $this->view->message = __("Requête incorrecte.", "s1b");
        } else
            $this->view->message = __("Vous devez être connecté pour effectuer cette action.", "s1b");
    }

    private function isReviewed(\Sb\Db\Model\UserBook $userBook) {
        if ($userBook->getReview()) {
            return true;
        }
    }

}

