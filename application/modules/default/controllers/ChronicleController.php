<?php
use Sb\Db\Dao\ChronicleDao;
use Sb\Db\Model\Chronicle;
use Sb\View\ChronicleDetail;
use Sb\Db\Service\ChronicleSvc;
use Sb\View\OtherChroniclesSameType;
use Sb\Trace\Trace;
use Sb\View\Components\Ad;
use Sb\Helpers\HTTPHelper;
use Sb\Lists\PaginatedList;
use Sb\View\BookReviews;
use Sb\Db\Model\UserBook;
use Sb\View\Components\PressReviewsSubscriptionWidget;
use Sb\Adapter\ChronicleListAdapter;
use Sb\Entity\GroupTypes;
use Sb\Db\Service\PressReviewSvc;
use Sb\View\Components\NewsReader;
use Sb\Entity\PressReviewTypes;
use Sb\View\ChroniclesBlock;
use Sb\View\BookPressReviews;
use Sb\Service\ChroniclePageSvc;
use Sb\Service\HeaderInformationSvc;

class Default_ChronicleController extends Zend_Controller_Action {

    const CHRONICLES_LIST = "CHRONICLES_LIST";

    const PAGE_KEY_ANY_GROUPS = "PAGE_KEY_ANY_GROUPS";

    const PAGE_KEY_BLOGGERS = "PAGE_KEY_BLOGGERS";

    const PAGE_KEY_BOOKSTORES = "PAGE_KEY_BOOKSTORES";

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
            
            $this->view->placeholder('footer')
                ->append("<script type=\"text/javascript\" src=\"" . BASE_URL . 'Resources/js/chronicle.js?v=' . VERSION . "\"></script>");
            
            $this->view->placeholder('footer')
                ->append("<script type=\"text/javascript\" src=\"" . BASE_URL . 'Resources/js/newsReader.js?v=' . VERSION . "\"></script>");
            
            $this->view->headLink()
                ->appendStylesheet(BASE_URL . "resources/css/newsReader.css?v=" . VERSION);
            
            $this->view->headLink()
                ->appendStylesheet(BASE_URL . "resources/css/pressReviews.css?v=" . VERSION);
            
            // Get chronicle id from request
            $chronicleId = $this->getParam("cid");
            
            // Get chronicle page
            $chroniclePage = ChroniclePageSvc::getInstance()->get($chronicleId);
            
            // Increment chronicle nb views
            $this->incrementChronicleNbViews($chroniclePage->getChronicle());
            
            // Add main chronicle view model to model view
            $chronicleView = new ChronicleDetail($chroniclePage->getChronicleViewModel());
            $this->view->chronicle = $chronicleView->get();
            
            // Get similar chronicles (with same tag or with similar keywords) and add it to model view
            $similarChronicles = $chroniclePage->getSimilarChronicles();
            if ($similarChronicles && count($similarChronicles) > 0) {
                $otherChoniclesSameTypeView = new OtherChroniclesSameType($similarChronicles);
                $this->view->otherChoniclesSameType = $otherChoniclesSameTypeView->get();
            }
            
            // Get same author chronicles and add it to model view
            if ($chroniclePage->getSameAuthorChronicles()) {
                $authorChroniclesView = new ChroniclesBlock($chroniclePage->getSameAuthorChronicles(), __("<strong>Chroniques</strong> du même auteur", "s1b"));
                // Add author chronicles to model
                $this->view->authorChroniclesView = $authorChroniclesView->get();
            }
            
            // Get press reviews
            if ($chroniclePage->getPressReviews()) {
                $pressReviewsView = new BookPressReviews($chroniclePage->getPressReviews());
                $this->view->pressReviewsView = $pressReviewsView->get();
            }
            
            // Get reviews and add it to model view
            if ($chroniclePage->getUserBooksReviews()) {
                $paginatedList = new PaginatedList($chroniclePage->getUserBooksReviews(), 5);
                $reviewsView = new BookReviews($paginatedList, $chroniclePage->getChronicle()
                    ->getBook()
                    ->getId());
                $this->view->reviews = $reviewsView->get();
            }
            
            // Get video press review and add it to view model
            if ($chroniclePage->getVideoPressReview())
                $this->view->videoUrl = $chroniclePage->getVideoPressReview()
                    ->getLink(); //
                                     
            // Add common items to model view
            $this->addCommonItemsToModelView();
            
            // Set SEO information
            $this->view->tagTitle = $chroniclePage->getChronicleViewModel()
                ->getTitle();
            $this->view->metaDescription = $chroniclePage->getChronicleViewModel()
                ->getShortenText();
            $this->view->metaKeywords = $chroniclePage->getChronicle()
                ->getKeywords();
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
            
            $this->view->placeholder('footer')
                ->append("<script type=\"text/javascript\" src=\"" . BASE_URL . 'Resources/js/chronicles.js?v=' . VERSION . "\"></script>");
            
