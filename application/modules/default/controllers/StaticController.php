<?php

use Sb\Trace\Trace;
use Sb\Service\HeaderInformationSvc;

class Default_StaticController extends Zend_Controller_Action {

    public function init() {
        
    }

    /**
     * Action for displaying a static page
     */
    public function indexAction() {

        try {

            $this->getHelper("viewRenderer")->setNoRender(true);

            $file = BASE_PATH . "/static-files/" . $this->_getParam('file');

            if (file_exists($file)) {
                ob_start();
                include $file;
                $output = ob_get_contents();
                ob_end_clean();
                $this->getResponse()->appendBody($output);

                // Get Header information
                $routeName = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
                $headerInformation = HeaderInformationSvc::getInstance()->getForStaticPage($routeName);
                $this->view->tagTitle = $headerInformation->getTitle();
                $this->view->metaDescription = $headerInformation->getDescription();
                $this->view->metaKeywords = $headerInformation->getKeywords();
            } else
                throw new \Exception;
        } catch (\Exception $exc) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $exc->getTraceAsString()));
            $this->_forward("error", "error", "default");
        }
    }

}

