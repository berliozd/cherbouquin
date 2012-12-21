<?php

use Sb\Db\Service\BookSvc;
use Sb\Helpers\HTTPHelper;
use Sb\View\Components\FacebookFrame;
use Sb\Db\Service\GroupChronicleSvc;


class IndexController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        
        global $globalContext;

        $bohBooks = BookSvc::getInstance()->getBOHForHomePage();
        if (count($bohBooks) == 0) {
            $noBohBooks = new \Sb\View\Components\NoBooksWidget(__("Aucun livre n'a encore été noté par les membres", "s1b"));
            $boh = $noBohBooks->get();
        } else {
            $bohBooksView = new \Sb\View\BookShelf($bohBooks, __("<span class=\"pb-highlight\">Coups de coeur</span> des lecteurs","s1b"));
            $boh = $bohBooksView->get();
        }
        $this->view->boh = $boh;

        $this->view->subscribeLink = HTTPHelper::Link(\Sb\Entity\Urls::SUBSCRIBE);

        $facebookFrame = new FacebookFrame();
        $this->view->faceBookFrame = $facebookFrame->get();

        $ad = new \Sb\View\Components\Ad("user_login", "0457389056");
        $this->view->ad = $ad->get();
        
        $twitter = new \Sb\View\Components\TwitterWidget();
        $this->view->twitter = $twitter->get();
        
        $this->view->headScript()->appendFile(BASE_URL . 'Resources/js/simple.carousel.js');
        $this->view->headScript()->appendScript("jQuery(document).ready(function() {
            $('ul.carousel-items').simplecarousel({
                width:980,
                height:340,
                auto: 8000,
                fade: 200,
                pagination: true
            });
        });");
        
        $this->view->tagTitle = __("Cherbouquin - gérez et partagez votre bibliothèque avec vos amis, offrez leurs le bon livre et découvrez les coups de coeur de la communauté de lecteurs","s1b");
        $this->view->metaDescription = __("Créez votre bibliothèque en ligne et partagez vos livres favoris au sein de la communauté de lecteurs","s1b");
        $this->view->metaKeywords = __("cher bouquin, cherbouquin, achat, acheter, art, atlas, auteur, avis, bande dessinee, bandes dessinées, bd, bibliotheque, bibliotheque en ligne, commentaires, communaute, communauté de lecteurs, contes, critiques, critiques de livres, cuisine, dictionnaire, ecrivain, editeur, emprunt, emprunter, fantasy, histoire, lecture, lire, littérature, livre, livre ancien, livre enfant, livre jeunesse, livre occasion, livre photo, livre scolaire, livres en ligne, logiciel gestion bibliotheque, manga, notes, notice, partage, philosophie, poesie, policier, prêt, prêter, recommandation livres, reseau, roman, science fiction, thriller, tourisme, vente livre, vin, voyage", "s1b");
        
        $this->view->autoPromoWishListLink = HTTPHelper::Link(Sb\Entity\Urls::USER_FRIENDS_WISHLIST);
        $this->view->autoPromoWishListImage = $globalContext->getBaseUrl() . "Resources/images/homepage/auto-promo-wishList.png";
        $this->view->autoPromoWishListTitle = __("Offrez un livre à vos amis","s1b");
        
        $chronicleView = new Sb\View\PushedChronicle(GroupChronicleSvc::getInstance()->getLast());
        $this->view->chronicle = $chronicleView->get();
    }
}