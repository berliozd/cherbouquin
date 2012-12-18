<?php

namespace Sb\Config\Model;

/**
 * Description of Config
 *
 * @author Didier
 */
Class Config {


    private $tracesEnabled = SHARE1BOOK_TRACES_ENABLED;
    private $logsEnabled = SHARE1BOOK_LOGS_ENABLED;
    private $cacheTemplatingEnabled = SHARE1BOOK_CACHE_TEMPLATING_ENABLED;
    private $amazonApiKey = SHARE1BOOK_AMAZON_API_KEY;
    private $amazonSecretKey = SHARE1BOOK_AMAZON_SECRET_KEY;
    private $amazonAssociateTag = SHARE1BOOK_AMAZON_ASSOCIATE_TAG;
    private $googleApiKey = SHARE1BOOK_GOOGLE_API_KEY;
    private $searchNbResultsToShow = SHARE1BOOK_NB_RESULTS_TO_SHOW;
    private $searchNbResultsPerPage = SHARE1BOOK_NB_RESULTS_PER_PAGE;
    private $listNbBooksPerPage = SHARE1BOOK_LIST_NB_BOOKS_PER_PAGE;
    private $amazonNumberOfPageRequested = SHARE1BOOK_AMAZON_NB_OF_PAGE_REQUESTED;
    private $maxImportNb = SHARE1BOOK_MAX_IMPORT_NB;
    private $userLibraryPageName = SHARE1BOOK_USER_LIBRARY_PAGE_NAME; // Wordpress page for main library
    private $friendLibraryPageName = SHARE1BOOK_FRIEND_LIBRARY_PAGE_NAME; // Wordpress page for friend library
    private $facebookApiId = SHARE1BOOK_FACEBOOK_API_ID;
    private $facebookSecret = SHARE1BOOK_FACEBOOK_SECRET;
    private $maximumNbUserBooksForPublic = SHARE1BOOK_MAX_NB_BOOKS_FOR_PUBLIC;
    private $isProduction = IS_PRODUCTION;

    public function getTracesEnabled() {
        return $this->tracesEnabled;
    }

    public function getLogsEnabled() {
        return $this->logsEnabled;
    }

    public function getCacheTemplatingEnabled() {
        return $this->cacheTemplatingEnabled;
    }

    public function getAmazonApiKey() {
        return $this->amazonApiKey;
    }

    public function getAmazonSecretKey() {
        return $this->amazonSecretKey;
    }

    public function getAmazonAssociateTag() {
        return $this->amazonAssociateTag;
    }

    public function getGoogleApiKey() {
        return $this->googleApiKey;
    }

    public function getSearchNbResultsToShow() {
        return $this->searchNbResultsToShow;
    }

    public function getSearchNbResultsPerPage() {
        return $this->searchNbResultsPerPage;
    }

    public function getListNbBooksPerPage() {
        return $this->listNbBooksPerPage;
    }

    public function getAmazonNumberOfPageRequested() {
        return $this->amazonNumberOfPageRequested;
    }

    public function getMaxImportNb() {
        return $this->maxImportNb;
    }

    public function getUserLibraryPageName() {
        return $this->userLibraryPageName;
    }

    public function getFriendLibraryPageName() {
        return $this->friendLibraryPageName;
    }

    public function getFacebookApiId() {
        return $this->facebookApiId;
    }

    public function getFacebookSecret() {
        return $this->facebookSecret;
    }

    public function getMaximumNbUserBooksForPublic() {
        return $this->maximumNbUserBooksForPublic;
    }

    public function getIsProduction() {
        return $this->isProduction;
    }

}