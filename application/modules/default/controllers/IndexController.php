<?php
use Sb\Db\Model\User;
use Sb\Db\Mapping\UserMapper;
use Sb\Db\Dao\UserDao;
use Sb\Db\Service\BookSvc;
use Sb\Db\Service\ChronicleSvc;
use Sb\Db\Service\UserEventSvc;
use Sb\Authentification\Service\AuthentificationSvc;
use Sb\Entity\EventTypes;
use Sb\Entity\Urls;
use Sb\View\Components\FacebookFrame;
use Sb\View\LastReviews;
use Sb\View\PushedChronicle;
use Sb\View\BookCoverFlip;
use Sb\View\Components\TwitterWidget;
use Sb\View\Components\AutoPromoWishlistWidget;
use Sb\View\Components\Ad;
use Sb\Flash\Flash;
use Sb\Helpers\HTTPHelper;
use Sb\Adapter\ChronicleListAdapter;
use Sb\View\PushedChronicles;
use Sb\Trace\Trace;
use Sb\View\Components\PressReviewsSubscriptionWidget;
use Sb\View\Components\NewsReader;
use Sb\Db\Service\PressReviewSvc;
use Sb\Entity\PressReviewTypes;
use Sb\View\Components\GooglePlus;

class Default_IndexController extends Zend_Controller_Action {

    public function init() {
        
        // Add homepage css to head
        $this->view->headLink()
            ->appendStylesheet(BASE_URL . "Resources/css/homepage.css?v=" . VERSION);
    }

