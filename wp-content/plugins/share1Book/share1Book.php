<?php
use Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager,
    Doctrine\DBAL\Event\Listeners\MysqlSessionInit,
    \Sb\Helpers\EntityHelper;

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
        private $friendUserId = null; // friend user id when showing friend library
        private $userId = null; // connected user's userid
        private $libraryUserId = null; // userid used for showing books, is the connected user id when not showing a friend library otherwise is the friend id
        private $libraryPageName = "";
        private $javascriptKeys = array();
        private $config = null;
        private $context = null;
        private $mailSvc = null;

        ////////////////// Accesseurs

        public function getIsSubmit() {
            return $this->isSubmit;
        }

        /**
         * Is set with the friend user id when a friend library is shown, otherwise is not shown
         * @return type
         */
        public function getFriendUserId() {
            return $this->friendUserId;
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

        public function getLibraryUserId() {
            return $this->libraryUserId;
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

                // Detect if plugin is loaded as a "friend library" or not, based on the shortcode attribute 'isfriendlibrary'
                extract(shortcode_atts(array('friendlibrary' => '0',), $atts));
                if ($friendlibrary == '1') {
                    $this->isFriendLibrary = true;
                }

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
                $pageContent = "";
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
                if ($requested_page == self::BOOKS_PAGE) {
                    // Préparation du header
                    $tplHeader = new \Sb\Templates\Template("header");

                    if ($this->context->getIsShowingFriendLibrary()) {
                        $friend = \Sb\Db\Dao\UserDao::getInstance()->get($this->friendUserId);
                        $friendUserName = $friend->getFirstName();
                        $tplHeader->setVariables(array("friendLibrary" => true,
                            "friendUserName" => $friendUserName));
                    } else {
                        $tplHeader->setVariables(array("friendLibrary" => false));
                    }

                    $this->setActiveTab($tplHeader); // Assigne le css class adéquat en fonction de la page
                    $moduleOutput = $this->getMasterLoaded($pageContent, $this->outputStuff($this->traces, "traces"), $tplHeader->output());
                } else {
                    $moduleOutput = $this->getMasterLoaded($pageContent, $this->outputStuff($this->traces, "traces"), null);
                }

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
            //echo $name . "<br/>";
            $isProxy = false;
            if (strpos($name, "Proxies\\__CG__\\") !== false)
                $isProxy = true;
            if ($isProxy) {
                $prefix = "Sb\Db\Proxies\__CG__";
                $name = str_replace("Proxies\\__CG__\\", "", $name);
                $name = $prefix . str_replace("\\", "", $name);
            }
            
            //echo $name . "<br/>";
            require(str_replace("\\", "/", $name) . ".php");
            return;
        }

        //////////////////////// FIN hooks

        public function setIsFriendLibrary($isFriendLibrary) {
            $this->isFriendLibrary = $isFriendLibrary;
        }

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
         * Determine if the current library shown is a friend's one or not
         * @return boolean
         */
        public function isShowingFriendLibrary() {
            if ($this->isFriendLibrary) {
                if ($this->friendUserId) {
                    return true;
                }
            }
            return false;
        }

        /**
         * Set the shown library's user id :
         * the connected user id in most of the case
         * the friend user id when a friend library is requested
         */
        public function setLibraryUserId() {
            if ($this->isShowingFriendLibrary()) {
                $this->libraryUserId = $this->friendUserId;
            } else {
                $this->libraryUserId = $this->userId;
            }
        }

        public function setLibraryPageName() {
            if ($this->isShowingFriendLibrary()) {
                $this->libraryPageName = $this->config->getFriendLibraryPageName();
            } else {
                $this->libraryPageName = $this->config->getUserLibraryPageName();
            }
        }

        private function createBookList($books, $listOptions) {
            return new \Sb\Lists\BookList($this->config->getListNbBooksPerPage(), $books, $listOptions);
        }

        public function formateListKey($key) {
            $tmpFriendId = "";
            if ($this->friendUserId) {
                $tmpFriendId = $this->friendUserId;
            }
            $fullKey = sprintf("%s_%s_%s_%s", $key, $this->userId, $tmpFriendId, session_id());
            return $fullKey;
        }

        public function createBookTableView($key, $books) {

            // Get full key for list option cache
            $fullKey = $this->formateListKey($key);

            // Get list options
            $listOpts = $this->getListOptions($fullKey);
            // Get potential search value and filtering
            $searchValue = "";
            $filteringType = "";
            $filter = "";
            if ($listOpts) {
                if ($listOpts->getSearch()) {
                    $searchValue = $listOpts->getSearch()->getValue();
                }
                if ($listOpts->getFiltering()) {
                    $filteringType = $listOpts->getFiltering()->getType();
                    $filter = $listOpts->getFiltering()->getValue();
                }
            }

            // Prepare list
            $list = $this->createBookList($books, $listOpts);

            $authorsFirstLetters = $this->getListMetaData($fullKey, Sb\Lists\MetaDataType::AUTHORS_FIRST_LETTERS);
            $titlesFirstLetters = $this->getListMetaData($fullKey, Sb\Lists\MetaDataType::TITLES_FIRST_LETTERS);

            return new \Sb\View\BookTable($key, $this->config->getUserLibraryPageName(), $this->config->getFriendLibraryPageName(),
                            $list->getShownResults(), $list->getPagerLinks(),
                            $list->getFirstItemIdx(), $list->getLastItemIdx(),
                            $list->getNbItemsTot(), $listOpts, $this->isShowingFriendLibrary(), $searchValue,
                            $authorsFirstLetters, $titlesFirstLetters, $filteringType, $filter);
        }

        /**
         * renvoit un bloc HTML correspond à une vue d'une partie du module mis à disposition par le biais d'une fonction
         * @global share1Book $s1b
         * @param type $functionToCall
         * @param type $needAuthentification
         * @return type
         */
        public function functionOutput($functionToCall, $prmArray = null, $needAuthentification = true) {

            $content = "";
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
            if ($this->isFriendLibrary) {
                $this->setFriendUserId();
            }
            $this->setLibraryUserId();
            $this->setLibraryPageName();

            // Set context
            $context = \Sb\Context\Model\Context::createContext($this->userId, $this->isShowingFriendLibrary(), $this->libraryUserId);
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

        /**
         * Function called when shortcode attribute friendlibray is set to 1
         * @throws \Sb\Exception\UserException
         */
        private function setFriendUserId() {
            $temporayFriendUSerId = null;
            // Get fid from QS
            if (array_key_exists("fid", $_GET)) {
                $temporayFriendUSerId = $_GET['fid'];
            } else { // Get fid from SESSION
                if (array_key_exists("fid", $_SESSION)) {
                    $temporayFriendUSerId = $_SESSION['fid'];
                }
            }

            // if fid is set, test if fid is a friend of the connected user or if the user has accepted everyone to see his library
            if ($temporayFriendUSerId) {

                $canAccessLibrary = $this->canAccessLibrary($temporayFriendUSerId);
                if ($canAccessLibrary) {
                    // if ok, fid is stored in SESSION and in private variables
                    $_SESSION['fid'] = $temporayFriendUSerId;
                    $this->friendUserId = $temporayFriendUSerId;
                } else {
                    throw new \Sb\Exception\UserException(__("Cette bibliothèque n'est pas accessible.", "s1b"));
                    unset($_SESSION['fid']);
                }
            } else {
                throw new \Sb\Exception\UserException(__("No friend id received."));
                unset($_SESSION['fid']);
            }
        }

        private function formatPagePath($page) {
            return  __DIR__ . '/Page/' . $page . '.php';
        }

        private function setActiveTab(&$tplHeader) {
            $key = "";
            if ($_GET && array_key_exists("key", $_GET) && $this->isValidBooksKey($_GET["key"])) {
                $key = $_GET["key"];
            }
            $tplHeader->set("cssAll", ($key == "allBooks" ? "active" : ""));
            $tplHeader->set("cssOwned", ($key == "myBooks" ? "active" : ""));
            $tplHeader->set("cssWished", ($key == "wishedBooks" ? "active" : ""));
            $tplHeader->set("cssLended", ($key == "lendedBooks" ? "active" : ""));
            $tplHeader->set("cssBorrowed", ($key == "borrowedBooks" ? "active" : ""));
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

        public function setListOptionsForSearching($listKey, $searchValue) {
            $listOptions = $this->getListOptions($listKey);
            if ($searchValue) {
                // Un objet Sb\Lists\Options a été récupéré pour cette liste : on assigne uniquement le search value
                if ($listOptions) {
                    if ($listOptions->getSearch()) {
                        $listOptions->getSearch()->setValue($searchValue);
                    } else {
                        $search = new \Sb\Lists\Search();
                        $search->setValue($searchValue);
                        $listOptions->setSearch($search);
                    }
                } else { // Aucun objet Sb\Lists\Options n'a été récupéré pour cette liste : on en créé un avec le numéro de page
                    $search = new \Sb\Lists\Search();
                    $search->setValue($searchValue);
                    $listOptions = new \Sb\Lists\Options();
                    $listOptions->setSearch($search);
                }
            }
//            var_dump($listOptions);
            $this->setListOptions($listKey, $listOptions);
        }

        public function setListOptionsForFiltering($listKey, $filteringValue, $filteringType) {
            $listOptions = $this->getListOptions($listKey);
            if ($filteringValue) {
                // Un objet Sb\Lists\Options a été récupéré pour cette liste : on assigne uniquement le filtering
                if ($listOptions) {
                    if ($listOptions->getFiltering()) {
                        $listOptions->getFiltering()->setType($filteringType);
                        $listOptions->getFiltering()->setValue($filteringValue);
                    } else {
                        $filtering = new \Sb\Lists\Filtering();
                        $filtering->setType($filteringType);
                        $filtering->setValue($filteringValue);
                        $listOptions->setFiltering($filtering);
                    }
                } else { // Aucun objet Sb\Lists\Options n'a été récupéré pour cette liste : on en créé un avec le filtering
                    $filtering = new \Sb\Lists\Filtering();
                    $filtering->setType($filteringType);
                    $filtering->setValue($filteringValue);
                    $listOptions = new \Sb\Lists\Options();
                    $listOptions->setFiltering($filtering);
                }
            }
//            var_dump($listOptions);
            $this->setListOptions($listKey, $listOptions);
        }

        public function setListOptionsForNavigation($listKey, $pageId) {
            $listOptions = $this->getListOptions($listKey);
            if ($pageId) {
                // Un objet Sb\Lists\Options a été récupéré pour cette liste : on assigne uniquement le numéro de page
                if ($listOptions) {
                    if ($listOptions->getPaging()) {
                        $listOptions->getPaging()->setCurrentPageId($pageId);
                    } else {
                        $paging = new \Sb\Lists\Paging();
                        $paging->setCurrentPageId($pageId);
                        $listOptions->setPaging($paging);
                    }
                } else { // Aucun objet Sb\Lists\Options n'a été récupéré pour cette liste : on en créé un avec le numéro de page
                    $paging = new \Sb\Lists\Paging();
                    $paging->setCurrentPageId($pageId);
                    $listOptions = new \Sb\Lists\Options();
                    $listOptions->setPaging($paging);
                }
            }
            $this->setListOptions($listKey, $listOptions);
        }

        public function setListOptionsForSorting($listKey, $sortCriteria) {
            \Sb\Trace\Trace::addItem("setListOptionsForSorting");
            $listOptions = $this->getListOptions($listKey);
            if ($sortCriteria) {
                \Sb\Trace\Trace::addItem("paramètre sortCriteria passé  : " . $sortCriteria);
                // Un objet Sb\Lists\Options a été récupéré pour cette liste : on assigne uniquement les infos de sorting
                if ($listOptions) {
                    \Sb\Trace\Trace::addItem("Un listOptions trouvé en session pour " . $listKey);
                    if ($listOptions->getSorting()) {
                        if ($listOptions->getSorting()->getField() == $sortCriteria) {
                            $sortDirection = ($listOptions->getSorting()->getDirection() == EntityHelper::ASC ? EntityHelper::DESC : EntityHelper::ASC);
                        } else {
                            $sortDirection = EntityHelper::ASC;
                        }
                        $listOptions->getSorting()->setField($sortCriteria);
                        $listOptions->getSorting()->setDirection($sortDirection);
                    } else {
                        $sorting = new \Sb\Lists\Sorting();
                        $sorting->setField($sortCriteria);
                        $sorting->setDirection(EntityHelper::ASC);
                        $listOptions->setSorting($sorting);
                    }
                } else { // Aucun objet Sb\Lists\Options n'a été récupéré pour cette liste : on en créé un avec le numéro de page
                    \Sb\Trace\Trace::addItem("Pas de listOptions trouvé en session pour " . $listKey);
                    $sorting = new \Sb\Lists\Sorting();
                    $sorting->setField($sortCriteria);
                    $sorting->setDirection(EntityHelper::ASC);
                    $listOptions = new \Sb\Lists\Options();
                    $listOptions->setSorting($sorting);
                }
            }
            $this->setListOptions($listKey, $listOptions);
        }

        public function setListMetaData($fullKey, $value, $metaDataType) {
            switch ($metaDataType) {
                case Sb\Lists\MetaDataType::AUTHORS_FIRST_LETTERS:
                    $fullKey = $fullKey . self::LIST_AUTHORS_FIRST_LETTER_SUFFIX;
                    break;
                case Sb\Lists\MetaDataType::TITLES_FIRST_LETTERS:
                    $fullKey = $fullKey . self::LIST_TITLES_FIRST_LETTER_SUFFIX;
                    break;
                default:
                    break;
            }
            \Sb\Cache\ZendFileCache::getInstance()->save($value, $fullKey);
        }

        public function getListMetaData($fullKey, $metaDataType) {
            switch ($metaDataType) {
                case Sb\Lists\MetaDataType::AUTHORS_FIRST_LETTERS:
                    $fullKey = $fullKey . self::LIST_AUTHORS_FIRST_LETTER_SUFFIX;
                    break;
                case Sb\Lists\MetaDataType::TITLES_FIRST_LETTERS:
                    $fullKey = $fullKey . self::LIST_TITLES_FIRST_LETTER_SUFFIX;
                    break;
                default:
                    break;
            }
            return \Sb\Cache\ZendFileCache::getInstance()->load($fullKey);
        }

        /**
         * Defines if we can access the user library (depends if is has accepted it or if he is a friend)
         * @param type $userId
         * @return boolean
         */
        private function canAccessLibrary($userId) {

            $requestedUser = \Sb\Db\Dao\UserDao::getInstance()->get($userId);
            $requestingUser = \Sb\Db\Dao\UserDao::getInstance()->get($this->userId);
            return \Sb\Helpers\SecurityHelper::IsUserAccessible($requestedUser, $requestingUser);
        }

    }

}

$s1b = new share1Book();