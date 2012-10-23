<?php

namespace Sb\Config\Model;

/**
 * Description of Config
 *
 * @author Didier
 */
class WordPressConfig implements Config {

    private $tracesEnabled = false;
    private $logsEnabled = false;
    private $cacheTemplatingEnabled = true;
    private $amazonApiKey = null;
    private $amazonSecretKey = null;
    private $amazonAssociateTag = null;
    private $googleApiKey = null;
    private $searchNbResultsToShow = null;
    private $searchNbResultsPerPage = null;
    private $listNbBooksPerPage = null;
    private $amazonNumberOfPageRequested = null;
    private $maxImportNb;
    private $userLibraryPageName = ""; // Wordpress page for main library
    private $friendLibraryPageName = ""; // Wordpress page for friend library
    private $facebookApiId;
    private $facebookSecret;
    private $maximumNbUserBooksForPublic;

    private static $instance;

    private function __construct() {
        $this->load();
    }

    /**
     * Return singleton instance
     * @return Config
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new WordPressConfig();
        return self::$instance;
    }

    private function load() {
//        var_dump("call db");
        $options = get_option("share1Book");
        // options du module
        $this->tracesEnabled = \Sb\Helpers\ArrayHelper::getSafeFromArray($options, 'tracesEnabled', false);
        $this->logsEnabled = \Sb\Helpers\ArrayHelper::getSafeFromArray($options, 'logsEnabled', false);
        $this->cacheTemplatingEnabled = \Sb\Helpers\ArrayHelper::getSafeFromArray($options, 'cacheTemplate', false);
        $this->amazonApiKey = \Sb\Helpers\ArrayHelper::getSafeFromArray($options, 'amazonAK', '');
        $this->amazonSecretKey = \Sb\Helpers\ArrayHelper::getSafeFromArray($options, 'amazonSK', '');
        $this->amazonAssociateTag = \Sb\Helpers\ArrayHelper::getSafeFromArray($options, 'amazonAT', '');
        $this->googleApiKey = \Sb\Helpers\ArrayHelper::getSafeFromArray($options, 'googleBooksAK', '');
        $this->searchNbResultsToShow = \Sb\Helpers\ArrayHelper::getSafeFromArray($options, 'searchNRTS', 20);
        $this->searchNbResultsPerPage = \Sb\Helpers\ArrayHelper::getSafeFromArray($options, 'searchNRPP', 3);
        $this->listNbBooksPerPage = \Sb\Helpers\ArrayHelper::getSafeFromArray($options, 'listNbBooksPerPage', 3);
        $this->amazonNumberOfPageRequested = \Sb\Helpers\ArrayHelper::getSafeFromArray($options, 'amazonNOfPR', 2);
        $this->maxImportNb = \Sb\Helpers\ArrayHelper::getSafeFromArray($options, 'maxImportNb', 2);
        $this->userLibraryPageName = \Sb\Helpers\ArrayHelper::getSafeFromArray($options, 'userLibraryPageName', '');
        $this->friendLibraryPageName = \Sb\Helpers\ArrayHelper::getSafeFromArray($options, 'friendLibraryPageName', '');
        $this->facebookApiId = \Sb\Helpers\ArrayHelper::getSafeFromArray($options, 'facebookApiId', "");
        $this->facebookSecret = \Sb\Helpers\ArrayHelper::getSafeFromArray($options, 'facebookSecret', "");
        $this->maximumNbUserBooksForPublic = \Sb\Helpers\ArrayHelper::getSafeFromArray($options, 'maximumNbUserBooksForPublic', 300);
    }

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

}