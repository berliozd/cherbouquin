<?php
use Sb\Authentification\Service\AuthentificationSvc;
use Sb\View\UserProfile;
use Sb\Helpers\ArrayHelper;
use Sb\Db\Dao\CountryDao;
use Sb\Db\Dao\UserDao;
use Sb\Flash\Flash;
use Sb\Helpers\DateHelper;
use Sb\Helpers\HTTPHelper;
use Sb\Entity\Urls;
use Sb\Trace\Trace;
class Member_ProfileController extends Zend_Controller_Action {

    public function init() {
        
        // Checks is user is connected
        AuthentificationSvc::getInstance()->checkUserIsConnected();
    }

    public function editAction() {
        global $globalContext;
        
        $user = $globalContext->getConnectedUser();
        $userSettings = $user->getSetting();
        
        $profile = new UserProfile($user, $userSettings, false, false, false);
        
        $userLang = ArrayHelper::getSafeFromArray($_SESSION, "WPLANG", "fr_FR");
        
        $countries = CountryDao::getInstance()->getAll();
        
        $userCountry = null;
        if ($user->getCountry())
            $userCountry = CountryDao::getInstance()->getCountryByCode($user->getCountry());
        
        $submitUrl = HTTPHelper::Link(Urls::USER_PROFILE_SUBMIT);
        
        $this->view->profileView = $profile->get();
        $this->view->user = $user;
        $this->view->userSettings = $userSettings;
        $this->view->userLang = $userLang;
        $this->view->countries = $countries;
        $this->view->userCountry = $userCountry;
        $this->view->submitUrl = $submitUrl;
    }

    public function submitAction() {
        global $globalContext;
        $user = $globalContext->getConnectedUser();
        $userSettings = $user->getSetting();
        
        $newFirstName = trim($this->getParam("FirstName_modif", null));
        $newLastName = trim($this->getParam("LastName_modif", null));
        $newUserName = trim($this->getParam("UserName_modif", null));
        $newGender = trim($this->getParam("Gender_modif", null));
        $newBirthDay = trim($this->getParam("BirthDay_pre_modif", null));
        $newAddress = trim($this->getParam("Address_modif", null));
        $newCity = trim($this->getParam("City_modif", null));
        $newZipCode = trim($this->getParam("ZipCode_modif", null));
        $newCountry = trim($this->getParam("Country_modif", null));
        $newLanguage = trim($this->getParam("Language_modif", null));
        
        $lang = ArrayHelper::getSafeFromArray($_SESSION, "WPLANG", "fr_FR");
        
        // on vérifie que tous les champs soient complétés
        if (strlen($newLastName) > 3 && strlen($newFirstName) > 1 && strlen($newUserName) > 1) {
            
            $userNameExist = false;
            
            // Check new username is not already existing in database
            if ($newUserName != $user->getUserName()) {
                $existingUserWithUserName = \Sb\Db\Dao\UserDao::getInstance()->getByUserName($newUserName);
                if ($existingUserWithUserName) {
                    Flash::addItem(__("Un membre utilise déjà l'identifiant que vous avez entré, merci d'en choisir un autre", "s1b"));
                    $userNameExist = true;
                }
            }
            
            if (!$userNameExist) {
                $user->setFirstName($newFirstName);
                $user->setLastName($newLastName);
                $user->setUserName($newUserName);
                $user->setGender($newGender);
                $user->setBirthDay(DateHelper::createDateBis($newBirthDay));
                $user->setAddress($newAddress);
                $user->setCity($newCity);
                $user->setZipCode($newZipCode);
                $user->setCountry($newCountry);
                $user->setLanguage($newLanguage);
                UserDao::getInstance()->update($user);
                Flash::addItem(__("Vos modifications ont bien été enregistrées", "s1b"));
            }
        } else {
            
            if (strlen($newLastName) < 3)
                Flash::addItem(__("votre nom doit comprendre au moins 3 caractères", "s1b"));
            
            if (strlen($newFirstName) < 1)
                Flash::addItem(__("merci d'indiquer votre prénom", "s1b"));
            
            if (strlen($newUserName) < 1)
                Flash::addItem(__("merci d'indiquer un identifiant", "s1b"));
        }
        
        $this->redirect(Urls::USER_PROFILE_EDIT);
    }

    /**
     * Show connected user profile
     */
    public function indexAction() {
    
        try {
            global $globalContext;
            $user = $globalContext->getConnectedUser();
            $this->view->user = $user;
            $this->view->userSettings = $user->getSetting();
    
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }
}