<?php
use Doctrine\Common\ClassLoader,
    Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\Cache\ApcCache,
    Doctrine\ORM\Tools\Setup,
    Doctrine\DBAL\Event\Listeners\MysqlSessionInit,
    \Sb\Helpers\EntityHelper;


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
            $plugin_dir = basename(dirname(__FILE__));
            load_plugin_textdomain('share1book', true, $plugin_dir . "/Languages");

            // register differents hooks and actions
            add_action('wp_enqueue_scripts', array(&$this, "registerStylesAndScripts"));
            add_filter('comments_template', array(&$this, 'onCommentTemplate'));
            add_action('admin_menu', array(&$this, 'onAdminMenu'));
            add_action('plugins_loaded', array(&$this, 'onPluginsLoaded'));
            $this->registerAjaxActions();

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
         * fonction appelée sur le hook plugin_action_links
         * Place in Settings Option List
         * @staticvar type $this_plugin
         * @param type $links
         * @param type $file
         * @return type
         */
        public function onPluginActionLinks($links, $file) {
            //Static so we don't call plugin_basename on every plugin row.
            static $this_plugin;
            if (!$this_plugin)
                $this_plugin = plugin_basename(__FILE__);

            if ($file == $this_plugin) {
                $settings_link = '<a href="options-general.php?page=' . basename(__FILE__) . '">' . __('Settings', "s1b") . '</a>';
                array_unshift($links, $settings_link); // before other links
            }
            return $links;
        }

        /**
         *  prep options page insertion
         */
        public function onAdminMenu() {
            if (function_exists('add_submenu_page')) {

                //add_options_page(page title, menu title, capability, menu slug, callback function);
                add_options_page('share1Book', 'Share1Book', 'edit_plugins', basename(__FILE__), array(&$this, 'configPage'));

                add_filter('plugin_action_links', array(&$this, 'onPluginActionLinks'), 'edit_plugins', 2);
            }
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
                $prefix = "\Db\Proxies\__CG__";
                $name = str_replace("Proxies\\__CG__\\", "", $name);
                $name = $prefix . str_replace("\\", "", $name);
            } else {
                $name = str_replace("Sb", "", $name);
            }
            require(dirname(__FILE__) . str_replace("\\", "/", $name) . ".php");
            return;
        }

        public function configPage() {
            include('admin-page.php');
        }

        public function registerStylesAndScripts() {

            $facebookJs = 'http://connect.facebook.net/fr_FR/all.js#xfbml=1&appId=' . $this->config->getFacebookApiId();
            if ($_SESSION['WPLANG'] == 'en_US')
                $facebookJs = 'http://connect.facebook.net/en_US/all.js#xfbml=1&appId=' . $this->config->getFacebookApiId();
            $facebookInviteText = __("Rejoignez vos amis, suivez les livres que vous leurs prêtez et partagez avec eux vos dernières lectures et envies", "s1b");

        }

        private function registerAjaxActions() {

            require_once "Wrapper/AjaxActions.php";
            $ajaxActions = new \Sb\Wrapper\AjaxActions($this);

            // navigation dans la recherche
            $this->registerAjaxAction('searchBookNagivation', array(&$ajaxActions, 'onAjaxActionSearchABookNagivation'));

            // navigation dans les listes de la bibliothèque
            $this->registerAjaxAction('navigation', array(&$ajaxActions, 'onAjaxActionBooksNavigation'));

            // tri dans les listes
            $this->registerAjaxAction('sorting', array(&$ajaxActions, 'onAjaxActionBooksSort'));
            
            // For adding userBook
            $this->registerAjaxAction('addUserBook', array(&$ajaxActions, 'onAjaxAddUserBook'));
        }

        private function registerAjaxAction($actionName, $function) {
            add_action('wp_ajax_nopriv_' . $actionName, $function);
            add_action('wp_ajax_' . $actionName, $function);
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

        public function testDoctrine() {

            $applicationMode = "development";
            if ($applicationMode == "development") {
                $cache = new \Doctrine\Common\Cache\ArrayCache;
            } else {
                $cache = new \Doctrine\Common\Cache\ApcCache;
            }

            $config = new Configuration;
            $config->setMetadataCacheImpl($cache);
            $driverImpl = $config->newDefaultAnnotationDriver(array(dirname(__FILE__) . "/Db/Model"));
            $config->setMetadataDriverImpl($driverImpl);
            $config->setQueryCacheImpl($cache);
            $config->setProxyDir(dirname(__FILE__) . "/Db/Proxies");
            $config->setProxyNamespace('Proxies');

            if ($applicationMode == "development") {
                $config->setAutoGenerateProxyClasses(true);
            } else {
                $config->setAutoGenerateProxyClasses(false);
            }

            $logger = new Doctrine\DBAL\Logging\EchoSQLLogger();
            $config->setSQLLogger($logger);
            // Database connection information
            $connectionOptions = array(
                'driver' => 'pdo_mysql',
                'user' => DB_USER,
                'password' => DB_PASSWORD,
                'host' => DB_HOST,
                'dbname' => DB_NAME
            );


            // Create EntityManager
            $em = EntityManager::create($connectionOptions, $config);
            $em->getEventManager()->addEventSubscriber(new MysqlSessionInit('utf8', 'utf8_general_ci'));

            $userbook = $em->find("\Sb\Db\Model\UserBook", 6);
            $readingState = $em->find("\Sb\Db\Model\ReadingState", 2);
            $userbook->setReadingState($readingState);
            $em->persist($userbook);
            $em->flush();


//            $query = $em->createQuery("SELECT r FROM \Sb\Db\Model\ReadingState r WHERE r.code = ?1");
//            $query->setParameters(array(1 => 'READ'));
            //var_dump($query->getResult());
//            $userbook = $em->find("\Sb\Db\Model\UserBook", 12);
//            $userbook->setRating(0);
//            $book = $userbook->getBook();
//            Doctrine\Common\Util\Debug::dump($book);
//            var_dump(count($book->getUserBooks()));
//            var_dump($book->getRatingSum());
//            $book->deleteUserBook($userbook);
//            var_dump(count($book->getUserBooks()));
//            var_dump($book->getRatingSum());
            // Test d'affichage d'un userbook, des ces infos directes, des ces tags, des userbooks du tags et des books des userbook du tag, des auteurs
//            $userbook = $em->find("\Sb\Db\Model\UserBook", 17);
//            echo "Rating - ".$userbook->getRating(). '<br/>';
//            echo "User - ".$userbook->getUser()->getLastName(). '<br/>';
//            foreach ($userbook->getTags() as $tag) {
//                echo $tag->getId() . " - ".$tag->getLabel(). '<br/>';
//                echo 'userbook du tag '. $tag->getLabel(). ' : <br/>';
//                foreach ($tag->getUserBooks() as $userBook) {
//                    echo '1 userbook pour le book '. $userBook->getBook()->getTitle(). '<br/>';
//                    echo 'publisher :  '. $userBook->getBook()->getPublisher()->getName() . '<br/>';
//                    foreach ($userBook->getBook()->getContributors() as $contrib) {
//                        echo "auteur : " . $contrib->getFullName() . "<br/>";
//                    }
//                }
//            }
//            $publisher = new \Sb\Db\Model\Publisher;
//            $publisher = $em->find("\Sb\Db\Model\Publisher", 6);
//            echo $publisher->getName() . "<br/>";
//            foreach ($publisher->getBooks() as $book) {
//                echo $book->getTitle() . "<br/>";
//            }
//            $user = new \Sb\Db\Model\User;
//            $user = $em->find("\Sb\Db\Model\User", 5);
//
//            $book = new \Sb\Db\Model\Book;
//            $book = $em->find("\Sb\Db\Model\Book", 4);
//
//            $readingState = new \Sb\Db\Model\ReadingState;
//            $readingState = $em->find("\Sb\Db\Model\ReadingState", 2);
//
//            $tag1 = new \Sb\Db\Model\Tag;
//            $tag1 = $em->find("\Sb\Db\Model\Tag", 2);
//            $tag2 = new \Sb\Db\Model\Tag;
//            $tag2 = $em->find("\Sb\Db\Model\Tag", 3);
//            $tags = array($tag1, $tag2);
//
//            $userbook = new \Sb\Db\Model\UserBook;
//            $userbook->setRating(2);
//            $userbook->setIsBlowOfHeart(true);
//            $userbook->setUser($user);
//            $userbook->setReadingState($readingState);
//            $userbook->setBook($book);
//            $userbook->setTags($tags);
//
//            $em->persist($userbook);
//            $em->persist($user);
//            $em->persist($readingState);
//            $em->flush();
//
//            echo "Title : " . $book->getTitle() . "<br/>";
//            echo "AverageRating : " . $book->getAverageRating() . "<br/>";
//            $book = new \Sb\Db\Model\Book;
//            $book = $em->find("\Sb\Db\Model\Book", 5);
//            echo "nb userbooks: " . count($book->getUserBooks()) . "<br/>";
//
//            $userbook  = new \Sb\Db\Model\UserBook;
//            $userbook = $em->find("\Sb\Db\Model\UserBook", 7);
//            $em->remove($userbook);
//            $em->flush();
//
//            echo "nb userbooks: " . count($book->getUserBooks()) . "<br/>";
//            $userbook = new \Sb\Db\Model\UserBook;
//            $userbook = $em->find("\Sb\Db\Model\UserBook", 31);
//            $userbook->setRating(2);
//            $em->persist($userbook);
//            $em->flush();
//            $message = new \Sb\Db\Model\Message;
//            $message = $em->find("\Sb\Db\Model\Message", 10);
//            echo $message->getMessage();
//            $user = new \Sb\Db\Model\User;
//            $user = $em->find("\Sb\Db\Model\User", 3);
//            var_dump(count($user->getFriendships_as_friend()));
//            foreach ($user->getFriendships_as_user() as $friendShip) {
//                var_dump("moi : " . $friendShip->getUser()->getLastName() . " mon ami : " . $friendShip->getFriend()->getLastName() . " - validé : " . ($friendShip->getValidated() ? "oui" : "non") . " acepted  : " . ($friendShip->getAccepted() ? "oui" : "non"));
//            }
//            $recipient = new \Sb\Db\Model\User;
//            $recipient = $em->find("\Sb\Db\Model\User", 2);
//            $message = new \Sb\Db\Model\Message;
//            $message->setMessage("test de message avec doctrine -- corps");
//            $message->setTitle("test de message avec doctrine");
//            $message->setSender($sender);
//            $message->setRecipient($recipient);
//            $em->persist($message);
//            $em->flush();
        }

        public function checkNonce($nonceKey) {
//            if ($_REQUEST) {
//                if (array_key_exists("nonce", $_REQUEST)) {
//                    $nonce = $_REQUEST['nonce'];
//                }
//            }
//            // vérification du nonce : correspond t'il bien à un nonce généré plus tôt?
//            if (!wp_verify_nonce($nonce, $nonceKey))
//                throw new \Sb\Exception\UserException(__("Invalid ajax call"));            
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

        private function createBookList($key, $fullKey, $books, $listOptions) {
            return new \Sb\Lists\BookList($this->config->getListNbBooksPerPage(), $books, $this->baseDir, $listOptions);
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
            $list = $this->createBookList($key, $fullKey, $books, $listOpts);

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
            set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . "/Library");

            if (!defined('WP_ZEND_FRAMEWORK'))
                define('WP_ZEND_FRAMEWORK', true);
            require_once 'Library/Zend/Loader/Autoloader.php';
            $autoloader = Zend_Loader_Autoloader::getInstance();

            // Registering Doctrine autoload
            require_once 'Library/Doctrine/Doctrine/ORM/Tools/Setup.php';
            Doctrine\ORM\Tools\Setup::registerAutoloadGit(dirname(__FILE__) . "/Library/Doctrine");

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
            $this->mailSvc = \Sb\Mail\Service\MailSvcImpl::getInstance();
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
                    \Sb\Trace\Trace::addItem("fid in SESSION : " . $_SESSION['fid']);
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
                    \Sb\Trace\Trace::addItem("Affichage de la bibliothèque de l'ami dont le user id est : " . $temporayFriendUSerId);
                } else {
                    throw new \Sb\Exception\UserException(__("Cette bibliothèque n'est pas accessible.", "s1b"));
                    \Sb\Trace\Trace::addItem("unsetting fid");
                    unset($_SESSION['fid']);
                }
            } else {
                throw new \Sb\Exception\UserException(__("No friend id received."));
                \Sb\Trace\Trace::addItem("unsetting fid");
                unset($_SESSION['fid']);
            }
        }

        private function formatPagePath($page) {
            return dirname(__FILE__) . '/' . self::PAGE_DIR . '/' . $page . '.php';
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

function getShare1BookFunction($functionName, $prmArray = null, $needAuthentification = true) {
    global $s1b;
    try {
        $s1b->prepare();
        $functionWrapper = new \Sb\Wrapper\Functions($s1b);
        $out = $s1b->functionOutput(array(&$functionWrapper, $functionName), $prmArray, $needAuthentification);
        return $out;
    } catch (\Sb\Exception\UserException $exUser) {
        return sprintf(__("Error : %s"), $exUser->getMessage());
    } catch (Exception $exc) {
        return $exc->getMessage();
    }
}

function share1book_tops() {
    return getShare1BookFunction("getTopsList", null, false);
}

function share1book_topsFriends() {
    return getShare1BookFunction("getTopsFriendsList");
}

function share1book_blowOfHearts() {
    return getShare1BookFunction("getBlowOfHeartsList", null, false);
}

function share1book_blowOfHeartsFriends() {
    return getShare1BookFunction("getBlowOfHeartsFriendsList");
}

function share1book_userCurrentlyReading($userId) {
    return getShare1BookFunction("getUserCurrentlyReading", array($userId));
}

function share1book_userBlowsOfHeart($userId) {
    return getShare1BookFunction("getUserBlowsOfHeart", array($userId));
}

function share1book_userLastlyReadOrCurrentlyReading($userId) {
    return getShare1BookFunction("getUserLastlyReadOrCurrentlyReading", array($userId));
}

?>