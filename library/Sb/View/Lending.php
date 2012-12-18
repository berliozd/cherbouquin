<?php

namespace Sb\View;

class Lending extends \Sb\View\AbstractView {

    private $userBook;
    private $connectedUserId;

    function __construct(\Sb\Db\Model\UserBook $userBook, $userId) {
        parent::__construct();
        $this->userBook = $userBook;
        $this->connectedUserId = $userId;
    }

    public function get() {

        $tpl = new \Sb\Templates\Template("book/bookForm/lending");

        // display lendings histo
        if ($this->userBook->getLendings()) {
            $lendingView = new \Sb\View\LendingsHisto($this->userBook->getLendings(), $this->userBook, $this->connectedUserId);
            $tpl->set("lendingsHisto", $lendingView->get());
        } else {
            $tpl->set("lendingsHisto", "");
        }

        // display borrowings histo
        if ($this->userBook->getBorrowings()) {
            $borrowingView = new \Sb\View\BorrowingsHisto($this->userBook->getBorrowings(), $this->userBook, $this->connectedUserId);
            $tpl->set("borrowingsHisto", $borrowingView->get());
        } else {
            $tpl->set("borrowingsHisto", $borrowingView->get());
        }

        // display form
        $lendingForm = new \Sb\View\LendingForm($this->userBook->getLendings(), $this->userBook->getBorrowings(), $this->userBook, $this->connectedUserId);
        $tpl->set("lendingForm", $lendingForm->get());

        return $tpl->output();

    }
}