            $this->view->placeholder('footer')
                ->append("<script type=\"text/javascript\" src=\"" . BASE_URL . 'Resources/js/newsReader.js?v=' . VERSION . "\"></script>");
            // Add newsreader css to head
            $this->view->headLink()
                ->appendStylesheet(BASE_URL . "resources/css/newsReader.css?v=" . VERSION);
            
            $navigationParamName = "pagenumber";
            $pageNumber = $this->getParam($navigationParamName, null);
            
            // Get key that define what type of chronicles to display
            $key = $this->getParam("key", self::PAGE_KEY_ANY_GROUPS);
            
            // Get 100 last chronicles
            switch ($key) {
                case self::PAGE_KEY_ANY_GROUPS :
                    $chronicles = ChronicleSvc::getInstance()->getLastChronicles(100, null, GroupTypes::BLOGGER . "," . GroupTypes::BOOK_STORE);
                    break;
                case self::PAGE_KEY_BLOGGERS :
                    $chronicles = ChronicleSvc::getInstance()->getLastChronicles(100, GroupTypes::BLOGGER);
                    break;
                case self::PAGE_KEY_BOOKSTORES :
                    $chronicles = ChronicleSvc::getInstance()->getLastChronicles(100, GroupTypes::BOOK_STORE);
                    break;
            }
            
            // Add all chronicle actions common items to model view
            $this->addCommonItemsToModelView();
            
