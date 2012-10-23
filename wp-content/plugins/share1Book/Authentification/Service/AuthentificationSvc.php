<?php

namespace Sb\Authentification\Service;

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

    public function loginSucces(\Sb\Db\Model\User $activeUser) {
        // Initialisation des infos de connexion dans la session
        $this->initAuthenticatedUser($activeUser);
        // Redirection vers la page d'accueil
        \Sb\Trace\Trace::addItem("Connexion réussie , redirecting to : " . \Sb\Entity\Urls::USER_HOME);
        \Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::USER_HOME);
    }

    public function initAuthenticatedUser(\Sb\Db\Model\User $activeUser) {
        $_SESSION['Auth'] = array(
            'Email' => $activeUser->getEmail(),
            'Password' => $activeUser->getPassword(),
            'Id' => $activeUser->getId());
        if ($activeUser->getFacebookId())
            $_SESSION['Auth']['FacebookId'] = $activeUser->getFacebookId();
    }

}