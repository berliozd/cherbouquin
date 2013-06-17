<?php
use Sb\Authentification\Service\AuthentificationSvc;
use Sb\ZendForm\ChronicleForm;
use Sb\Db\Model\Chronicle;
use Sb\Db\Dao\ChronicleDao;
use Sb\Db\Dao\UserDao;
use Sb\Db\Model\User;
use Sb\Db\Model\GroupUser;
use Sb\Helpers\HTTPHelper;
use Sb\Flash\Flash;
use Sb\Db\Dao\GroupDao;
use Sb\Db\Dao\TagDao;
use Sb\Db\Dao\BookDao;
use Sb\Trace\Trace;
use Sb\Entity\Constants;
use Sb\Facebook\Service\FacebookSvc;
use Sb\Adapter\ChronicleAdapter;

/**
 *
 * @author Didier
 */
class Member_ChronicleController extends Zend_Controller_Action {

    const EDIT_CHRONICLE_NAMESPACE = "EDIT_CHRONICLE_NAMESPACE";

    public function init() {
        
        // Check if user is connected
        AuthentificationSvc::getInstance()->checkUserIsConnected();
        
        // Add chronicle css to head
        $this->view->headLink()
            ->appendStylesheet(BASE_URL . "Resources/css/groupAdmin.css?v=" . VERSION);
    }

