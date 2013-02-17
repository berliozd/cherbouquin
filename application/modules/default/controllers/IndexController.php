<?php

use Sb\Db\Model\User;
use Sb\Db\Mapping\UserMapper;
use Sb\Db\Dao\UserDao;
use Sb\Db\Service\BookSvc;
use Sb\Db\Service\GroupChronicleSvc;
use Sb\Db\Service\UserEventSvc;
use Sb\Authentification\Service\AuthentificationSvc;
use Sb\Entity\EventTypes;
use Sb\Entity\Urls;
use Sb\View\Components\FacebookFrame;
use Sb\View\LastReviews;
use Sb\View\PushedChronicle;
use Sb\View\BookCoverFlip;
use Sb\View\BookShelf;
use Sb\View\Components\NoBooksWidget;
use Sb\View\Components\TwitterWidget;
use Sb\View\Components\AutoPromoWishlistWidget;
use Sb\View\Components\Ad;
use Sb\View\Components\CommunityLastEvents;
use Sb\Flash\Flash;
use Sb\Helpers\HTTPHelper;

class Default_IndexController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    /**
     * Homepage controller
     * @global type $globalContext
     */
    public function indexAction() {

        global $globalContext;
        
        $this->view->tagTitle = "Cherbouquin - gérez et partagez votre bibliothèque avec vos amis, offrez leurs le bon livre et découvrez les coups de coeur de la communauté de lecteurs";
        $this->view->metaDescription = "Créez votre bibliothèque en ligne et partagez vos livres favoris au sein de la communauté de lecteurs";
        $this->view->metaKeywords = "cher bouquin, cherbouquin, achat, acheter, art, atlas, auteur, avis, bande dessinee, bandes dessinées, bd, bibliotheque, bibliotheque en ligne, commentaires, communaute, communauté de lecteurs, contes, critiques, critiques de livres, cuisine, dictionnaire, ecrivain, editeur, emprunt, emprunter, fantasy, histoire, lecture, lire, littérature, livre, livre ancien, livre enfant, livre jeunesse, livre occasion, livre photo, livre scolaire, livres en ligne, logiciel gestion bibliotheque, manga, notes, notice, partage, philosophie, poesie, policier, prêt, prêter, recommandation livres, reseau, roman, science fiction, thriller, tourisme, vente livre, vin, voyage";
        
        $bohBooks = BookSvc::getInstance()->getBOHForHomePage();
        if (count($bohBooks) == 0) {
            $noBohBooks = new NoBooksWidget(__("Aucun livre n'a encore été noté par les membres", "s1b"));
            $boh = $noBohBooks->get();
        } else {
            $bohBooksView = new BookShelf($bohBooks, __("<span class=\"pb-highlight\">Coups de coeur</span> des lecteurs", "s1b"));
            $boh = $bohBooksView->get();
        }
        $this->view->boh = $boh;

        $this->view->subscribeLink = HTTPHelper::Link(Urls::SUBSCRIBE);

        $facebookFrame = new FacebookFrame();
        $this->view->faceBookFrame = $facebookFrame->get();

        $ad = new Ad("user_login", "0457389056");
        $this->view->ad = $ad->get();

        $twitter = new TwitterWidget();
        $this->view->twitter = $twitter->get();

        $this->view->placeholder('footer')->append("<script src=\"" . $globalContext->getBaseUrl() . 'Resources/js/simple-carousel/simple.carousel.js' . "\"></script>");
        $this->view->placeholder('footer')->append("<script>$(function() {initCarousel('carousel-items', 980, 340)});</script>");

        // Getting auto promo widget
        $autoPromoWishlist = new AutoPromoWishlistWidget();
        $this->view->autoPromoWishlist = $autoPromoWishlist->get();

        // Getting chronicle
        $chronicleView = new PushedChronicle(GroupChronicleSvc::getInstance()->getLast());
        $this->view->chronicle = $chronicleView->get();

        // Getting last rated books cover flip
        $this->view->placeholder('footer')->append("<script src=\"" . $globalContext->getBaseUrl() . 'Resources/js/waterwheel-carousel/jquery.waterwheelCarousel.min.js' . "\"></script>\n");
        $this->view->placeholder('footer')->append("<script>$(function () {initCoverFlip('lastRatedBooks', 90)});</script>\n");
        $lastRatedBooks = BookSvc::getInstance()->getLastRatedBookForHomePage();        
        $lastRatedCoverFlip = new BookCoverFlip($lastRatedBooks, __("<strong>Les derniers livres notés</strong>", "s1b"), "lastRatedBooks", "");
        $this->view->lastRatedCoverFlip = $lastRatedCoverFlip->get();
        
        // Get last reviews
        $lastReviews = UserEventSvc::getInstance()->getLastEventsOfType(EventTypes::USERBOOK_REVIEW_CHANGE);
        $lastReviewsView = new LastReviews($lastReviews, __("Dernières critiques postées", "s1b"));
        $this->view->lastReviews = $lastReviewsView->get();
        
        // Get community last events
        $communityLastEvents = UserEventSvc::getInstance()->getLastEventsOfType(null, 15);
        $communityLastEventsView = new CommunityLastEvents($communityLastEvents);
        $this->view->communityLastEvents = $communityLastEventsView->get();
        $this->view->placeholder('footer')->append("<script>\n
            toInit.push(\"attachCommunityEventsExpandCollapse()\");\n
            function attachCommunityEventsExpandCollapse() {_attachExpandCollapseBehavior(\"js_communityLastEvents\", \"userEvent\", \"Voir moins d'activités\", \"Voir plus d'activités\");}\n
        </script>\n");
    }

    public function logAction() {

        $invalidDataMsg = __("Les informations saisies ne nous permettent pas de vous authentifier.", "s1b");
        $accountNotActivated = __("Votre compte n'est pas activé. Merci de vérifier votre boite email. Vous avez certainemnt reçu un message vous demandant de l'activer.", "s1b");
        $accountDeleted = __("Votre compte a été supprimé.", "s1b");

        if ($_POST) {

            $userInForm = new User;
            UserMapper::map($userInForm, $_POST);

            if ($userInForm->IsValidForS1bAuthentification()) {
                $activeUser = UserDao::getInstance()->getS1bUser($userInForm->getEmail(), $userInForm->getPassword());
                if ($activeUser) {
                    if ($activeUser->getDeleted()) {
                        Flash::addItem($accountDeleted);
                    } elseif (!$activeUser->getActivated()) {
                        Flash::addItem($accountNotActivated);
                    } else {
                        $activeUser->setLastLogin(new \DateTime);
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

}