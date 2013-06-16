<?php
use Sb\Trace\Trace;
use Sb\Lists\PaginatedList;
use Sb\Db\Service\PressReviewSvc;
use Sb\Entity\PressReviewTypes;
use Sb\Db\Service\TagSvc;
use Sb\Db\Dao\TagDao;
use Sb\View\Components\Ad;
use Sb\View\Components\PressReviewsSubscriptionWidget;
use Sb\Db\Service\ChronicleSvc;
use Sb\Adapter\ChronicleListAdapter;
use Sb\View\ChroniclesBlock;
use Sb\Db\Service\BookSvc;
use Sb\View\BookCoverFlip;
use Sb\Service\HeaderInformationSvc;
use Sb\View\Components\ContentSearch;

class Default_PressReviewController extends Zend_Controller_Action {

    const PRESSREVIEW_LIST = "PRESSREVIEW_LIST";

    private $navigationParamName = "pagenumber";

    public function init() {
        
        // Add css
        $this->view->headLink()
            ->appendStylesheet(BASE_URL . "Resources/css/contents.css?v=" . VERSION);
        
        // Add js
        $this->view->placeholder('footer')
            ->append("<script type=\"text/javascript\" src=\"" . BASE_URL . 'Resources/js/pressReviews.js?v=' . VERSION . "\"></script>");
        $this->view->placeholder('footer')
            ->append("<script type=\"text/javascript\" src=\"" . BASE_URL . 'Resources/js/content.js?v=' . VERSION . "\"></script>");
    }