    public function addAction() {

        try {
            global $globalContext;
            
            $groupId = $this->getParam("gid", null);
            
            if (!$groupId) {
                Flash::addItem(__("Vous devez renseigner un identifiant de groupe.", "s1b"));
                HTTPHelper::redirectToReferer();
            }
            
            // Check if user is member of group
            $this->checkUserIsMemberOfGroup($groupId);
            
            $imageUploadPath = $this->getImageUploadPath($groupId);
            
            // Create directory to upload the images in case it doesn't exist for the group
            if (!file_exists($imageUploadPath))
                mkdir($imageUploadPath); //
                                             
            // Get chronicle form for a new chronicle and add it to view model
            $chronicleForm = new ChronicleForm($imageUploadPath, true);
            $chronicleForm->setNewChronicle($globalContext->getConnectedUser()
                ->getId(), $groupId);
            $this->view->form = $chronicleForm;
            
            // Get Group and add it to view model
            $group = GroupDao::getInstance()->get($groupId);
            $this->view->group = $group;
            
            return $this->render("edit");
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function updateAction() {

        try {
            global $globalContext;
            
            $chronicleId = $this->getParam("cid", null);
            
            if (!$chronicleId) {
                Flash::addItem(__("Vous devez renseigner un identifiant de chronique", "s1b"));
                HTTPHelper::redirectToReferer();
            }
            
            /* @var $chronicleToEdit Chronicle */
            $chronicleToEdit = ChronicleDao::getInstance()->get($chronicleId);
            if ($chronicleToEdit) {
                
                // Check if the connected user is a member of the chronicle group
                $this->checkUserCanEditChronicle($chronicleToEdit);
                
                // Get chronicle form for an existing chronicle and add it to the view model
                $imageUploadPath = $this->getImageUploadPath($chronicleToEdit->getGroup()
                    ->getId());
                $chronicleForm = new ChronicleForm($imageUploadPath, false);
                $chronicleForm->setExistingChronicle($chronicleToEdit);
                $this->view->form = $chronicleForm;
                
                // Add the group to the view model
                $this->view->group = $chronicleToEdit->getGroup();
                
                return $this->render("edit");
            } else {
                Flash::addItem(__("La chronique que vous souhaitez éditer n'existe pas.", "s1b"));
                HTTPHelper::redirectToReferer();
            }
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function postAction() {

        try {
            global $globalContext;
            
            // Form is not posted correctly, we redirect to the previous page
            if (!$this->getRequest()
                ->isPost()) {
                Flash::addItem(__("Requête invalide.", "s1b"));
                return HTTPHelper::redirectToReferer();
            }
            
            $groupId = $this->getParam("group_id", null);
            $imageUploadPath = $this->getImageUploadPath($groupId);
            
            $id = $this->getParam("id", null);
            // Check the form validity
            $form = new ChronicleForm($imageUploadPath, $id ? false : true);
            if (!$form->isValid($_POST)) {
                
                // Add the chronicle form to the view model
                $this->view->form = $form;
                
                // Add the group to the view model
                $group = GroupDao::getInstance()->get($groupId);
                $this->view->group = $group;
                
                // When form is not valid, we render the edit page
                return $this->render("edit");
            } else {
                
                // 1. If the form is valid, the image file has already been uploaded
                
                // 2. We add or update chronicle in database
                
                if ($form->getChronicleId()) { // Get existing chronicle from database
                    $chronicle = ChronicleDao::getInstance()->get($form->getChronicleId());
                } else // create new chronicle
                    $chronicle = new Chronicle(); //
                                                      
                // Set all chronicle members
                $this->setChronicleData($form, $chronicle);
                
                // Update chronicle if editing an existing one
                if ($form->getChronicleId())
                    ChronicleDao::getInstance()->update($chronicle);
                else // And add a new chronicle if not
                    ChronicleDao::getInstance()->add($chronicle); //
                                                                      
                // 3. Post on facebook if necessary
                if ($form->getChroniclePostOnFacebook())
                    $postOk = $this->postOnFacebook($chronicle); //
                                                                     
                // 4. We redirect to confirmation page
                $sessionData = new Zend_Session_Namespace(self::EDIT_CHRONICLE_NAMESPACE);
                $sessionData->chronicleId = $chronicle->getId();
                $sessionData->postedOnFacebook = $form->getChroniclePostOnFacebook();
                $sessionData->postedOnFacebookOk = $postOk;
                $sessionData->lock();
                $this->redirect("member/chronicle/confirmation");
            }
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function confirmationAction() {

        try {
            
            $sessionData = new Zend_Session_Namespace(self::EDIT_CHRONICLE_NAMESPACE);
            /* @var $chronicle Chronicle */
            $chronicle = ChronicleDao::getInstance()->get($sessionData->chronicleId);
            $this->view->chronicleLink = $chronicle->getDetailLink();
            $this->view->postedOnFacebook = $sessionData->postedOnFacebook;
            $this->view->postedOnFacebookOk = $sessionData->postedOnFacebookOk;
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    private function setChronicleData(ChronicleForm $form, Chronicle $chronicle) {

        global $globalContext;
        
        $book = BookDao::getInstance()->get($form->getChronicleBookId());
        $chronicle->setBook($book);
        
        $group = GroupDao::getInstance()->get($form->getChronicleGroupId());
        $chronicle->setGroup($group);
        
        if ($form->getChronicleImage()) {
            $fullImageName = $this->getImageUrl($form->getChronicleGroupId()) . "/" . $form->getChronicleImage();
            $chronicle->setImage($fullImageName);
        }
        
        $chronicle->setIs_validated(true);
        $chronicle->setKeywords($form->getChronicleKeywords());
        $chronicle->setLink($form->getChronicleLink());
        
        $tag = TagDao::getInstance()->get($form->getChronicleTagId());
        $chronicle->setTag($tag);
        
        $chronicle->setText($form->getChronicleText());
        $chronicle->setTitle($form->getChronicleTitle());
        $chronicle->setType_id($form->getChronicleType());
        $chronicle->setUser($globalContext->getConnectedUser());
        $chronicle->setLink_type($form->getChronicleLinkType());
    }

    private function checkUserIsMemberOfGroup($groupId) {

        global $globalContext;
        
        /* @var $user User */
        $user = UserDao::getInstance()->get($globalContext->getConnectedUser()
            ->getId());
        $found = false;
        foreach ($user->getGroupusers() as $groupUser) {
            /* @var $groupUser GroupUser */
            if ($groupUser->getGroup()
                ->getId() == $groupId) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            Flash::addItem(__("Vous ne pouvez pas éditer de chronique pour ce groupe.", "s1b"));
            HTTPHelper::redirectToReferer();
        }
    }

    private function checkUserCanEditChronicle(Chronicle $chronicle) {

        global $globalContext;
        
        $chronicleGroupId = $chronicle->getGroup()
            ->getId();
        
        $found = false;
        foreach ($globalContext->getConnectedUser()
            ->getGroupusers() as $groupUser) {
            /* @var $groupUser GroupUser */
            if ($groupUser->getGroup()
                ->getId() == $chronicleGroupId) {
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            Flash::addItem(__("Vous ne pouvez pas éditer cette chronique.", "s1b"));
            HTTPHelper::redirectToReferer();
        }
    }

    private function getImageUploadPath($groupId) {

        return BASE_PATH . "/images/chronicles/group_" . $groupId;
    }

    private function getImageUrl($groupId) {

        return "/images/chronicles/group_" . $groupId;
    }

    /**
     * Post a message on facebook
     * @param Chronicle $chronicle the chronicle to post on facebook
     * @return boolean return TRUE if post was succesfull, FALSE otherwise
     */
    private function postOnFacebook(Chronicle $chronicle) {

        try {
            
            global $globalConfig;
            global $globalContext;
            
            $facebookSvc = new FacebookSvc($globalConfig->getFacebookApiId(), $globalConfig->getFacebookSecret(), "", "");
            // Set facebook posts variables using a ChronicleAdapter and a PushedChronicle
            $chronicleAdapter = new ChronicleAdapter($chronicle);
            $pushedChronicle = $chronicleAdapter->getAsChronicleViewModelLight();
            $facebookMessage = $pushedChronicle->getTitle();
            $facebookTitle = sprintf(__("%s vient de poster une chronique sur%s", "s1b"), $globalContext->getConnectedUser()
                ->getFirstName(), Constants::SITENAME);
            $facebookCaption = $pushedChronicle->getShortenText();
            $facebookLink = $pushedChronicle->getDetailLink();
            $facebookPicture = $pushedChronicle->getImage();
            
            return $facebookSvc->post($facebookMessage, $facebookTitle, $facebookCaption, $facebookLink, $facebookPicture);
        } catch (\Exception $e) {
            
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            return false;
        }
    }

}
