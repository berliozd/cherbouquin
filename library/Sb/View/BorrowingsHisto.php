<?php

namespace Sb\View;

/**
 * Description of UserBook
 *
 * @author Didier
 */
class BorrowingsHisto extends \Sb\View\AbstractView {

    private $borrowings;
    private $book;
    private $userBook;
    private $connectedUserId;

    function __construct($borrowings, $userBook, $connectedUserId) {
        parent::__construct();
        $this->borrowings = $borrowings;
        $this->connectedUserId = $connectedUserId;
        $this->userBook = $userBook;
        $this->book = $userBook->getBook();
    }

    public function get() {

        $baseTpl = "book/bookForm/lending";

        // Préparation de l'historique des emprunts
        // ----------------------------------------------
        $activeBorrowing = null;
        if ($this->borrowings) {
            $lendingLines = array();
            foreach ($this->borrowings as $borrowing) {
                // Si le prêt est encore actif, la ligne n'est pas affichée dans l'historique
                if (($borrowing->getState() != \Sb\Lending\Model\LendingState::IN_ACTIVE)) {
                    $activeBorrowing = $borrowing;
                } else {
                    $lendingLineTpl = new \Sb\Templates\Template($baseTpl . "/inActiveBorrowingLine");
                    if ($borrowing->getUserbook())
                        $lendingLineTpl->set("lenderName", $borrowing->getUserbook()->getUser()->getFirstName() . " " . $borrowing->getUserbook()->getUser()->getLastName());
                    else
                        $lendingLineTpl->set("lenderName", sprintf("%s (invité)", $borrowing->getGuest()->getName()));
                    $lendingLineTpl->set("startDate", $borrowing->getStartDate()->format(__("d/m/Y", "s1b")));
                    $lendingLineTpl->set("endDate", $borrowing->getEndDate()->format(__("d/m/Y", "s1b")));
                    $lendingLines[] = $lendingLineTpl;
                }
            }
            $lendingsHisto = new \Sb\Templates\Template($baseTpl . "/borrowingsHisto");
            // Si les lignes d'historiques en sont pas nulles, affichage sous forme de liste
            if ($lendingLines) {
                $lendingLinesTpl = \Sb\Templates\Template::merge($lendingLines);
                $lendingsHisto->set("histo", "<ul>" . $lendingLinesTpl . "</ul>");
            } else { // Sinon affichage d'un libellé "vide" ou "aucun"
                $lendingsHisto->set("histo", __("aucun", "s1b"));
            }

            return $lendingsHisto->output();
        } else {
            return __("Vous n'avez jamais prêté ce livre.", "s1b");
        }
    }

}