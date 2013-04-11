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

    public static function createContext($userId, $isShowFriendLibrary, $libraryUserId) {

        // Set context params except isShowingFriendLibrary and user
        $context = new \Sb\Context\Model\Context();
        $context->setBaseDirectory(BASE_PATH);
        $context->setBaseUrl(BASE_URL);
        $context->setDefaultImage(\Sb\Helpers\BookHelper::getDefaultImage());

        // Set context param user
        if ($userId) {
            $user = \Sb\Db\Dao\UserDao::getInstance()->get($userId);
            $context->setConnectedUser($user);
        }

        // Set context param isShowingFriendLibrary
        $context->setIsShowingFriendLibrary($isShowFriendLibrary);

        if ($isShowFriendLibrary)
            $context->setLibraryUserId($libraryUserId);
        else
            $context->setLibraryUserId($userId);


        // Set the singleton for future use
        \Sb\Context\Model\Context::setInstance($context);

        return $context;
    }

}
