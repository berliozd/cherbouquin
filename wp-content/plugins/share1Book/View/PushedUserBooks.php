<?php

namespace Sb\View;

class PushedUserBooks extends \Sb\View\AbstractView {

    private $userBooksExt;
    private $mainPageName;
    private $nbBooksShownByDefault;
    private $showingConnectedUserBook;

    function __construct($userBooksExt, $mainPageName, $nbBooksShownByDefault, $showingConnectedUserBook) {
        parent::__construct();
        $this->userBooksExt = $userBooksExt;
        $this->mainPageName = $mainPageName;
        $this->nbBooksShownByDefault = $nbBooksShownByDefault;
        $this->showingConnectedUserBook = $showingConnectedUserBook;
    }

    public function get() {

        if ($this->userBooksExt) {

            $templatesBooks = "";
            foreach ($this->userBooksExt as $userBookExt) {
                $pushedBookView = new \Sb\View\PushedUserBook($this->showingConnectedUserBook, $userBookExt);
                $templatesBooks .= $pushedBookView->get();
            }

            $tpl = new \Sb\Templates\Template("pushedBooks");
            $tpl->set("books", $templatesBooks);
            \Sb\Trace\Trace::addItem("Nb books in total : " . count($this->userBooksExt));
            \Sb\Trace\Trace::addItem("Nb books shown by default : " . $this->nbBooksShownByDefault);
            $nbBooks = count($this->userBooksExt);
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
