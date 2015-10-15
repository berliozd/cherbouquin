<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH . '/../vendor'),
    get_include_path(),
)));

defined('BASE_URL') || define('BASE_URL', '/');
defined('BASE_PATH') || define('BASE_PATH', str_replace('tests', '', dirname(__FILE__)));
defined('VERSION') || define('VERSION', '2.9');

require_once 'autoload.php';


// Set WPLANG constant identically as in wordpress code
$isWPLangDefined = defined('WPLANG');
if (!$isWPLangDefined) {
    if (isset($_GET['lang'])) {
        $_SESSION['WPLANG'] = $_GET['lang'];
        define('WPLANG', $_SESSION['WPLANG']);
    } else {
        if (isset($_SESSION['WPLANG'])) {
            define('WPLANG', $_SESSION['WPLANG']);
            $_GET['lang'] = $_SESSION['WPLANG'];
        } else {
            define('WPLANG', 'fr_FR');
        }
    }
}

// Declare localization functions used in all pages and codes
function __($stringId, $domain = "") {
    return \Sb\Helpers\LocalizationHelper::getZendTranslate()->_($stringId);
}

function _e($stringId, $domain = "") {
    echo \Sb\Helpers\LocalizationHelper::getZendTranslate()->_($stringId);
}


