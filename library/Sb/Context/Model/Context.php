<?php

namespace Sb\Context\Model;

/**
 * Description of Context
 *
 * @author Didier
 */
class Context {

    private $baseDirectory;
    private $baseUrl;
    private $connectedUser;
    private $defaultImage;
    private $isShowingFriendLibrary;
    private $libraryUserId; // user id to show the library
    private static $instance;

    public function __construct() {
        $this->setBaseDirectory(BASE_PATH);
        $this->setBaseUrl(BASE_URL);
        $this->setDefaultImage(\Sb\Helpers\BookHelper::getDefaultImage());

        // Set context param user
        $userId= \Sb\Authentification\Service\AuthentificationSvc::getInstance()->getConnectedUserId();
        if ($userId) {
            $user = \Sb\Db\Dao\UserDao::getInstance()->get($userId);
            $this->setConnectedUser($user);
        }
    }

    /**
     *
     * @return \Sb\Context\Model\Context
     */
    public static function getInstance() {
        return self::$instance;
    }

    public static function setInstance($instance) {
        self::$instance = $instance;
    }

    public function getBaseDirectory() {
        return $this->baseDirectory;
    }

    public function getBaseUrl() {
        return $this->baseUrl;
    }

    public function getConnectedUser() {
        return $this->connectedUser;
    }

    public function getDefaultImage() {
        return $this->defaultImage;
    }

    public function getIsShowingFriendLibrary() {
        return $this->isShowingFriendLibrary;
    }

    public function setBaseDirectory($baseDirectory) {
        $this->baseDirectory = $baseDirectory;
    }

    public function setBaseUrl($baseUrl) {
        $this->baseUrl = $baseUrl;
    }

    public function setConnectedUser($connectedUser) {
        $this->connectedUser = $connectedUser;
    }

    public function setDefaultImage($defaultImage) {
        $this->defaultImage = $defaultImage;
    }

    public function setIsShowingFriendLibrary($isShowingFriendLibrary) {
        $this->isShowingFriendLibrary = $isShowingFriendLibrary;
    }

    public function getLibraryUserId() {
        return $this->libraryUserId;
    }

    public function setLibraryUserId($libraryUserId) {
        $this->libraryUserId = $libraryUserId;
    }

}
