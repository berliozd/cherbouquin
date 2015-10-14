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
        return new \Sb\Config\Model\Config();
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
        self::redirect(Urls::USER_LIBRARY, $params);
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
        } else
            self::redirect();
    }

    //public static function Link($pageKey, $params = array(), $secure = false) {
    public static function Link($pageKey = "", $params = array(), $secure = false, $addHost = true) {
        $base = "";
        if (array_key_exists("SCRIPT_NAME", $_SERVER)) {
            // For Zend pages
            $base = str_replace("index.php", "", $_SERVER['SCRIPT_NAME']);
            $base = str_replace("wp-admin/admin-ajax.php", "", $base);
        }

        if (count($params) > 0) {
            $dest = $base . $pageKey . '?' . http_build_query($params);
        } else {
            $dest = $base . $pageKey;
        }
        if ($addHost) {
            if (array_key_exists("HTTP_HOST", $_SERVER)) {
                $dest = $_SERVER['HTTP_HOST'] . $dest;
            }

            if ($secure)
                $dest = "https://" . $dest;
            else
                $dest = "http://" . $dest;
        }

        return $dest;
    }

    /**
     * Return the referer : when the referer is the library list page, we remove the "reset=1" parameters because we don't want the filtering, paging, sorting
      values to be reset when returning to the page
     * @return string
     */
    public static function getReferer() {
        // When referer is the library list page, we remove the "reset=1" parameters because we don't want the filtering, paging, sorting
        // values to be reset when returning to the page
        $referer = ArrayHelper::getSafeFromArray($_SERVER, "HTTP_REFERER", null);
        if ($referer && (strpos($referer, self::Link(Urls::USER_LIBRARY)) !== false))
			$referer = str_replace("reset=1", "", $referer);
        return $referer;
    }

    /**
     * Get host base in Host. Ex cherbouquin.fr when host is www.cherbouquin.fr:8080
     * @return string host base
     */
    public static function getHostBase() {
        $host = $_SERVER['HTTP_HOST'];
        if (strpos($host, ":") !== false) {
            $arr = explode(":", $host);
            $host = $arr[0];
        }

        $parts = explode(".", $host);
        $last = end($parts);
        $res = $last;
        $count = 0;
        while (($prev = prev($parts)) && $count < 1) {
            $res = $prev . '.' . $res;
            $count++;
        }
        return $res;

    }

}
