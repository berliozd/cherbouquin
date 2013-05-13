<?php
use Sb\Db\Dao\ChronicleDao;
use Sb\Db\Model\Chronicle;
use Sb\View\ChronicleDetail;
use Sb\Db\Service\ChronicleSvc;
use Sb\View\OtherChroniclesSameType;
use Sb\Trace\Trace;
use Sb\View\OtherChroniclesSameAuthor;
use Sb\View\Components\Ad;
use Sb\Helpers\HTTPHelper;
use Sb\Lists\PaginatedList;
use Sb\View\BookReviews;
use Sb\Db\Model\UserBook;
use Sb\View\Components\PressReviewsSubscriptionWidget;
use Sb\Adapter\ChronicleListAdapter;
use Sb\Adapter\ChronicleAdapter;
use Sb\Entity\GroupTypes;

/**
 * ChronicleController
 * @author
 * @version
 */
class Default_ChronicleController extends Zend_Controller_Action {

    public function init() {
        
        // Add chronicle css to head
        $this->view->headLink()
            ->appendStylesheet(BASE_URL . "resources/css/chronicle.css?v=" . VERSION);
    }

    /**
     * The default action - show a chronicle detail page
     */
    public function indexAction() {

        try {
            
            // Get chronicle id from request
            $chronicleId = $this->getParam("cid");
            
            // Get main chronicle
            /* @var $chronicle Chronicle */
            $chronicle = ChronicleDao::getInstance()->get($chronicleId);
            
            // Increment chronicle nb views
            $this->incrementChronicleNbViews($chronicle);
            
            $chronicleAdapter = new ChronicleAdapter($chronicle);
            // Get the ChronicleViewModel with maximum 3 similar chronicles, and maximum 5 same author chronicles
            $chronicleViewModel = $chronicleAdapter->getAsChronicleViewModel(3, 5);
            
            // Add main chronicle view model to model view
            $chronicleView = new ChronicleDetail($chronicleViewModel);
            $this->view->chronicle = $chronicleView->get();
            
            // Get similar chronicles (with same tag or with similar keywords) and add it to model view
            $similarChronicles = $chronicleViewModel->getSimilarChronicles();
            if ($similarChronicles && count($similarChronicles) > 0) {
                $otherChoniclesSameTypeView = new OtherChroniclesSameType($similarChronicles);
                $this->view->otherChoniclesSameType = $otherChoniclesSameTypeView->get();
            }
            
            // Get same author chronicles and add it to model view
            $authorChronicles = $chronicleViewModel->getSameAuthorChronicles();
            if ($authorChronicles) {
                $authorChroniclesView = new OtherChroniclesSameAuthor($authorChronicles);
                // Add author chronicles to model
                $this->view->authorChroniclesView = $authorChroniclesView->get();
            }
            
            // Add common items to model view
            $this->addCommonItemsToModelView();
            
            // Get reviews and add it to model view
            $reviewsView = $this->getReviews($chronicle);
            if ($reviewsView)
                $this->view->reviews = $reviewsView->get(); //
                                                                
            // Set SEO information
            $this->view->tagTitle = $chronicleViewModel->getTitle();
            $this->view->metaDescription = $chronicleViewModel->getShortenText();
            $this->view->metaKeywords = $chronicle->getKeywords();
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    /**
     * Action for chronicles list pages
     */
    public function listAction() {

        try {
            
            // Get key that define what type of chronicles to display
            $key = $this->getParam("key", "last-anytype");
            
            $navigationParamName = "pagenumber";
            
            $pageNumber = $this->getParam($navigationParamName, 1);
            
            // Get 100 last chronicles
            switch ($key) {
                case "last-anytype" :
                    $chronicles = ChronicleSvc::getInstance()->getLastChronicles(100, null, GroupTypes::BLOGGER . "," . GroupTypes::BOOK_STORE);
                    $title = __("Dernières chroniques", "s1b");
                    break;
                case "last-bloggers" :
                    $chronicles = ChronicleSvc::getInstance()->getLastChronicles(100, GroupTypes::BLOGGER);
                    $title = __("Chroniques des bloggeurs", "s1b");
                    break;
                case "last-bookstores" :
                    $chronicles = ChronicleSvc::getInstance()->getLastChronicles(100, GroupTypes::BOOK_STORE);
                    $title = __("Chroniques des libraires", "s1b");
                    break;
            }
            
            // Add title list to model view
            $this->view->title = $title;
            
            $chroniclesPaginated = new PaginatedList($chronicles, 5, $navigationParamName, $pageNumber);
            $chroniclesPage = $chroniclesPaginated->getItems();
            
            $chroniclesAdapter = new ChronicleListAdapter();
            $chroniclesAdapter->setChronicles($chroniclesPage);
            $chronicleDetailViewModelList = $chroniclesAdapter->getAsChronicleViewModelList(2);
            
            // Add chronicleDetailViewModel list to model view
            $this->view->chronicleDetailViewModelList = $chronicleDetailViewModelList;
            
            // Add navigation bar to view model
            $this->view->navigationBar = $chroniclesPaginated->getNavigationBar();
            
            // Add common items to model view
            $this->addCommonItemsToModelView();
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    /**
     * Test if chronicle nb of views needs to be incremented based on the presence of the chronicle id in a cookie called chroniclesSeen
     * @param Chronicle $chronicle
     */
    private function incrementChronicleNbViews(Chronicle $chronicle) {

        $cookieName = "chroniclesSeen";
        $chronicleNotSeen = false;
        
        // Get cookie 'chroniclesSeen'
        $chroniclesSeenCookie = $this->getRequest()
            ->getCookie($cookieName);
        
        // Parse cookie and tell if current chronicle has been seen already
        if ($chroniclesSeenCookie) {
            
            $chroniclesSeen = explode(",", $chroniclesSeenCookie);
            if (!in_array($chronicle->getId(), $chroniclesSeen)) {
                
                $chroniclesSeen[] = $chronicle->getId();
                $cookieValue = implode(",", $chroniclesSeen);
                
                // Set cookie
                $this->setChronicleSeenCookie($cookieName, $cookieValue);
                
                // Increment chronicle nb views
                $this->incrementChronicleInDB($chronicle);
            }
        } else {
            
            // Set cookie
            $this->setChronicleSeenCookie($cookieName, $chronicle->getId());
            
            // Increment chronicle nb views
            $this->incrementChronicleInDB($chronicle);
        }
    }

    /**
     * Set a cookie for name and value with a 24 hours life time
     * @param String $cookieName
     * @param String $cookieValue
     */
    private function setChronicleSeenCookie($cookieName, $cookieValue) {

        $this->getResponse()
            ->setRawHeader(new Zend_Http_Header_SetCookie($cookieName, $cookieValue, time() + 3600 * 24, '/', HTTPHelper::getHostBase(), false, true));
    }

    /**
     * Increment nb of views in database for chronicle
     * @param Chronicle $chronicle
     */
    private function incrementChronicleInDB(Chronicle $chronicle) {

        $chronicle->setNb_views($chronicle->getNb_views() + 1);
        ChronicleDao::getInstance()->update($chronicle);
    }

    /**
     * Get a reviews view object representing a paginated list of reviews for the current book
     * @param Chronicle $chronicle the current chronicle to get the book and the reviews from
     * @return \Sb\View\BookReviews NULL BookReviews object or NULL
     */
    private function getReviews(Chronicle $chronicle) {

        if ($chronicle->getBook()) {
            
            // book reviews
            $userBooks = $chronicle->getBook()
                ->getNotDeletedUserBooks();
            $reviewedUserBooks = array_filter($userBooks, array(
                    &$this,
                    "isReviewd"
            ));
            
            $reviews = "";
            if ($reviewedUserBooks) {
                
                $paginatedList = new PaginatedList($reviewedUserBooks, 5);
                $reviewsView = new BookReviews($paginatedList, $chronicle->getBook()
                    ->getId());
                
                return $reviewsView;
            }
        }
        
        return null;
    }

    private function isReviewd(UserBook $userBook) {

        if ($userBook->getReview()) {
            return true;
        }
    }

    /**
     * Add common items to all actions in model view
     */
    private function addCommonItemsToModelView() {
        
        // Get ad and add it to model view
        $ad = new Ad("", "");
        $this->view->ad = $ad->get();
        
        // Get press reviews subscription widget and add it to view model
        $pressReviewsSubscriptionWidget = new PressReviewsSubscriptionWidget();
        $this->view->pressReviewsSubscriptionWidget = $pressReviewsSubscriptionWidget->get();
    }

}