    /**
     * Action for press review (type article) list pages
     */
    public function listAction() {

        try {
            
            // Get article press reviews using cache
            $pressReviews = $this->getPressReviews(PressReviewTypes::ARTICLE, null, null, true);
            
            // Add common items to model view
            $this->addCommonListItemsToModelView(null, null, null, $pressReviews, PressReviewTypes::ARTICLE);
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    /**
     * Action for press review (type video) list pages
     */
    public function videosAction() {

        try {
            
            // Get video press reviews using cache
            $pressReviews = $this->getPressReviews(PressReviewTypes::VIDEO, null, null, true);
            
            // Add common items to model view
            $this->addCommonListItemsToModelView(null, null, null, $pressReviews, PressReviewTypes::VIDEO);
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    /**
     * Action for press review search (video or articles)
     */
    public function searchAction() {

        try {
            
            // Get press review type
            $typeId = $this->getParam('pageKey', null);
            
            // Get search parameters : tag id and search term
            $tagId = $this->getParam('tid', null);
            $searchTerm = $this->getParam("contentSearchTerm", null);
            
            $pageNumber = $this->getParam($this->navigationParamName, null);
            
            if ($pageNumber) // Get press reviews from session when paging
                $pressReviews = $this->getResultsInSession($typeId);
            else { // Get press reviews from sql and and store them into session
                $pressReviews = $this->getPressReviews($typeId, $tagId, $searchTerm, false);
                $this->setResultsInSession($typeId, $pressReviews);
            }
            
            // Add common items to model view
            $this->addCommonListItemsToModelView($pageNumber, $tagId, $searchTerm, $pressReviews, $typeId);
            
            switch ($typeId) {
                case PressReviewTypes::ARTICLE :
                    $this->render("list");
                    break;
                case PressReviewTypes::VIDEO :
                    $this->render("videos");
                    break;
            }
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    private function addCommonListItemsToModelView($pageNumber, $tagId, $searchTerm, $pressReviews, $pressReviewTypeId) {

        if (!$pageNumber)
            $pageNumber = 1; //
                                 
        // Add press review list to model view
        $pressReviewsPaginated = $this->getPressReviewsPaginated($pressReviews, $pageNumber, $pressReviewTypeId == PressReviewTypes::ARTICLE ? 10 : 5);
        $pagesPressReviews = $pressReviewsPaginated->getItems();
        $this->view->pressReviews = $pagesPressReviews;
        
        // Get tags and add it to model view
        $tags = TagSvc::getInstance()->getTagsForPressReviews();
        
        // Add reinit url to model view
        switch ($pressReviewTypeId) {
            case PressReviewTypes::ARTICLE :
                $initUrl = $this->view->url(array(), 'articlePressReviews');
                $searchLabel = __("Rechercher un article", "s1b");
                break;
            case PressReviewTypes::VIDEO :
                $initUrl = $this->view->url(array(), 'videoPressReviews');
                $searchLabel = __("Rechercher une vidéo", "s1b");
                break;
        }
        
        // Get search content form and add it to model view
        $contentSearch = new ContentSearch("/default/press-review/search", $tags, $tagId, $searchLabel, $pressReviewTypeId, $searchTerm, $initUrl);
        $this->view->contentSearch = $contentSearch->get();
        
        // Get press review subscription module and add it to view model
        $pressReviewSubscriptionWidget = new PressReviewsSubscriptionWidget();
        $this->view->pressReviewSubscriptionWidget = $pressReviewSubscriptionWidget->get();
        
        // Add navigation bar to view model
        $this->view->navigationBar = $pressReviewsPaginated->getNavigationBar();
        
        // Get ad and add it to view model
        $ad = new Ad("", "");
        $this->view->ad = $ad->get();
        
        // Get chronicles for right column widget and add to view model
        $chroniclesTitle = "";
        $chronicles = null;
        if ($tagId) {
            $chroniclesTitle = __("<strong>Chroniques</strong> dans la même catégorie", "s1b");
            $chronicles = ChronicleSvc::getInstance()->getChroniclesWithTags(array(
                    $tagId
            ), 5);
        } else {
            $chroniclesTitle = __("Dernières chroniques", "s1b");
            $chronicles = ChronicleSvc::getInstance()->getLastChronicles(5);
        }
        if (count($chronicles) > 0) {
            $chronicleAdapter = new ChronicleListAdapter();
            $chronicleAdapter->setChronicles($chronicles);
            $chroniclesView = new ChroniclesBlock($chronicleAdapter->getAsChronicleViewModelLightList(), $chroniclesTitle);
            $this->view->chroniclesView = $chroniclesView->get();
        }
        
        // Get books with press reviews for right column
        $this->view->placeholder('footer')
            ->append("<script src=\"" . BASE_URL . 'Resources/js/waterwheel-carousel/jquery.waterwheelCarousel.min.js' . "\"></script>\n");
        $this->view->placeholder('footer')
            ->append("<script>$(function () {initCoverFlip('booksWithPressReviews', 30)});</script>\n");
        $books = BookSvc::getInstance()->getListWithPressReviews(15);
        $booksCoverFlip = new BookCoverFlip($books, __("Les livres dont parlent <strong>les médias</strong>", "s1b"), "booksWithPressReviews", "");
        $this->view->booksCoverFlip = $booksCoverFlip->get();
        
        // Add SEO (title, meta description and keywords)
        $routeName = Zend_Controller_Front::getInstance()->getRouter()
            ->getCurrentRouteName();
        $headerInformation = HeaderInformationSvc::getInstance()->getByRouteName($routeName);
        $this->view->tagTitle = $headerInformation->getTitle();
        $this->view->metaDescription = $headerInformation->getDescription();
        $this->view->metaKeywords = $headerInformation->getKeywords();
    }

    /**
     * Get a paginated press review list
     * @param array of PressReview $pressReviews
     * @param int $pageNumber
     * @param int $nbOfItemsPerPage
     * @return \Sb\Lists\PaginatedList
     */
    private function getPressReviewsPaginated($pressReviews, $pageNumber, $nbOfItemsPerPage) {

        if (!$pageNumber)
            $pageNumber = 1;
        $pressReviewsPaginated = new PaginatedList($pressReviews, $nbOfItemsPerPage, $this->navigationParamName, $pageNumber);
        
        return $pressReviewsPaginated;
    }

    /**
     * Get the list of rpess review from SQL
     * @param int $pressReviewType
     * @param int $tagId
     * @param string $searchTerm
     * @param boolean $useCache
     * @return Ambigous <NULL, multitype:, mixed, false, boolean, string>
     */
    private function getPressReviews($pressReviewTypeId, $tagId, $searchTerm, $useCache) {

        $tag = null;
        if ($tagId)
            $tag = TagDao::getInstance()->get($tagId);
            
            // Initialize criteria array
        $criteria = array(
                "type" => array(
                        false,
                        "=",
                        $pressReviewTypeId
                )
        );
        
        // Add tag criteria
        if ($tag)
            $criteria["tag"] = array(
                    true,
                    "=",
                    $tag
            );
            
            // Add keywords criteria
        if ($searchTerm)
            $criteria["keywords"] = array(
                    false,
                    "LIKE",
                    $searchTerm
            );
        
        $result = PressReviewSvc::getInstance()->getList($criteria, 100, $useCache);
        return $result;
    }

    /**
     * Set list of press review in session
     * @param int $typeId
     * @param array of PressReview $value
     */
    private function setResultsInSession($typeId, $value) {

        $sessionData = new Zend_Session_Namespace(self::PRESSREVIEW_LIST);
        
        switch ($typeId) {
            case PressReviewTypes::ARTICLE :
                $sessionData->resultsArticles = $value;
                break;
            case PressReviewTypes::VIDEO :
                $sessionData->resultsVideos = $value;
                break;
        }
    }

    /**
     * Get list of press review from session
     * @param int $typeId
     * @return list of press review from session
     */
    private function getResultsInSession($typeId) {

        $sessionData = new Zend_Session_Namespace(self::PRESSREVIEW_LIST);
        
        switch ($typeId) {
            case PressReviewTypes::ARTICLE :
                return $sessionData->resultsArticles;
                break;
            case PressReviewTypes::VIDEO :
                return $sessionData->resultsVideos;
                break;
        }
        
        return null;
    }

}
