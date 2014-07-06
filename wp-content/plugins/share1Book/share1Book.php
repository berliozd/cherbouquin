<?php

date_default_timezone_set('Europe/Paris');

/*
  Plugin Name: share1Book
  Plugin URI: http://www.berliozd.com
  Description: Module de bibliothèque communautaire, Affichage des livres de l'utilisateur connecté, des livres prêtés, souhaités
  Author: Didier
  Version: 1.0
  Author URI: http://www.berliozd.com
 */

// Start the plugin
if (!class_exists('share1Book')) {

    class share1Book {

        const PAGE_DIR = 'Page';
        const MY_BOOKS_KEY = 'myBooks';
        const BOOKS_PAGE = 'book/list';
        const ALL_BOOKS_KEY = 'allBooks';
        const WISHED_BOOKS_KEY = 'wishedBooks';
        const BORROWED_BOOKS_KEY = 'borrowedBooks';
        const LENDED_BOOKS_KEY = 'lendedBooks';
        const SEARCH_BOOK_KEY = 'searchBook';
        const LIST_OPTIONS_SUFFIX = "_options";
        const LIST_WORKED_SUFFIX = "_worked";
        const LIST_AUTHORS_FIRST_LETTER_SUFFIX = "_authorsFirstLetter";
        const LIST_TITLES_FIRST_LETTER_SUFFIX = "_titlesFirstLetter";

        public function isValidBooksKey($key) {
            $booksKeys = array(self::ALL_BOOKS_KEY, self::MY_BOOKS_KEY, self::WISHED_BOOKS_KEY, self::BORROWED_BOOKS_KEY, self::LENDED_BOOKS_KEY);
            if (in_array($key, $booksKeys))
                return true;
            return false;
        }

        ///////////////// Private variables
        private $isSubmit = false;
        private $flashes;
        private $traces;
        private $baseDir;
        private $baseUrl;
        private $defImg = "";
        private $msgNotConnectedUser; // __("Vous n'êtes pas connecté.");
        private $msgInternalError; //= __("Une erreur interne s'est produite.");
        private $pagesWithoutAuthentification = array("search/show", "search/submit", "book/view");
        private $isFriendLibrary = false;
        private $userId = null; // connected user's userid
        private $javascriptKeys = array();
        private $config = null;
        private $context = null;
        private $mailSvc = null;

        ////////////////// Accesseurs

        public function getIsSubmit() {
            return $this->isSubmit;
        }

        public function getFlashes() {
            return $this->flashes;
        }

        public function getTraces() {
            return $this->traces;
        }

        public function getMsgNotConnectedUser() {
            return $this->msgNotConnectedUser;
        }

        public function getJavascriptKeys() {
            return $this->javascriptKeys;
        }

        public function getConfig() {
            return $this->config;
        }

        public function getContext() {
            return $this->context;
        }

        public function getMailSvc() {
            return $this->mailSvc;
        }

        ////////////////////////////// Fin Accesseurs

        public function addJavascriptKeys($javascriptKeys) {
            $this->javascriptKeys[] = $javascriptKeys;
        }

        ///////////////////// =============== Constructeur =================
        function __construct() {

            // Load languages files
            load_plugin_textdomain('s1b', true, BASE_PATH . "languages");

            // register differents hooks and actions
            add_action('plugins_loaded', array(&$this, 'onPluginsLoaded'));

            // necessaire pour em pécher wordpress de rajouter des <p></p> dans le contenu
            remove_filter('the_content', 'wpautop');

            // ajoute le shortcode [share1book] qui renvoir la vue principale du module
            add_shortcode('share1book', array(&$this, 'share1book_library'));
        }

        /**
         * shortcode function [share1book]
         * @param type $atts : array of shortcode attributes
         * @return type
         */
        public function share1book_library($atts) {

            try {

                // prépare le module (load  css et js, auto register des classes, démarrage de la session, initialisation des variables
                $this->prepare();

                // Récupération du mode d'affichage
                if (array_key_exists('mode', $_REQUEST)) {
                    $mode = $_REQUEST['mode'];
                    if ($mode == 'SUBMIT') {
                        $this->isSubmit = true;
                    }
                }

                $requested_page = self::BOOKS_PAGE;
                $page = $this->formatPagePath(self::BOOKS_PAGE);

                // Récupération de la page de demandée
                if (array_key_exists('page', $_REQUEST)) {
                    $requested_page = $_REQUEST['page'];
                }

                // Assignation de la page à inclure
                $tmpPage = $this->formatPagePath($requested_page);
                if (file_exists($tmpPage)) {
                    $page = $tmpPage;
                }

                $needAuthentification = (!in_array($requested_page, $this->pagesWithoutAuthentification));

                if ($needAuthentification && !$this->getIsConnected()) {
                    $_SESSION[\Sb\Entity\SessionKeys::RETURN_URL_AFTER_LOGIN] = $_SERVER["REQUEST_URI"];
                    Throw new \Sb\Exception\UserException($this->msgNotConnectedUser);
                } else {
                    // Récupération du flux de la page
                    ob_start();
                    \Sb\Trace\Trace::addItem("including : " . $page);
                    include $page;
                    $pageContent = ob_get_contents();
                    ob_end_clean();
                }

                // Récupération des traces
                if ($this->config->getTracesEnabled()) {
                    $this->traces = null;
                    if (\Sb\Trace\Trace::hasItems()) {
                        $this->traces = \Sb\Trace\Trace::getItems();
                    }
                }

                // header only needed for book list page
                $moduleOutput = $this->getMasterLoaded($pageContent, $this->outputStuff($this->traces, "traces"), null);

                return $moduleOutput;
            } catch (\Sb\Exception\UserException $exUser) {
                \Sb\Flash\Flash::addItem($exUser->getMessage());
                \Sb\Helpers\HTTPHelper::redirectToReferer();
            } catch (\Exception $exc) {
                return $exc->getMessage();
            }
        }

        //////////////////////// hooks

        /**
         * fonction appelée sur le hook comments_template
         * @param string $file
         * @return string
         */
        public function onCommentTemplate($file) {

            if (is_page()) {
                $file = dirname(__FILE__) . '/empty-file.php';
            }
            return $file;
        }

        /**
          Démarre la session (session_start)
          doit être fait le plus tôt possible (avant l'execution des templates)
          et démarre l'output buffer pour permettre notamment les redirections dans les pages
         */
        public function onPluginsLoaded() {

            $session_id = session_id();
            if (empty($session_id)) {
                session_start();
            }

            // démarre l'output buffer car différentes actions ne pouvant être faites
            // si le header HTTP est déjà renvoyé vont être faites par le module : redirections
            ob_start();
        }

        /**
         * Class loader.
         */
        public function loadClass($name) {
            $isProxy = false;
            if (strpos($name, "Proxies\\__CG__\\") !== false)
                $isProxy = true;
            if ($isProxy) {
                $prefix = "Sb\\Db\\Proxies\\__CG__";
                $name = str_replace("Proxies\\__CG__\\", "", $name);
                $name = $prefix . str_replace("\\", "", $name);
            }
            
            require(str_replace("\\", "/", $name) . ".php");
            return;
        }

        //////////////////////// FIN hooks


        public function compareWithConnectedUserId($bookUserId) {
            \Sb\Trace\Trace::addItem("user connecté : " . $this->userId . " == userBook id demandé : " . $bookUserId . " ?");
            if (!($this->userId == $bookUserId)) {
                \Sb\Flash\Flash::addItem(__("Le livre que vous souhaitez éditer ne correspond pas à l'utilisateur connecté.", "share1book"));
                \Sb\Helpers\HTTPHelper::redirectToLibrary();
            }
        }

        public function getMasterLoaded($content, $traces = "", $header = "") {
            // récupère l'output du module
            if ($header) {
                $tplMaster = new \Sb\Templates\Template("master");
                $tplMaster->setVariables(array("friendLibrary" => $this->isFriendLibrary));
            }
            else
                $tplMaster = new \Sb\Templates\Template("master-noheader");

            $tplMaster->set("traces", $traces);

            // Adding this javascript variable maually as it's not working when using wordpress functions. It's necessary for all ajax calls
            $script = sprintf("<script>var library = {\"isFriendLibrary\":\"%s\"};</script>", $this->isFriendLibrary ? "1" : "0");

            $tplMaster->set("content", $script . $content);
            if ($header)
                $tplMaster->set("header", $header);
            return $tplMaster->output();
        }

        public function getFunctionLoaded($content) {
            // récupère l'output des fonctions
            $tplFunction = new \Sb\Templates\Template("function");
            $tplFunction->set("content", $content);
            return $tplFunction->output();
        }

        /**
         * prépare le module :
         * - auto register Zend
         * - auto register des classes share1book
         * - initialisation des variables
         */
        public function prepare() {

            $this->initAutoLoad();
            
            // initialisation des options
            $this->initVariables();
        }

        /**
         * renvoit un bloc HTML correspond à une vue d'une partie du module mis à disposition par le biais d'une fonction
         * @param type $functionToCall
         * @param null $prmArray
         * @param bool|\type $needAuthentification
         * @throws Sb\Exception\UserException
         * @return type
         */
        public function functionOutput($functionToCall, $prmArray = null, $needAuthentification = true) {

            if ($needAuthentification && !$this->getIsConnected()) {
                Throw new \Sb\Exception\UserException($this->msgNotConnectedUser);
            } else {
                if ($prmArray) {
                    $content = call_user_func_array($functionToCall, $prmArray);
                } else {
                    $content = call_user_func($functionToCall);
                }
            }

            // Intégration du contenu dans le template et renvoi
            if ($content != "")
                $functionOuput = $this->getFunctionLoaded($content);
            else
                $functionOuput = $content;

            return $functionOuput;
        }

        ///////////////////////////////////////// Fonctions internes

        private function initAutoLoad() {

            // Registering Zend autoload
            set_include_path(get_include_path() . PATH_SEPARATOR . BASE_PATH . "/library");
            
            if (!defined('WP_ZEND_FRAMEWORK'))
                define('WP_ZEND_FRAMEWORK', true);
            require_once 'Zend/Loader/Autoloader.php';
            $autoloader = Zend_Loader_Autoloader::getInstance();

            // Registering Doctrine autoload
            require_once 'Doctrine/Doctrine/ORM/Tools/Setup.php';
            Doctrine\ORM\Tools\Setup::registerAutoloadGit("Doctrine");

            // Registering Share1book autoload
            spl_autoload_register(array($this, 'loadClass'));
        }

        private function initVariables() {

            $this->config =  new \Sb\Config\Model\Config();
            // Set config in global variable for Zend pages
            global $globalConfig;
            $globalConfig = $this->config;

            // TODO : remove and use context instead
            $this->baseDir = plugin_dir_path(__FILE__);
            $this->baseUrl = plugins_url('', __FILE__) . "/";
            $this->defImg = $this->baseUrl . 'Resources/images/nocover.png';
            $this->msgInternalError = __("Une erreur interne s'est produite.");
            $this->msgNotConnectedUser = __("Vous n'êtes pas connecté.");

            $connecteUserId = \Sb\Authentification\Service\AuthentificationSvc::getInstance()->getConnectedUserId();
            if ($connecteUserId)
                $this->userId = $connecteUserId;            

            // Set context
            $context = \Sb\Context\Model\Context::createContext($this->userId);
            $this->context = $context;       
            // Set context in global variable for Zend pages
            global $globalContext;
            $globalContext = $context;

            // mailSvc needs config object just created to create itself
            $this->mailSvc = \Sb\Service\MailSvc::getInstance();
        }


        public function getIsConnected() {
            return \Sb\Authentification\Service\AuthentificationSvc::getInstance()->getIsConnected();
        }

        private function formatPagePath($page) {
            return  __DIR__ . '/Page/' . $page . '.php';
        }

        private function outputStuff($stuffs, $stuffName) {
            $ret = "";
            if ($stuffs) {
                $ret .= "<div id=\"$stuffName-wrap\"><div id=\"$stuffName-background\"></div><div id='flashes'><div id='$stuffName-close-button'></div><ul>";
                foreach ($stuffs as $stuff) {
                    $ret .= "<li>" . $stuff . "</li>";
                }
                $ret .= "</ul></div></div>";
            }
            return $ret;
        }

        ////////////////////////////// List options and meta datas (authors, titles first letters) ///////////////////////////

        public function getListOptions($fullKey) {
            $opts = \Sb\Cache\ZendFileCache::getInstance()->load($fullKey . self::LIST_OPTIONS_SUFFIX);
            if ($opts instanceof Sb\Lists\Options) {
                return $opts;
            } else {
                return null;
            }
        }

        public function setListOptions($fullKey, $value) {
            \Sb\Cache\ZendFileCache::getInstance()->save($value, $fullKey . self::LIST_OPTIONS_SUFFIX);
        }

        public function resetListOption($fullKey) {
            $fullKey = $fullKey . self::LIST_OPTIONS_SUFFIX;
            \Sb\Cache\ZendFileCache::getInstance()->remove($fullKey);
        }
    }
}

$s1b = new share1Book();