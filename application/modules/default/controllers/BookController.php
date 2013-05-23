<?php
use Sb\Db\Dao\BookDao;
use Sb\Db\Service\BookSvc;
use Sb\View\Book as BookView;
use Sb\Db\Model\Book;
use Sb\Db\Model\Tag;
use Sb\Db\Model\PressReview;
use Sb\View\Components\ButtonsBar;
use Sb\View\Components\Ad;
use Sb\Service\MailSvc;
use Sb\Entity\Constants;
use Sb\Service\HeaderInformationSvc;
use Sb\Trace\Trace;
use Sb\View\SocialNetworksBar;
use Sb\Db\Service\TagSvc;
use Sb\Db\Service\ChronicleSvc;
use Sb\Db\Service\PressReviewSvc;
use Sb\Adapter\ChronicleListAdapter;
use Sb\View\PushedChronicles;
use Sb\Entity\PressReviewTypes;
use Sb\View\BookPressReviews;

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

        try {
            
            // $referer = $this->getRequest()->getHeader('referer');
            global $globalContext;
            
            $noBook = false;
            $bookId = $this->_getParam('bid');
            
            // Get books with same contributors
            if ($bookId) {
                
                // Get book
                /* @var $book Book */
                $book = BookDao::getInstance()->get($bookId);
                
                if ($book) {
                    
                    // Add chronicle css to head
                    $this->view->headLink()
                        ->appendStylesheet(BASE_URL . "resources/css/chronicle.css?v=" . VERSION);
                    
                    // Add press review css to head
                    $this->view->headLink()
                        ->appendStylesheet(BASE_URL . "resources/css/pressReviews.css?v=" . VERSION);
                    
                    // Get book view and add it to view model
                    $bookView = new BookView($book, true, true, true, false, true);
                    $this->view->bookView = $bookView;
                    
                    // Get book buttonbar and add it to view model
                    $buttonsBar = new ButtonsBar(false);
                    $this->view->buttonsBar = $buttonsBar;
                    
                    // Get Books with same contributors and add it to view model
                    $this->view->sameAuthorBooks = BookSvc::getInstance()->getBooksWithSameContributors($bookId);
                    
                    $this->view->placeholder('footer')
                        ->append("<script src=\"" . $globalContext->getBaseUrl() . 'Resources/js/waterwheel-carousel/jquery.waterwheelCarousel.min.js' . "\"></script>\n");
                    $this->view->placeholder('footer')
                        ->append("<script>$(function () {initCoverFlip('sameAuthorBooks', 30)});</script>\n");
                    
                    // We pass ASIN code to be used by amazon url builder widget
                    $this->view->bookAsin = $book->getASIN();
                    
                    // Get fnac buy link and add it to view model
                    $this->view->buyOnFnacLink = null;
                    if ($book->getISBN13())
                        $this->view->buyOnFnacLink = "http://ad.zanox.com/ppc/?23404800C471235779T&ULP=[[http://recherche.fnac.com/search/quick.do?text=" . $book->getISBN13() . "]]"; //
                                                                                                                                                                                           
                    // Get social network bar and add it to view model
                    $socialBar = new SocialNetworksBar($book->getLargeImageUrl(), $book->getTitle());
                    $this->view->socialBar = $socialBar->get();
                    
                    // Get ad and add it to view model
                    $ad = new Ad("bibliotheque", "1223994660");
                    $this->view->ad = $ad;
                    
                    // Get Header Information and add it to view model
                    $headerInformation = HeaderInformationSvc::getInstance()->get($book);
                    $this->view->tagTitle = $headerInformation->getTitle();
                    $this->view->metaDescription = $headerInformation->getDescription();
                    $this->view->metaKeywords = $headerInformation->getKeywords();
                    
                    // Get last read userbooks for the book and add it to view model
                    $this->view->lastlyReadUserbooks = Sb\Db\Service\UserBookSvc::getInstance()->getLastlyReadUserbookByBookId($bookId, 5);
                    
                    if (count($this->view->lastlyReadUserbooks) > 1) {
                        $this->view->placeholder('footer')
                            ->append("<script src=\"" . $globalContext->getBaseUrl() . 'Resources/js/simple-carousel/simple.carousel.js' . "\"></script>\n");
                        $this->view->placeholder('footer')
                            ->append("<script>$(function() {initCarousel('carousel-lastUsersWhoReadThatBook', 298, 85)});</script>\n");
                    }
                    
                    // Get chronicles and add it to view model
                    $chronicles = $this->getChroniclesRelativeToBook($book);
                    $this->view->chronicles = $this->getChronicleView($chronicles);
                    
                    // Get video press review associated to book
                    $videoPressReviews = PressReviewSvc::getInstance()->getListByBookId($book->getId(), PressReviewTypes::VIDEO, 1);
                    if ($videoPressReviews) {
                        $video = $videoPressReviews[0];
                        $this->view->videoUrl = $video->getLink();
                    }
                    
                    // Get book press reviews
                    $bookPressReviews = $this->getBookPressReviews($book);
                    if ($bookPressReviews) {
                        $bookPressReviewsView = new BookPressReviews($bookPressReviews);
                        $this->view->pressReviews = $bookPressReviewsView->get();
                    }
                } else
                    $noBook = true;
            } else
                $noBook = true;
            
            if ($noBook)
                $this->forward("error", "error", "default");
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
                &$this,
                "isReviewed"
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

        global $globalContext;
        
        $bookId = $this->_getParam('bid');
        if ($globalContext->getConnectedUser()) {
            if ($bookId) {
                
                $userId = $globalContext->getConnectedUser()
                    ->getId();
                
                $mailSvc = MailSvc::getNewInstance(null, $globalContext->getConnectedUser()
                    ->getEmail());
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
                
                $userId = $globalContext->getConnectedUser()
                    ->getId();
                
                $mailSvc = MailSvc::getNewInstance(null, $globalContext->getConnectedUser()
                    ->getEmail());
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

    private function getChroniclesRelativeToBook(Book $book) {

        $chronicles = null;
        
        // Get book userbook's tag
        $bookTags = TagSvc::getInstance()->getTagsForBooks(array(
                $book
        ));
        $bookTagIds = null;
        foreach ($bookTags as $bookTag) {
            /* @var $bookTag Tag */
            $bookTagIds[] = $bookTag->getId();
        }
        
        // Get 3 chronicles with same tags
        if ($bookTags && count($bookTags) > 0)
            $chronicles = ChronicleSvc::getInstance()->getChroniclesWithTags($bookTagIds, 3); //
                                                                                                  
        // Get last chronicles of any types and add them to previously set list of chronicles
        if (!$chronicles || count($chronicles) < 3) {
            $lastChronicles = ChronicleSvc::getInstance()->getLastChronicles(3);
            foreach ($lastChronicles as $lastChronicle) {
                
                $add = true;
                if ($chronicles) {
                    foreach ($chronicles as $chronicle) {
                        if ($chronicle->getId() == $lastChronicle->getId()) {
                            $add = false;
                            break;
                        }
                    }
                }
                
                if ($add)
                    $chronicles[] = $lastChronicle;
            }
        }
        
        if ($chronicles)
            $chronicles = array_slice($chronicles, 0, 3);
        
        return $chronicles;
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

    private function getBookPressReviews(Book $book) {

        $bookPressReviews = PressReviewSvc::getInstance()->getListByBookId($book->getId(), PressReviewTypes::ARTICLE, 3);
        
        // If not enough press reviews associated to book, getting general press reviews
        if (!$bookPressReviews || count($bookPressReviews) < 3) {
            
            // Get general press reviews
            $generalPressReviews = PressReviewSvc::getInstance()->getList(3, PressReviewTypes::ARTICLE);
            
            if (!$bookPressReviews) {
                
                $bookPressReviews = $generalPressReviews;
            } else {
                if ($generalPressReviews) {
                    foreach ($generalPressReviews as $generalPressReview) {
                        /* @var $generalPressReview PressReview */
                        $add = true;
                        foreach ($bookPressReviews as $bookPressReview) {
                            /* @var $bookPressReview PressReview */
                            if ($generalPressReview->getId() == $bookPressReview->getId()) {
                                $add = false;
                                break;
                            }
                        }
                        
                        if ($add)
                            $bookPressReviews[] = $generalPressReview;
                    }
                }
            }
        }
        
        if ($bookPressReviews)
            $bookPressReviews = array_slice($bookPressReviews, 0, 3);
        
        return $bookPressReviews;
    }

}

