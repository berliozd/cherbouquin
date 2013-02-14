<?php

use \Sb\Db\Service\TagSvc;
use \Sb\Db\Service\BookSvc;

class Default_BooksController extends Zend_Controller_Action {

    private $selectedTagId;

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        // action body
    }

    public function lastAddedAction() {

        // Get all books
        $books = BookSvc::getInstance()->getLastlyAddedForPage();

        // Get tags for combo
        $this->view->tags = TagSvc::getInstance()->getTagsForBooks($books);

        $books = $this->filterBooks($books);
        $this->setPageList($books);

        $description = __("Cette sélection des derniers livres ajoutés par les membres sur Cherbouquin vous donnera sûrement des idées de lecture auquel vous n'aviez pas pensées.", "s1b");
        $title = __("Derniers livres ajoutés", "s1b");

        $this->view->tagTitle = sprintf(__("%s - %s", "s1b"), Sb\Entity\Constants::SITENAME, $title);
        $this->view->title = $title;

        $this->view->metaDescription = $description;
        $this->view->description = $description;

        $this->view->metaKeywords = "cher bouquin, cherbouquin, achat, acheter, art, atlas, auteur, avis, bande dessinee, bandes dessinées, bd, bibliotheque, bibliotheque en ligne, commentaires, communaute, communauté de lecteurs, contes, critiques, critiques de livres, cuisine, dictionnaire, ecrivain, editeur, emprunt, emprunter, fantasy, histoire, lecture, lire, littérature, livre, livre ancien, livre enfant, livre jeunesse, livre occasion, livre photo, livre scolaire, livres en ligne, logiciel gestion bibliotheque, manga, notes, notice, partage, philosophie, poesie, policier, prêt, prêter, recommandation livres, reseau, roman, science fiction, thriller, tourisme, vente livre, vin, voyage, derniers ajouts, ajout récent";

        $this->view->action = $this->view->url(array(), 'lastAddedBooks');
        
        $this->render("list");
    }

    public function blowOfHeartsAction() {

        // Get all books
        $books = BookSvc::getInstance()->getBOHPageBOH();

        // Get tags for combo
        $this->view->tags = TagSvc::getInstance()->getTagsForBooks($books);

        $books = $this->filterBooks($books);
        $this->setPageList($books);

        $description = __("Cette sélection des coups de coeur est le résultat d'un classement effectué sur tous les livres présents chez Cherbouquin sur la base des coups de coeur que vous et les autres membres avez attribués. L'idée de ce top est que vous puissiez y trouver l'inspiration pour vos prochaines lectures.", "s1b");
        $title = __("Coups de coeurs", "s1b");

        $this->view->tagTitle = sprintf(__("%s - %s", "s1b"), Sb\Entity\Constants::SITENAME, $title);
        $this->view->title = $title;

        $this->view->metaDescription = $description;
        $this->view->description = $description;

        $this->view->metaKeywords = "cher bouquin, cherbouquin, achat, acheter, art, atlas, auteur, avis, bande dessinee, bandes dessinées, bd, bibliotheque, bibliotheque en ligne, commentaires, communaute, communauté de lecteurs, contes, critiques, critiques de livres, cuisine, dictionnaire, ecrivain, editeur, emprunt, emprunter, fantasy, histoire, lecture, lire, littérature, livre, livre ancien, livre enfant, livre jeunesse, livre occasion, livre photo, livre scolaire, livres en ligne, logiciel gestion bibliotheque, manga, notes, notice, partage, philosophie, poesie, policier, prêt, prêter, recommandation livres, reseau, roman, science fiction, thriller, tourisme, vente livre, vin, voyage, coup de coeur, favoris";

        $this->view->action = $this->view->url(array(), 'blowOfHeartsBooks');
        
        $this->render("list");
    }

    /**
     * Action for showing a list of top books
     */
    public function topsAction() {

        // Get all books
        $books = BookSvc::getInstance()->getTopsPageTops();

        // Get tags for combo
        $this->view->tags = TagSvc::getInstance()->getTagsForBooks($books);

        $books = $this->filterBooks($books);
        $this->setPageList($books);

        $description = __("Cette sélection des meilleurs livres est le résultat d'un classement effectué sur tous les livres présents chez Cherbouquin sur la base de la note que vous et les autres membres avez attribuée. L'idée de ce top est que vous puissiez y trouver l'inspiration pour vos prochaines lectures.", "s1b");
        $title = __("Tops des livres", "s1b");

        $this->view->tagTitle = sprintf(__("%s - %s", "s1b"), Sb\Entity\Constants::SITENAME, $title);
        $this->view->title = $title;

        $this->view->metaDescription = $description;
        $this->view->description = $description;

        $this->view->metaKeywords = "cher bouquin, cherbouquin, achat, acheter, art, atlas, auteur, avis, bande dessinee, bandes dessinées, bd, bibliotheque, bibliotheque en ligne, commentaires, communaute, communauté de lecteurs, contes, critiques, critiques de livres, cuisine, dictionnaire, ecrivain, editeur, emprunt, emprunter, fantasy, histoire, lecture, lire, littérature, livre, livre ancien, livre enfant, livre jeunesse, livre occasion, livre photo, livre scolaire, livres en ligne, logiciel gestion bibliotheque, manga, notes, notice, partage, philosophie, poesie, policier, prêt, prêter, recommandation livres, reseau, roman, science fiction, thriller, tourisme, vente livre, vin, voyage, top des livres, meileurs classements";

        $this->view->action = $this->view->url(array(), 'topsBooks');

        $this->render("list");
    }

    private function filterBooks($books) {
        $result = $books;
        $tid = $this->_getParam('tid', -1);
        if ($tid > 0) {
            $this->view->selectedTagId = $tid;
            $this->selectedTagId = $tid;
            $result = array_filter($books, array(&$this, "bookHasSelectedTag"));
        }
        return $result;
    }

    private function setPageList($books) {
        if ($books && count($books) > 0) {
            $paginatedList = new \Sb\Lists\PaginatedList($books, 10);
            $this->view->firstItemIdx = $paginatedList->getFirstPage();
            $this->view->lastItemIdx = $paginatedList->getLastPage();
            $this->view->nbItemsTot = $paginatedList->getTotalPages();
            $this->view->navigation = $paginatedList->getNavigationBar();
            $books = $paginatedList->getItems();
            $this->view->books = $books;
        }
    }

    private function bookHasSelectedTag(\Sb\Db\Model\Book $book) {
        $bookTags = TagSvc::getInstance()->getTagsForBooks(array($book));

        foreach ($bookTags as $tag) {
            if ($tag->getId() == $this->selectedTagId)
                return true;
        }
        return false;
    }

}