<?php

namespace Sb\View;

/**
 * Description of UserBook
 *
 * @author Didier
 */
class LendingsHisto extends \Sb\View\AbstractView {

    private $lendings;
    private $book;
    private $userBook;
    private $connectedUserId;

    function __construct($lendings, $userBook, $connectedUserId) {
        parent::__construct();
        $this->lendings = $lendings;
        $this->connectedUserId = $connectedUserId;
        $this->userBook = $userBook;
        $this->book = $userBook->getBook();
    }

    public function get() {

        $baseTpl = "book/bookForm/lending";

        // Préparation de l'historique des prêts
        // ----------------------------------------------
        $activeLending = null;

        $lendingLines = array();
        foreach ($this->lendings as $lending) {
            // Si le prêt est encore actif, la ligne n'est pas affichée dans l'historique
            if (($lending->getState() != \Sb\Lending\Model\LendingState::IN_ACTIVE)) {
                $activeLending = $lending;
            } else {
                $lendingLineTpl = new \Sb\Templates\Template($baseTpl . "/inActiveLendingLine");
                $lendingLineTpl->set("borrowerName",
                        $lending->getBorrower_userbook()->getUser()->getFirstName() . " " . $lending->getBorrower_userbook()->getUser()->getLastName());
                $lendingLineTpl->set("startDate", $lending->getStartDate()->format(__("d/m/Y", "s1b")));
                $lendingLineTpl->set("endDate", $lending->getEndDate()->format(__("d/m/Y", "s1b")));
                $lendingLines[] = $lendingLineTpl;
            }
        }
        $lendingsHisto = new \Sb\Templates\Template($baseTpl . "/lendingsHisto");
        // Si les lignes d'historiques en sont pas nulles, affichage sous forme de liste
        if ($lendingLines) {
            $lendingLinesTpl = \Sb\Templates\Template::merge($lendingLines);
            $lendingsHisto->set("histo", "<ul>" . $lendingLinesTpl . "</ul>");
        } else { // Sinon affichage d'un libellé "vide" ou "aucun"
            $lendingsHisto->set("histo", __("aucun", "s1b"));
        }

        return $lendingsHisto->output();
    }

}