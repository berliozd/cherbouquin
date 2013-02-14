<?php

namespace Sb\Authentification\Service;

use Sb\Helpers\HTTPHelper;
use Sb\Helpers\ArrayHelper;
use Sb\Flash\Flash;
use Sb\Trace\Trace;
use Sb\Entity\Urls;
use Sb\Entity\SessionKeys;
use Sb\Db\Model\User;

class AuthentificationSvc {

    private static $instance;

    /**
     *
     * @return \Sb\Authentification\Service\AuthentificationSvc
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new AuthentificationSvc;
        return self::$instance;
    }

    protected function __construct() {
        
    }

    public function loginSucces(User $activeUser) {
        // Initialisation des infos de connexion dans la session
        $this->initAuthenticatedUser($activeUser);
        // Redirection vers la page d'accueil
        Trace::addItem("Connection ok,redirecting to : " . Urls::USER_HOME);
        HTTPHelper::redirect(Urls::USER_HOME);
    }

    public function initAuthenticatedUser(User $activeUser) {
        $_SESSION['Auth'] = array(
            'Email' => $activeUser->getEmail(),
            'Password' => $activeUser->getPassword(),
            'Id' => $activeUser->getId());
        if ($activeUser->getFacebookId())
            $_SESSION['Auth']['FacebookId'] = $activeUser->getFacebookId();

        // If a return url is in session unset it and redirect to it
        $returnUrl = ArrayHelper::getSafeFromArray($_SESSION, SessionKeys::RETURN_URL_AFTER_LOGIN, null);
        if ($returnUrl) {
            unset($_SESSION[SessionKeys::RETURN_URL_AFTER_LOGIN]);
            HTTPHelper::redirectToUrl($returnUrl);
        }
    }

    public function getConnectedUserId() {
        if ($this->getIsConnected())
            return ArrayHelper::getSafeFromArray($_SESSION["Auth"], "Id", null);
        else
            return null;
    }

    public function getIsConnected() {
        if ($_SESSION && ArrayHelper::getSafeFromArray($_SESSION, "Auth", null) && ArrayHelper::getSafeFromArray($_SESSION["Auth"], "Id", null))
            return true;
        else
            return false;
    }

    /**
     * Check if a user is connected in session and otherwise set a flash message, persist request url in session and redirect to homepage
     */
    public function checkUserIsConnected() {
        if (!$this->getIsConnected()) {
            $_SESSION[\Sb\Entity\SessionKeys::RETURN_URL_AFTER_LOGIN] = $_SERVER["REQUEST_URI"];
            Flash::addItem(__("Vous devez être connecté pour accéder à cette page.", "s1b"));
            HTTPHelper::redirectToHome();
        }
    }

}