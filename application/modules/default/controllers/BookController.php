<?php
use Sb\View\Book as BookView;
use Sb\View\Components\ButtonsBar;
use Sb\View\Components\Ad;
use Sb\Service\MailSvc;
use Sb\Entity\Constants;
use Sb\Service\HeaderInformationSvc;
use Sb\Trace\Trace;
use Sb\View\SocialNetworksBar;
use Sb\Adapter\ChronicleListAdapter;
use Sb\View\PushedChronicles;
use Sb\View\BookPressReviews;
use Sb\Service\BookPageSvc;
use Sb\Model\BookPage;

class Default_BookController extends Zend_Controller_Action {

    public function init() {

        /* Initialize actions called in AJAX mode */
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('get-reviews-page', 'html')->addActionContext('warn-offensive-comment', 'json')->addActionContext('warn-bad-description', 'json')->initContext();
    }

    /**
     * Action showing a book description
     */
    public function indexAction() {
        try {

            $globalContext = new \Sb\Context\Model\Context();;

            $bookId = $this->_getParam('bid');

            if (!$bookId) {
                $this->getResponse()->setHttpResponseCode(404);
                $this->forward("error", "error", "default");
                return;
            }

            // Get book page
            /* @var $bookPage BookPage */
            $bookPage = BookPageSvc::getInstance()->get($bookId);

            if (!$bookPage) {
                $this->getResponse()->setHttpResponseCode(404);
                $this->forward("error", "error", "default");
                return;
            }

            // Add and js files
            $this->view->placeholder('footer')->append("<script src=\"" . $globalContext->getBaseUrl() . 'Resources/js/waterwheel-carousel/jquery.waterwheelCarousel.min.js' . "\"></script>\n");
            $this->view->placeholder('footer')->append("<script>$(function () {initCoverFlip('sameAuthorBooks', 30)});</script>\n");

            // Get book view and add it to view model
            $bookView = new BookView($bookPage->getBook(), true, true, true, $bookPage->getBooksAlsoLiked(), $bookPage->getBooksWithSameTags(), $bookPage->getReviewedUserBooks(), false, true);
            $this->view->bookView = $bookView;

            // Get book buttonbar and add it to view model
            $buttonsBar = new ButtonsBar(false);
            $this->view->buttonsBar = $buttonsBar;

            // Get Books with same contributors and add it to view model
            $this->view->sameAuthorBooks = $bookPage->getBooksWithSameAuthor();

            // We pass ASIN code to be used by amazon url builder widget
            $this->view->bookAsin = $bookPage->getBook()->getASIN();

            // Get fnac buy link and add it to view model
            $this->view->buyOnFnacLink = null;
            if ($bookPage->getBook()->getISBN13()) {
                $this->view->buyOnFnacLink = 'http://clic.reussissonsensemble.fr/click.asp?ref=751772&site=14485&type=text&tnb=3&diurl=http%3A%2F%2Feultech.fnac.com%2Fdynclick%2Ffnac%2F%3Feseg-name%3DaffilieID%26eseg-item%3D%24ref%24%26eaf-publisher%3DAFFILINET%26eaf-name%3Dg%3Fn%3Frique%26eaf-creative%3D%24affmt%24%26eaf-creativetype%3D%24affmn%24%26eurl%3Dhttp%253A%252F%252Frecherche.fnac.com%252FSearchResult%252FResultList.aspx%253FSCat%253D0%2525211%2526Search%253D'
                    . $bookPage->getBook()->getISBN13() . '%2526Origin%253Daffilinet%2524ref%2524';
            }

            // Get social network bar and add it to view model
            $socialBar = new SocialNetworksBar($bookPage->getBook()->getLargeImageUrl(), $bookPage->getBook()->getTitle());
            $this->view->socialBar = $socialBar->get();

            // Get ad and add it to view model
            $ad = new Ad("bibliotheque", "1223994660");
            $this->view->ad = $ad;

            // Get Header Information and add it to view model
            $headerInformation = HeaderInformationSvc::getInstance()->get($bookPage->getBook());
            $this->view->tagTitle = $headerInformation->getTitle();
            $this->view->metaDescription = $headerInformation->getDescription();
            $this->view->metaKeywords = $headerInformation->getKeywords();
            $this->view->urlCanonical = $headerInformation->getUrlCanonical();
            $this->view->pageImage = $headerInformation->getPageImage();

            // Get last read userbooks for the book and add it to view model
            $this->view->lastlyReadUserbooks = $bookPage->getLastlyReadUserbooks();

            if (count($this->view->lastlyReadUserbooks) > 1) {
                $this->view->placeholder('footer')->append("<script src=\"" . $globalContext->getBaseUrl() . 'Resources/js/simple-carousel/simple.carousel.js' . "\"></script>\n");
                $this->view->placeholder('footer')->append("<script>$(function() {initCarousel('carousel-lastUsersWhoReadThatBook', 298, 85)});</script>\n");
            }

            // Get chronicles and add it to view model
            $this->view->chronicles = $this->getChronicleView($bookPage->getRelatedChronicles());

            // Get video press review associated to book
            $this->view->placeholder('footer')->append("<script src=\"" . $globalContext->getBaseUrl() . 'Resources/js/waterwheel-carousel/jquery.waterwheelCarousel.min.js' . "\"></script>\n");
            $this->view->placeholder('footer')->append("<script>$(function () {initCoverFlip('sameAuthorBooks', 30)});</script>\n");

            $video = $bookPage->getVideoPressReview();
            if ($video)
                $this->view->videoUrl = $video->getLink(); //

            // Get book press reviews
            $bookPressReviews = $bookPage->getPressReviews();
            if ($bookPressReviews) {
                $bookPressReviewsView = new BookPressReviews($bookPressReviews);
                $this->view->pressReviews = $bookPressReviewsView->get();
            }


        } catch (\Exception $exc) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $exc->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    /**
     * Action called exclusively throught AJAX, show a book review page
     */
    public function getReviewsPageAction() {
        $bid = $this->_getParam('key');
        $pageId = $this->_getParam('param');

        $book = Sb\Db\Dao\BookDao::getInstance()->get($bid);
        $userBooks = $book->getNotDeletedUserBooks();
        $reviewedUserBooks = array_filter($userBooks, array(
            &$this, "isReviewed"
        ));

        $nbOfReviewsPerPage = 5;
        if ($reviewedUserBooks && count($reviewedUserBooks) > 0) {
            $paginatedList = new \Sb\Lists\PaginatedList($reviewedUserBooks, $nbOfReviewsPerPage, 'pagenumber', $pageId);
            $this->view->paginatedList = $paginatedList;
            $this->view->bookId = $bid;
            $this->view->pageNumber = $pageId;
        }
    }

    public function warnOffensiveCommentAction() {
        $globalContext = new \Sb\Context\Model\Context();

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
        $globalContext = new \Sb\Context\Model\Context();

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
        if ($userBook->getReview())
            return true;
    }

    private function getChronicleView($chronicles) {
        $chronicleListAdapter = new ChronicleListAdapter();
        // Getting list of view model
        $chronicleListAdapter->setChronicles($chronicles);
        $chroniclesAsViewModel = $chronicleListAdapter->getAsChronicleViewModelLightList();
        // Get chronicles view
        $link = $this->view->url(array(), 'chroniclesLastAnyType');
        $chroniclesView = new PushedChronicles($chroniclesAsViewModel, $link);
        return $chroniclesView->get();
    }

}
