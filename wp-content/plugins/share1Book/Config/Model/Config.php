<?php

namespace Sb\Config\Model;

/**
 * Description of Config
 *
 * @author Didier
 */
interface Config {

    /**
     * Return singleton instance
     * @return Config
     */
    public static function getInstance();

    public function getTracesEnabled();

    public function getLogsEnabled();

    public function getCacheTemplatingEnabled();

    public function getAmazonApiKey();

    public function getAmazonSecretKey();

    public function getAmazonAssociateTag();

    public function getGoogleApiKey();

    public function getSearchNbResultsToShow();

    public function getSearchNbResultsPerPage();

    public function getListNbBooksPerPage();

    public function getAmazonNumberOfPageRequested();

    public function getMaxImportNb();

    public function getUserLibraryPageName();

    public function getFriendLibraryPageName();

    public function getFacebookApiId();

    public function getFacebookSecret();
    
    public function getMaximumNbUserBooksForPublic();
}