<?php

namespace Sb\View;

/**
 * Description of CurrentlyReadingBook
 *
 * @author Didier
 */
class CurrentlyReadingBook extends \Sb\View\AbstractView {

    private $userBook;
    private $mainPageName;
    private $showingConnectedUserBook;

    function __construct($mainPageName, $showingConnectedUserBook, \Sb\Db\Model\UserBook $userBook = null) {
        parent::__construct();
        if ($userBook) {
            $this->userBook = $userBook;
        }
        $this->mainPageName = $mainPageName;
        $this->showingConnectedUserBook = $showingConnectedUserBook;
    }

    public function get() {

        if ($this->userBook) {
            $pushedBookView = new \Sb\View\PushedUserBook($this->showingConnectedUserBook, $this->userBook);
            return $pushedBookView->get();
        } else {
            return "";
        }
        
    }
}