    /**
     * Homepage controller
     * @global type $globalContext
     */
    public function indexAction() {

        try {
            global $globalContext;
            
            $this->view->placeholder('footer')
                ->append("<script type=\"text/javascript\" src=\"" . BASE_URL . 'Resources/js/pressReviews.js?v=' . VERSION . "\"></script>");
            
            $this->view->placeholder('footer')
                ->append("<script type=\"text/javascript\" src=\"" . BASE_URL . 'Resources/js/newsReader.js?v=' . VERSION . "\"></script>");
            
            // Add chronicle css to head
            $this->view->headLink()
                ->appendStylesheet(BASE_URL . "Resources/css/contents.css?v=" . VERSION);
            
            $this->view->tagTitle = sprintf(__("%s : livre et littérature - tops | coups de cœur | critiques", "s1b"), \Sb\Entity\Constants::SITENAME);
            $this->view->metaDescription = __("Créez votre bibliothèque, partagez vos livres et coups de cœur avec la communauté de lecteurs et offrez le bon livre sans risque de doublon", "s1b");
            $this->view->metaKeywords = "BD|bibliotheque|commentaires|communaute|lecteurs|critiques|livres|emprunt|littérature|livre|notice|partage|policier|polar|prêt|recommandation|roman|thriller";
            
            $this->view->subscribeLink = HTTPHelper::Link(Urls::SUBSCRIBE);
            
            if (IS_PRODUCTION) {
                $facebookFrame = new FacebookFrame();
                $this->view->faceBookFrame = $facebookFrame->get();
                
                $ad = new Ad("user_login", "0457389056");
                $this->view->ad = $ad->get();
                
                $twitter = new TwitterWidget();
                $this->view->twitter = $twitter->get();
                
                $googlePlus = new GooglePlus();
                $this->view->googlePlus = $googlePlus->get();
            }
            
            $this->view->placeholder('footer')
                ->append("<script src=\"" . $globalContext->getBaseUrl() . 'Resources/js/simple-carousel/simple.carousel.js' . "\"></script>");
            $this->view->placeholder('footer')
                ->append("<script>$(function() {initCarousel('carousel-items', 980, 340)});</script>");
            
            // Getting auto promo widget
            $autoPromoWishlist = new AutoPromoWishlistWidget();
            $this->view->autoPromoWishlist = $autoPromoWishlist->get();
            
            // Set chronicles (last one, last from any groups except bloggers and bookstore, last from bloggers, last from bookstores)
            $this->setViewChronicles();
            
            // Getting last rated books cover flip
            $this->view->placeholder('footer')
                ->append("<script src=\"" . $globalContext->getBaseUrl() . 'Resources/js/waterwheel-carousel/jquery.waterwheelCarousel.min.js' . "\"></script>\n");
            $this->view->placeholder('footer')
                ->append("<script>$(function () {initCoverFlip('lastRatedBooks', 30)});</script>\n");
            $lastRatedBooks = BookSvc::getInstance()->getLastRatedBookForHomePage();
            $lastRatedCoverFlip = new BookCoverFlip($lastRatedBooks, __("Derniers livres notés", "s1b"), "lastRatedBooks", "");
            $this->view->lastRatedCoverFlip = $lastRatedCoverFlip->get();
            
            // Get last reviews
            $lastReviews = UserEventSvc::getInstance()->getLastEventsOfType(EventTypes::USERBOOK_REVIEW_CHANGE);
            $lastReviewsView = new LastReviews($lastReviews, __("Dernières critiques postées", "s1b"));
            $this->view->lastReviews = $lastReviewsView->get();
            
            // Press reviews subscription widget
            $pressReviewsSubscriptionWidget = new PressReviewsSubscriptionWidget();
            $this->view->pressReviewsSubscriptionWidget = $pressReviewsSubscriptionWidget->get();
            
            // Newsreader
            $criteria = array(
                    "type" => array(
                            false,
                            "=",
                            PressReviewTypes::ARTICLE
                    )
            );
            $pressReviews = PressReviewSvc::getInstance()->getList($criteria, 50);
            if ($pressReviews) {
                $newsReader = new NewsReader($pressReviews);
                $this->view->newsReader = $newsReader->get();
            }
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function logAction() {

        $invalidDataMsg = __("Les informations saisies ne nous permettent pas de vous authentifier.", "s1b");
        $accountNotActivated = __("Votre compte n'est pas activé. Merci de vérifier votre boite email. Vous avez certainemnt reçu un message vous demandant de l'activer.", "s1b");
        $accountDeleted = __("Votre compte a été supprimé.", "s1b");
        
        if ($_POST) {
            
            $userInForm = new User();
            UserMapper::map($userInForm, $_POST);
            
            if ($userInForm->IsValidForS1bAuthentification()) {
                $activeUser = UserDao::getInstance()->getS1bUser($userInForm->getEmail(), $userInForm->getPassword());
                if ($activeUser) {
                    if ($activeUser->getDeleted()) {
                        Flash::addItem($accountDeleted);
                    } elseif (!$activeUser->getActivated()) {
                        Flash::addItem($accountNotActivated);
                    } else {
                        $activeUser->setLastLogin(new \DateTime());
                        UserDao::getInstance()->update($activeUser);
                        AuthentificationSvc::getInstance()->loginSucces($activeUser);
                    }
                } else {
                    Flash::addItem($invalidDataMsg);
                }
            } else {
                Flash::addItem($invalidDataMsg);
            }
        }
        $this->_redirect('');
    }

    private function setViewChronicles() {
        
        // Getting chronicles
        $anyGroupTypesChronicles = ChronicleSvc::getInstance()->getLastChroniclesOfAnyType();
        $bloggersChronicles = ChronicleSvc::getInstance()->getLastBloggersChronicles();
        $bookstoresChronicles = ChronicleSvc::getInstance()->getLastBookStoresChronicles();
        
        // Init chronicle view model adapter
        $chronicleListAdapter = new ChronicleListAdapter();
        
        // Set chronicles from any groups except bloggers and bookstores
        if ($anyGroupTypesChronicles && count($anyGroupTypesChronicles) > 0) {
            
            $chronicleView = new PushedChronicle($anyGroupTypesChronicles[0]);
            $this->view->chronicle = $chronicleView->get();
            
            $anyGroupTypesChronicles = array_slice($anyGroupTypesChronicles, 1, 3);
            // Set chronicles view
            $this->view->chronicles = $this->getChronicleView($chronicleListAdapter, $anyGroupTypesChronicles, __("Dernières <strong>chroniques</strong>", "s1b"), "last-chronicles", $this->view->url(array(), 'chroniclesLastAnyType'), __("Voir d'autres chroniques", "s1b"));
        }
        
        // Set bloggers chronicles
        if ($bloggersChronicles && count($bloggersChronicles) > 0) {
            // We take 3 first chronicles only
            $bloggersChronicles = array_slice($bloggersChronicles, 0, 3);
            // Set bloggers chronicle view
            $this->view->bloggersChronicles = $this->getChronicleView($chronicleListAdapter, $bloggersChronicles, __("En direct des blogs", "s1b"), "bloggers", $this->view->url(array(), 'chroniclesLastBloggers'), __("Voir tous les billets des bloggeurs", "s1b"));
        }
        
        // Set bookstores chronicles
        if ($bookstoresChronicles && count($bookstoresChronicles) > 0) {
            // We take 3 first chronicles only
            $bookstoresChronicles = array_slice($bookstoresChronicles, 0, 3);
            // Set bookstores view
            $this->view->bookStoresChronicles = $this->getChronicleView($chronicleListAdapter, $bookstoresChronicles, __("Le mot des libraires", "s1b"), "bookstores", $this->view->url(array(), 'chroniclesLastBookStores'), __("Voir tous les billets des libraires", "s1b"));
        }
    }

    private function getChronicleView(ChronicleListAdapter $chronicleListAdapter, $chronicles, $title, $typeCSS, $link, $textLink) {
        // Getting list of view model
        $chronicleListAdapter->setChronicles($chronicles);
        $anyGroupTypeChronicesAsViewModel = $chronicleListAdapter->getAsChronicleViewModelLightList();
        // Get chronicles view
        $chroniclesView = new PushedChronicles($anyGroupTypeChronicesAsViewModel, $link, $title, $typeCSS, $textLink);
        return $chroniclesView->get();
    }

}
