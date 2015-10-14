<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../vendor'),
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once 'autoload.php';

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
                APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini'
);

// ==========================================================
// =========== DEBUT cherbouquin specicific code ============
// ==========================================================

defined('BASE_URL') || define('BASE_URL', '/');
defined('BASE_PATH') || define('BASE_PATH', str_replace('public', '', dirname(__FILE__)));
defined('VERSION') || define('VERSION', '2.9');


// Démarrage de la session si besoin
$session_id = session_id();
if (empty($session_id))
    Zend_Session::start();

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

// ========================================================
// =========== FIN cherbouquin specicific code ============
// ========================================================

$application->bootstrap()
        ->run();