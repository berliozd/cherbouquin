<?php

namespace Sb\Helpers;

use \Sb\Entity\Urls;

/**
 * @author Didier
 */
class HTTPHelper {

    /**
     *
     * @return Config
     */
    private static function getConfig() {
        global $globalConfig;
        return $globalConfig;
    }

    /**
     * Redirect to the given page.
     * @param type $page target page
     * @param array $params page parameters
     */
    //public static function redirect($pageKey, array $params = array()) {
    public static function redirect($pageKey = "", array $params = array()) {
        $dest = self::Link($pageKey, $params);
        if (self::getConfig()->getTracesEnabled()) {
            \Sb\Trace\Trace::addItem("<a href='$dest'>Suite...</a>");
        } else {
            self::redirectToUrl($dest);
            die();
        }
    }

    public static function redirectToUrl($url) {
        header("Location: $url", true, 302);
        die();
    }

    /**
     * Redirect to a library page (book/view, userbook/add, etc...)
     */
    public static function redirectToLibrary(array $params = array()) {
        self::redirect(self::getConfig()->getUserLibraryPageName(), $params);
    }

    /**
     * Redirect to homepage
     */
    public static function redirectToHome() {
        self::redirect();
    }

    /**
     * Redirect to referer
     */
    public static function redirectToReferer() {
        if (array_key_exists("HTTP_REFERER", $_SERVER)) {
            self::redirectToUrl($_SERVER["HTTP_REFERER"]);
        }else
            self::redirect();
    }

    //public static function Link($pageKey, $params = array(), $secure = false) {
    public static function Link($pageKey = "", $params = array(), $secure = false, $addHost = true) {
        $base = "";
        if (array_key_exists("SCRIPT_NAME", $_SERVER)) {
            // For Zend pages
            $base = str_replace("public/index.php", "", $_SERVER['SCRIPT_NAME']);
            // For Wordpress pages
            $base = str_replace("index.php", "", $base);
            $base = str_replace("wp-admin/admin-ajax.php", "", $base);
        }

        if (count($params) > 0) {
            $dest = $base . $pageKey . '?' . http_build_query($params);
        } else {
            $dest = $base . $pageKey;
        }
        if ($addHost) {
            $dest = $_SERVER['HTTP_HOST'] . $dest;

            if ($secure)
                $dest = "https://" . $dest;
            else
                $dest = "http://" . $dest;
        }

        return $dest;
    }

    public static function encodeTextForURL($text) {
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace("/[áàâãªä]/u", "a", $text);
        $text = preg_replace("/[íìîï]/u", "i", $text);
        $text = preg_replace("/[éèêë]/u", "e", $text);
        $text = preg_replace("/[óòôõºö]/u", "o", $text);
        $text = preg_replace("/[úùûü]/u", "u", $text);
        $text = preg_replace("/[ñ]/u", "n", $text);
        $text = str_replace("ç", "c", $text);
        $text = preg_replace("/[':;.()]/u", "", $text);
        $text = preg_replace("/[\W]/u", "-", $text);
        $text = preg_replace("/[-]$/u", "", $text);
        return $text;
    }

    /**
     * Return the referer : when the referer is the library list page, we remove the "reset=1" parameters because we don't want the filtering, paging, sorting 
      values to be reset when return to the page
     * @return string
     */
    public static function getReferer() {
        // When referer is the library list page, we remove the "reset=1" parameters because we don't want the filtering, paging, sorting 
        // values to be reset when return to the page
        $referer = ArrayHelper::getSafeFromArray($_SERVER, "HTTP_REFERER", null);
        if ($referer) {
            $libraryUrl = self::Link(Urls::USER_LIBRARY);
            if (strpos($referer, $libraryUrl) !== false) {
                $referer = str_replace("reset=1", "", $referer);
            }
        }
        return $referer;
    }

}