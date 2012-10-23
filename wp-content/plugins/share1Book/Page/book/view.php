<?php

global $s1b;
$context = $s1b->getContext();

$id = null;
if (array_key_exists("bid", $_GET)) {
    $id = $_GET["bid"];
    if ($id) {
        $book = \Sb\Db\Dao\BookDao::getInstance()->get($id);
        if ($book) {

            $bookView = new \Sb\View\Book($book, true, true, true, false);
            $buttonsBar = new \Sb\View\Components\ButtonsBar(false);
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