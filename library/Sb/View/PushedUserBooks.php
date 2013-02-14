<?php

namespace Sb\View;

class PushedUserBooks extends \Sb\View\AbstractView {

    private $userBooks;
    private $nbBooksShownByDefault;
    private $showingConnectedUserBook;

    function __construct($userBooks, $nbBooksShownByDefault, $showingConnectedUserBook) {
        parent::__construct();
        $this->userBooks = $userBooks;
        $this->nbBooksShownByDefault = $nbBooksShownByDefault;
        $this->showingConnectedUserBook = $showingConnectedUserBook;
    }

    public function get() {

        if ($this->userBooks) {

            $templatesBooks = "";
            foreach ($this->userBooks as $userBookExt) {
                $pushedBookView = new \Sb\View\PushedUserBook($this->showingConnectedUserBook, $userBookExt);
                $templatesBooks .= $pushedBookView->get();
            }

            $tpl = new \Sb\Templates\Template("pushedBooks");
            $tpl->set("books", $templatesBooks);
            \Sb\Trace\Trace::addItem("Nb books in total : " . count($this->userBooks));
            \Sb\Trace\Trace::addItem("Nb books shown by default : " . $this->nbBooksShownByDefault);
            $nbBooks = count($this->userBooks);
            if ($nbBooks <= $this->nbBooksShownByDefault) {
                $tpl->set("bottomLink", "");
            } else {
                $tplBottomLink = new \Sb\Templates\Template("components/seeMore");
                $tpl->set("bottomLink", $tplBottomLink->output());
            }

            return $tpl->output();
        }else
            return "";
    }

}
