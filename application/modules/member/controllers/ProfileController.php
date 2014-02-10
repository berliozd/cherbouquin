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
                $existingUserWithUserName = UserDao::getInstance()->getByUserName($newUserName);
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

    public function deleteAction() {

        try {

            global $globalContext;

            $user = $globalContext->getConnectedUser();
            $userSettings = $user->getSetting();

            $this->view->user = $user;
            $this->view->userSettings = $user->getSetting();
            if ($_POST) {

                $user->setDeleted(true);
                UserDao::getInstance()->update($user);
                session_destroy();
                HTTPHelper::redirect(Urls::LOGIN);
            }
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function gravatarAction() {
        try {

            global $globalContext;

            $user = $globalContext->getConnectedUser();
            $userSettings = $user->getSetting();

            $this->view->user = $user;
            $this->view->userSettings = $user->getSetting();

            if ($_POST) {
                if (!empty($_POST['gravatar'])) {
                    $gravatar = trim($_POST['gravatar']);
                    $user->setGravatar($gravatar);
                    UserDao::getInstance()->update($user);
                    Flash::addItem(__("Votre photo a été mise à jour.", "s1b"));
                } else {
                    Flash::addItem(__("Vous devez sélectionner au moins un Gravatar", "s1b"));
                }
                HTTPHelper::redirect(Urls::MY_PROFILE);
            }

        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    /**
     * Show password edit form
     */
    public function editPasswordAction() {

        try {
            global $globalContext;

            /* @var $user \Sb\Db\Model\User */
            $user = $globalContext->getConnectedUser();
            $this->view->user = $user;
            $this->view->userSettings = $user->getSetting();

            if (!$user->getPassword())
                Flash::addItem(__("Vous n'avez pas de mot de passe car vous vous êtes inscrit via Facebook.
                            Si vous souhaitez utiliser votre email et un mot de passe pour vous connecter
                            merci d'indiquer un mot de passe dans la case Nouveau mot de passe", "s1b"));

        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function submitPasswordAction() {

        try {

            global $globalContext;

            /* @var $user \Sb\Db\Model\User */
            $user = $globalContext->getConnectedUser();
            $this->view->user = $user;
            $this->view->userSettings = $user->getSetting();

            if ($_POST) {
                $Password_modif = trim($_POST['Password_modif']);
                $Password_old_crypted = sha1(trim($_POST['Password_old']));
                $Password_modif_crypted = sha1(trim($_POST['Password_modif']));

                // On teste si l'utilisateur à déjà un mot de passe
                // --> si oui il est inscrit via le formulaire share1Book
                // --> si non il est inscrit via Facebook Connect

                $redirect = false;

                // No password for user
                if (!$user->getPassword()) {
                    if (strlen($Password_modif) >= 8) {
                        // update password
                        $user->setPassword($Password_modif_crypted);
                        UserDao::getInstance()->update($user);

                        // set flash message
                        Flash::addItem(__("Vos modifications ont bien été enregistrées.", "s1b"));
                        $redirect = true;
                    } else {
                        // password not long enough
                        Flash::addItem(__("Le mot de passe doit contenir au moins 8 caractères", "s1b"));
                    }
                } else {
                    // on teste si l'ancien mot de passe est bon
                    if ($user->getPassword() == $Password_old_crypted) {

                        //On verifie que le mot de passe a 8 caracteres ou plus
                        if (strlen($Password_modif) >= 8) {

                            // update password
                            $user->setPassword($Password_modif_crypted);
                            UserDao::getInstance()->update($user);

                            // set flash message
                            Flash::addItem(__("Vos modifications ont bien été enregistrées.", "s1b"));

                            $redirect = true;

                        } else {
                            //Sinon, on dit que le mot de passe n'est pas assez long
                            Flash::addItem(__("Le mot de passe doit contenir au moins 8 caractères", "s1b"));
                        }
                    } else
                        Flash::addItem(__("Votre ancien mot de passe n'est pas correct", "s1b"));
                }

                if ($redirect)
                    HTTPHelper::redirect(Urls::MY_PROFILE);
                else
                    HTTPHelper::redirect(Urls::USER_PROFILE_EDIT_PASSWORD);
            }


        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

}