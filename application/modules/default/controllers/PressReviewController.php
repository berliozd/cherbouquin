<?php
use Sb\Trace\Trace;
use Sb\Lists\PaginatedList;
use Sb\Db\Service\PressReviewSvc;
use Sb\Entity\PressReviewTypes;
use Sb\Db\Service\TagSvc;
use Sb\Db\Dao\TagDao;

class Default_PressReviewController extends Zend_Controller_Action {

    public function init() {
        
        // Add chronicle css to head
        $this->view->headLink()
            ->appendStylesheet(BASE_URL . "resources/css/pressReviews.css?v=" . VERSION);
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
            
            // Add pressreview css to head
            $this->view->headLink()
                ->appendStylesheet(BASE_URL . "resources/css/pressReview.css?v=" . VERSION);
            
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
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

}