            // Add chronicles list action common items to model view
            $this->addCommonListItemsToModelView($key, $chronicles, $pageNumber, $navigationParamName);
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    /**
     * Action for chronicles list pages
     */
    public function searchAction() {

        try {
            
            $this->view->placeholder('footer')
                ->append("<script type=\"text/javascript\" src=\"" . BASE_URL . 'Resources/js/chronicles.js?v=' . VERSION . "\"></script>");
            
            $this->view->placeholder('footer')
                ->append("<script type=\"text/javascript\" src=\"" . BASE_URL . 'Resources/js/newsReader.js?v=' . VERSION . "\"></script>");
            // Add newsreader css to head
            $this->view->headLink()
                ->appendStylesheet(BASE_URL . "resources/css/newsReader.css?v=" . VERSION);
            
            $navigationParamName = "pagenumber";
            $pageNumber = $this->getParam($navigationParamName, null);
            
            // Get key that define what type of chronicles to display
            $key = $this->getParam("key", self::PAGE_KEY_ANY_GROUPS);
            
            $searchTerm = $this->getParam("chroniclesSearchTerm", null);
            
            // Search chronicles
            switch ($key) {
                case self::PAGE_KEY_ANY_GROUPS :
                    
                    if ($pageNumber) {
                        $chronicles = $this->getResultsInSession($key);
                    } else {
                        $chronicles = ChronicleSvc::getInstance()->getLastChronicles(100, null, GroupTypes::BLOGGER . "," . GroupTypes::BOOK_STORE, false, $searchTerm);
                        $this->setResultsInSession($key, $chronicles);
                    }
                    
                    $initUrl = $this->view->url(array(), 'chroniclesLastAnyType');
                    break;
                case self::PAGE_KEY_BLOGGERS :
                    
                    if ($pageNumber) {
                        $chronicles = $this->getResultsInSession($key);
                    } else {
                        $chronicles = ChronicleSvc::getInstance()->getLastChronicles(100, GroupTypes::BLOGGER, null, false, $searchTerm);
                        $this->setResultsInSession($key, $chronicles);
                    }
                    
                    $initUrl = $this->view->url(array(), 'chroniclesLastBloggers');
                    break;
                case self::PAGE_KEY_BOOKSTORES :
                    
                    if ($pageNumber) {
                        Trace::addItem("From session");
                        $chronicles = $this->getResultsInSession($key);
                    } else {
                        Trace::addItem("From SQL no cache");
                        $chronicles = ChronicleSvc::getInstance()->getLastChronicles(100, GroupTypes::BOOK_STORE, null, false, $searchTerm);
                        $this->setResultsInSession($key, $chronicles);
                    }
                    $initUrl = $this->view->url(array(), 'chroniclesLastBookStores');
                    break;
            }
            
            // Add search term to view model
            $this->view->searchTerm = $searchTerm;
            
            // Add search term to view model
            $this->view->initUrl = $initUrl;
            
            // Add common items to model view
            $this->addCommonItemsToModelView();
            
            // Add chronicles list action common items to model view
            $this->addCommonListItemsToModelView($key, $chronicles, $pageNumber, $navigationParamName);
            
            $this->render("list");
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
                    "isReviewed"
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

    private function isReviewed(UserBook $userBook) {

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
        
        // Newsreader
        $criteria = array(
                "type" => array(
                        "=",
                        PressReviewTypes::ARTICLE
                )
        );
        $pressReviews = PressReviewSvc::getInstance()->getList($criteria, 50);
        if ($pressReviews) {
            $newsReader = new NewsReader($pressReviews, __("L'actualité du <strong>livre</strong> dans les médias", "s1b"));
            $this->view->newsReader = $newsReader->get();
        }
    }

    /**
     * Add common item to view model for list actions
     * @param String $key the page key (last chronicles, bloggers, bookstores)
     * @param Array of ChronicleModelView $chronicles the array of ChronicleViewModel to display
     * @param int $pageNumber the page number
     * @param String $navigationParamName the page navigation param name
     */
    private function addCommonListItemsToModelView($key, $chronicles, $pageNumber, $navigationParamName) {

        switch ($key) {
            case self::PAGE_KEY_ANY_GROUPS :
                
                $title = __("Dernières chroniques", "s1b");
                break;
            case self::PAGE_KEY_BLOGGERS :
                
                $title = __("En direct des blogs", "s1b");
                break;
            case self::PAGE_KEY_BOOKSTORES :
                
                $title = __("Le mot des libraires", "s1b");
                break;
        }
        
        // Add title list to model view
        $this->view->title = $title;
        
        // Add key value to model view to go in hidden filed for search form
        $this->view->key = $key;
        
        if (!$pageNumber)
            $pageNumber = 1;
        $chroniclesPaginated = new PaginatedList($chronicles, 5, $navigationParamName, $pageNumber);
        $chroniclesPage = $chroniclesPaginated->getItems();
        
        $chroniclesAdapter = new ChronicleListAdapter();
        $chroniclesAdapter->setChronicles($chroniclesPage);
        // Get as a chronicle view model list with 2 similar chronicles
        $chronicleDetailViewModelList = $chroniclesAdapter->getAsChronicleViewModelList(2);
        
        // Add chronicleDetailViewModel list to model view
        $this->view->chronicleDetailViewModelList = $chronicleDetailViewModelList;
        
        // Add navigation bar to view model
        $this->view->navigationBar = $chroniclesPaginated->getNavigationBar();
        
        // Add more seen chronicles to model view
        $nbMoreSeenChronicles = 5;
        $orderArray = array(
                "nb_views",
                "DESC"
        );
        switch ($key) {
            case self::PAGE_KEY_ANY_GROUPS :
                $moreSeenChronicles = ChronicleSvc::getInstance()->getLastChronicles($nbMoreSeenChronicles, null, GroupTypes::BLOGGER . "," . GroupTypes::BOOK_STORE, true, null, $orderArray);
                break;
            case self::PAGE_KEY_BLOGGERS :
                $moreSeenChronicles = ChronicleSvc::getInstance()->getLastChronicles($nbMoreSeenChronicles, GroupTypes::BLOGGER, null, true, null, $orderArray);
                break;
            case self::PAGE_KEY_BOOKSTORES :
                $moreSeenChronicles = ChronicleSvc::getInstance()->getLastChronicles($nbMoreSeenChronicles, GroupTypes::BOOK_STORE, null, true, null, $orderArray);
                break;
        }
        if ($moreSeenChronicles) {
            $chroniclesAdapter->setChronicles($moreSeenChronicles);
            $moreSeenChroniclesView = new ChroniclesBlock($chroniclesAdapter->getAsChronicleViewModelLightList(), __("<strong>Chroniques</strong> les plus en vues", "s1b"));
            $this->view->moreSeenChronicles = $moreSeenChroniclesView->get();
        }
        
        // Add SEO (title, meta description and keywords)
        $routeName = Zend_Controller_Front::getInstance()->getRouter()
            ->getCurrentRouteName();
        $headerInformation = HeaderInformationSvc::getInstance()->getByRouteName($routeName);
        $this->view->tagTitle = $headerInformation->getTitle();
        $this->view->metaDescription = $headerInformation->getDescription();
        $this->view->metaKeywords = $headerInformation->getKeywords();
    }

    /**
     * Set the resulting array of ChronicleViewModel in session
     * @param String $key the page key (last chronicles, bloggers, bookstores)
     * @param array of ChronicleViewModel $value
     */
    private function setResultsInSession($key, $value) {

        $sessionData = new Zend_Session_Namespace(self::CHRONICLES_LIST);
        
        switch ($key) {
            case self::PAGE_KEY_ANY_GROUPS :
                $sessionData->resultsAnyGroups = $value;
                break;
            case self::PAGE_KEY_BLOGGERS :
                $sessionData->resultsBloggers = $value;
                break;
            case self::PAGE_KEY_BOOKSTORES :
                $sessionData->resultsBookStores = $value;
                break;
        }
    }

    /**
     * Get the resulting array of ChronicleViewModel from session
     * @param String $key the page key (last chronicles, bloggers, bookstores)
     */
    private function getResultsInSession($key) {

        $sessionData = new Zend_Session_Namespace(self::CHRONICLES_LIST);
        
        switch ($key) {
            case self::PAGE_KEY_ANY_GROUPS :
                return $sessionData->resultsAnyGroups;
                break;
            case self::PAGE_KEY_BLOGGERS :
                return $sessionData->resultsBloggers;
                break;
            case self::PAGE_KEY_BOOKSTORES :
                return $sessionData->resultsBookStores;
                break;
        }
    }

}
