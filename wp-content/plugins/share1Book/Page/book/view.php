<?php

use \Sb\Db\Service\BookSvc;
use \Sb\Db\Dao\BookDao;
use \Sb\View\Book;
use \Sb\View\Components\ButtonsBar;
use \Sb\View\BookShelf;

global $s1b;
$context = $s1b->getContext();

$id = null;
if (array_key_exists("bid", $_GET)) {
    $id = $_GET["bid"];
    if ($id) {
        $id = str_replace("/", "", $id);
        $book = BookDao::getInstance()->get($id);
        if ($book) {

            $bookView = new Book($book, true, true, true, false);
            $buttonsBar = new ButtonsBar(false);
            
            echo $bookView->get(). $buttonsBar->get();

        } else {
            $redirect = true;
        }
    } else {
        $redirect = true;
    }
}
if ($redirect)
    \Sb\Helpers\HTTPHelper::redirectToHome();