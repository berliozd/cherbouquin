<?php

namespace Sb\Authentification\Service;

use \Sb\Helpers\HTTPHelper;
use \Sb\Helpers\ArrayHelper;
use \Sb\Trace\Trace;
use \Sb\Entity\Urls;
use Sb\Entity\SessionKeys;
use \Sb\Db\Model\User;

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
        Trace::addItem("Connexion réussie , redirecting to : " . Urls::USER_HOME);
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
        if ($_SESSION 
                && (array_key_exists("Auth", $_SESSION)) 
                && (array_key_exists("Id", $_SESSION["Auth"])))
            return $_SESSION["Auth"]["Id"];
        else
            return null;
    }

    public function getIsConnected() {
        if ($_SESSION && (array_key_exists("Auth", $_SESSION)) && (array_key_exists("Id", $_SESSION["Auth"])))
            return true;
        else
            return false;
    }
}