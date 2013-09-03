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
use Sb\Helpers\ChronicleHelper;
use Sb\Helpers\ArrayHelper;
use Sb\Service\MailSvc;
use Sb\Entity\Constants;
class Default_IndexController extends Zend_Controller_Action {

    public function init() {
        
        // Add homepage css to head
        $this->view->headLink()->appendStylesheet(BASE_URL . "Resources/css/homepage.css?v=" . VERSION);
    }

    /**
     * Homepage controller
     * @global type $globalContext
     */
    public function indexAction() {
        try {
            global $globalContext;
            
            $this->view->placeholder('footer')->append("<script type=\"text/javascript\" src=\"" . BASE_URL . 'Resources/js/pressReviews.js?v=' . VERSION . "\"></script>");            
            $this->view->placeholder('footer')->append("<script type=\"text/javascript\" src=\"" . BASE_URL . 'Resources/js/newsReader.js?v=' . VERSION . "\"></script>");
            $this->view->placeholder('footer')->append("<script type=\"text/javascript\" src=\"" . BASE_URL . 'Resources/js/content.js?v=' . VERSION . "\"></script>");
            
            // Add chronicle css to head
            $this->view->headLink()->appendStylesheet(BASE_URL . "Resources/css/contents.css?v=" . VERSION);
            
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
            
            $this->view->placeholder('footer')->append("<script src=\"" . $globalContext->getBaseUrl() . 'Resources/js/simple-carousel/simple.carousel.js' . "\"></script>");
            $this->view->placeholder('footer')->append("<script>$(function() {initCarousel('carousel-items', 980, 340)});</script>");
            
            // Getting auto promo widget
            $autoPromoWishlist = new AutoPromoWishlistWidget();
            $this->view->autoPromoWishlist = $autoPromoWishlist->get();
            
            // Set chronicles (last one, last from any groups except bloggers and bookstore, last from bloggers, last from bookstores)
            $this->setViewChronicles();
            
            // Getting last rated books cover flip
            $this->view->placeholder('footer')->append("<script src=\"" . $globalContext->getBaseUrl() . 'Resources/js/waterwheel-carousel/jquery.waterwheelCarousel.min.js' . "\"></script>\n");
            $this->view->placeholder('footer')->append("<script>$(function () {initCoverFlip('lastRatedBooks', 30)});</script>\n");
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
            $pressReviews = $this->getNewsReaderPressReviews();
            if ($pressReviews) {
                $newsReader = new NewsReader($pressReviews);
                $this->view->newsReader = $newsReader->get();
            }
        } catch ( \Exception $e ) {
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

    public function activateAction() {

        $email = $this->getParam("Email", null);
        
        if ($email) {
            
            $user = UserDao::getInstance()->getByEmail($email);
            if ($user) {
                
                if ($user->getActivated())
                    Flash::addItem(__("utilisateur déjà actif", "s1b"));
                else {
                    $token = htmlspecialchars($this->getParam("Token", null));
                    if ($user->getToken() == $token) {
                        $user->setActivated(true);
                        UserDao::getInstance()->update($user);
                        Flash::addItem(__("Votre compte est désormais activé", "s1b"));
                    } else {
                        Flash::addItem(__("Token invalide!", "s1b"));
                    }
                }
            } else // user is unknown
                Flash::addItem(__("Une erreur est survenue lors de l'activation, merci de contacter l'administrateur via le formulaire de ", "s1b") . '<a href=' . Urls::CONTACT . '>' . __("contact", "s1b") . '</a>');
        }
        HTTPHelper::redirect(Urls::LOGIN);
    }

    public function contactAction() {

        try {
            
            if ($_POST) {
                
                $emailSent = false;
                
                if ($this->validateContactForm()) {
                    
                    $name = ArrayHelper::getSafeFromArray($_POST, "contactName", null);
                    $firstName = ArrayHelper::getSafeFromArray($_POST, "contactFirstName", null);
                    $email = ArrayHelper::getSafeFromArray($_POST, "email", null);
                    $message = ArrayHelper::getSafeFromArray($_POST, "comments", null);
                    $sendCopy = ArrayHelper::getSafeFromArray($_POST, "sendCopy", false);
                    
                    $subject = sprintf(__("Formulaire de contact : %s %s", "s1b"), $name, $firstName);
                    $body = sprintf(__("Nom : %s <br/>Prénom: %s <br/>Email : %s <br/>Message: %s <br/>", "s1b"), $name, $firstName, $email, $message);
                    
                    $mailSvc = MailSvc::getNewInstance(null, Constants::CONTACT_EMAIL);
                    $mailSvc->send(Constants::CONTACT_EMAIL . ", berliozd@gmail.com, rebiffe_olivier@yahoo.fr", $subject, $body);
                    
                    if ($sendCopy) {
                        $subject = __("Formulaire de contact", "s1b");
                        $copyMessage = sprintf(__("Merci d'avoir contacté %s.", "s1b"), Constants::SITENAME) . "<br/>" . __("Nous nous efforçons de vous répondre au plus vite.", "s1b") . "<br/>" . sprintf(__("L'équipe %s", "s1b"), Constants::SITENAME) . "<br/><br/>" . $body;
                        $mailSvc->send($email, $subject, $copyMessage);
                    }
                    
                    $emailSent = true;
                } else
                    Flash::addItem(__("Le message n'a pas pu être envoyé.", "s1b"));                    
                
                if ($emailSent) {

                    Flash::addItem(__("Merci.", "s1b"));
                    Flash::addItem(__("Votre e-mail a été envoyé. Vous recevrez une réponse au plus vite.", "s1b"));
                    Flash::addItem(sprintf(__("L'equipe %s", "s1b"), Constants::SITENAME));

                    HTTPHelper::redirect("");
                }
                
            }
        } catch ( \Exception $e ) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    private function setViewChronicles() {
        
        // Getting chronicles
        $lastChronicles = ChronicleSvc::getInstance()->getLastAnyType();
        $lastChronicle = array_slice($lastChronicles, 0, 1);
        $lastChronicle = $lastChronicle[0];
        $notBloggersOrBookStoresChronicles = ChronicleSvc::getInstance()->getLastChroniclesNotBloggersOrBookStores();
        $bloggersChronicles = ChronicleSvc::getInstance()->getLastBloggersChronicles();
        $bookstoresChronicles = ChronicleSvc::getInstance()->getLastBookStoresChronicles();
        
        // Init chronicle view model adapter
        $chronicleListAdapter = new ChronicleListAdapter();
        
        $chronicleView = new PushedChronicle($lastChronicle);
        $this->view->chronicle = $chronicleView->get();
        
        // Set chronicles from any groups except bloggers and bookstores
        if ($notBloggersOrBookStoresChronicles && count($notBloggersOrBookStoresChronicles) > 0) {
            // We take 3 first chronicles only and different from the last chronicle
            $notBloggersOrBookStoresChronicles = ChronicleHelper::getDifferentChronicles($lastChronicle, $notBloggersOrBookStoresChronicles, 3);
            // Set chronicles view
            $this->view->chronicles = $this->getChronicleView($chronicleListAdapter, $notBloggersOrBookStoresChronicles, __("Dernières <strong>chroniques</strong>", "s1b"), "last-chronicles", $this->view->url(array(), 'chroniclesLastAnyType'), __("Voir d'autres chroniques", "s1b"));
        }
        
        // Set bloggers chronicles
        if ($bloggersChronicles && count($bloggersChronicles) > 0) {
            // We take 3 first chronicles only and different from the last chronicle
            $bloggersChronicles = ChronicleHelper::getDifferentChronicles($lastChronicle, $bloggersChronicles, 3);
            // Set bloggers chronicle view
            $this->view->bloggersChronicles = $this->getChronicleView($chronicleListAdapter, $bloggersChronicles, __("En direct des blogs", "s1b"), "bloggers", $this->view->url(array(), 'chroniclesLastBloggers'), __("Voir tous les billets des bloggeurs", "s1b"));
        }
        
        // Set bookstores chronicles
        if ($bookstoresChronicles && count($bookstoresChronicles) > 0) {
            // We take 3 first chronicles only and different from the last chronicle
            $bookstoresChronicles = ChronicleHelper::getDifferentChronicles($lastChronicle, $bookstoresChronicles, 3);
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

    private function getNewsReaderPressReviews() {
        // Newsreader
        $criteria = array(
                "type" => array(
                        false, "=", PressReviewTypes::ARTICLE
                ), 
                // Add is_validated criteria
                "is_validated" => array(
                        false, "=", 1
                )
        );
        $pressReviews = PressReviewSvc::getInstance()->getList($criteria, 50);
        
        return $pressReviews;
    }

    private function validateContactForm() {
        $name = ArrayHelper::getSafeFromArray($_POST, "contactName", null);
        $firstName = ArrayHelper::getSafeFromArray($_POST, "contactFirstName", null);
        $email = ArrayHelper::getSafeFromArray($_POST, "email", null);
        $message = ArrayHelper::getSafeFromArray($_POST, "comments", null);
        
        $ok = true;
        
        if (!$name) {
            Flash::addItem(__("Indiquez votre nom", "s1b"));
            $ok = false;
        }
        if (!$firstName) {
            Flash::addItem(__("Indiquez votre prénom", "s1b"));
            $ok = false;
        }
        if (!$email) {
            Flash::addItem(__("Indiquez une adresse mail valide", "s1b"));
            $ok = false;
        } elseif (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\\.[A-Z]{2,4}$", $email)) {
            Flash::addItem(__("Indiquez une adresse mail valide", "s1b"));
            $ok = false;
        }
        if (!$message) {
            Flash::addItem(__("Le message est vide.", "s1b"));
            $ok = false;
        }
        
        return $ok;
    }

}
