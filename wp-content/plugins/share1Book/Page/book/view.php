<?php

use \Sb\Db\Dao\BookDao;
use \Sb\View\Book;
use \Sb\View\Components\ButtonsBar;

global $s1b;

$redirect = false;

$id = null;
if (array_key_exists("bid", $_GET)) {
    $id = $_GET["bid"];
    if ($id) {
        $id = str_replace("/", "", $id);
        $book = BookDao::getInstance()->get($id);
        if ($book) {
            
            $bookView = new Book($book, true, true, true, false, true);
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