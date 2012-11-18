<?php

// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../wp-content/plugins/share1Book/Library'),
            get_include_path(),
        )));


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
if (APPLICATION_ENV == 'development') {
    $front = Zend_Controller_Front::getInstance();
    $front->setBaseUrl('/cherbouquin');
}

require_once(APPLICATION_PATH . '/configs/share1Book-config.php');

// Registering Doctrine autoload
require_once SHARE1BOOK_PLUGIN_PATH . '/Library/Doctrine/Doctrine/ORM/Tools/Setup.php';
Doctrine\ORM\Tools\Setup::registerAutoloadGit(SHARE1BOOK_PLUGIN_PATH . "/Library/Doctrine");

// Init internal autloloader for loading all class in /wp-content/plugins/share1Book
spl_autoload_register('loadClass');


// Démarrage de la session si besoin
$session_id = session_id();
if (empty($session_id))
    session_start();

// Set context
$connecteUserId = \Sb\Authentification\Service\AuthentificationSvc::getInstance()->getConnectedUserId();
$globalContext = \Sb\Context\Model\Context::createContext($connecteUserId, false, null);

// Set Config
$globalConfig = new \Sb\Config\Model\Config();


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

// Declare general autoloading function for all Sb classes
function loadClass($name) {
    $isProxy = false;
    if (strpos($name, "Proxies\\__CG__\\") !== false)
        $isProxy = true;
    if ($isProxy) {
        $prefix = "\Db\Proxies\__CG__";
        $name = str_replace("Proxies\\__CG__\\", "", $name);
        $name = $prefix . str_replace("\\", "", $name);
    } else {
        $name = str_replace("Sb", "", $name);
    }
    $require = SHARE1BOOK_PLUGIN_PATH . str_replace("\\", "/", $name) . ".php";

    require($require);
    return;
}

// ========================================================
// =========== FIN cherbouquin specicific code ============
// ========================================================

$application->bootstrap()
        ->run();

