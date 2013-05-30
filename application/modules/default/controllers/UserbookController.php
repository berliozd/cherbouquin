<?php
use Sb\Trace\Trace;

class Default_UserbookController extends Zend_Controller_Action {

    private function getConfig() {

        global $globalConfig;
        return $globalConfig;
    }

    private function getContext() {

        global $globalContext;
        return $globalContext;
    }

    public function init() {
        /* Initialize action controller here */
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('add', 'html')
            ->initContext();
    }

    public function addAction() {

        try {
            if ($this->getContext()
                ->getConnectedUser()) {
                $config = $this->getConfig();
                $return = \Sb\Db\Service\UserBookSvc::getInstance()->addFromPost($this->getContext()
                    ->getConnectedUser(), $config);
                
                $this->view->message = $return;
            } else
                $this->view->message = __("Vous devez être connecté pour ajouter un livre.", "s1b");
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->view->message = __("Une erreur s'est produite lors de l'ajout du livre.", "s1b");
        }
    }

}
