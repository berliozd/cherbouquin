<?php
use Sb\Trace\Trace;
use Sb\Lists\PaginatedList;
use Sb\Db\Service\PressReviewSvc;
use Sb\Entity\PressReviewTypes;
use Sb\Db\Service\TagSvc;
use Sb\Db\Dao\TagDao;
use Sb\View\Components\Ad;
use Sb\View\Components\PressReviewsSubscriptionWidget;

class Default_PressReviewController extends Zend_Controller_Action {

    public function init() {
        
        // Add css to head
        $this->view->headLink()
            ->appendStylesheet(BASE_URL . "resources/css/pressReviews.css?v=" . VERSION);
        // Add js to footer
        $this->view->placeholder('footer')
            ->append("<script type=\"text/javascript\" src=\"" . BASE_URL . 'Resources/js/pressReviews.js?v=' . VERSION . "\"></script>");
    }

    /**
     * Action for press review (type article) list pages
     */
    public function listAction() {

        try {
            $navigationParamName = "pagenumber";
            $pageNumber = $this->getParam($navigationParamName, null);
            
            $tagId = $this->_getParam('tid', null);
            $tag = null;
            if ($tagId) {
                $this->view->selectedTagId = $tagId;
                $tag = TagDao::getInstance()->get($tagId);
            }
            
            $tags = TagSvc::getInstance()->getTagsForPressReviews();
            $this->view->tags = $tags;
            
            $criteria = array(
                    "type" => PressReviewTypes::ARTICLE
            );
            if ($tag)
                $criteria["tag"] = $tag;
            $articlePressReviews = PressReviewSvc::getInstance()->getList($criteria, 100, true);
            $this->view->articlePressReviews = $articlePressReviews;
            
            if (!$pageNumber)
                $pageNumber = 1;
            $pressReviewsPaginated = new PaginatedList($articlePressReviews, 10, $navigationParamName, $pageNumber);
            $pagesPressReviews = $pressReviewsPaginated->getItems();
            
            // Add press review list to model view
            $this->view->pressReviews = $pagesPressReviews;
            
            // Add navigation bar to view model
            $this->view->navigationBar = $pressReviewsPaginated->getNavigationBar();
            
            // Get add and add it to view model
            $ad = new Ad("", "");
            $this->view->ad = $ad->get();
            
            // Get press review subscription module and add it to view model
            $pressReviewSubscriptionWidget = new PressReviewsSubscriptionWidget();
            $this->view->pressReviewSubscriptionWidget = $pressReviewSubscriptionWidget->get();
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

}
