<?php

use \Sb\Helpers\HTTPHelper;
use \Sb\Trace\Trace;

Trace::addItem(\Sb\Entity\LibraryPages::USERBOOK_EDIT);

global $s1b;
$context = $s1b->getContext();

if ($context->getIsShowingFriendLibrary()) {
    Throw new \Sb\Exception\UserException(__("Vous ne pouvez pas éditer le livre d'un ami.", "s1b"));
}

if (!$s1b->getIsSubmit()) {

    $idUserBook = $_GET['ubid'];
    $userBook = \Sb\Db\Dao\UserBookDao::getInstance()->get($idUserBook);

    if ($userBook) {
        // On vérifit la correspondance du user
        $s1b->compareWithConnectedUserId($userBook->getUser()->getId());
        showBook($userBook);
    } else {
        \Sb\Flash\Flash::addItem(__("Le livre que vous souhaitez éditer n'existe pas.", "s1b"));
        HTTPHelper::redirectToLibrary();
    }
} else {

    // getting form data
    $userBookForm = new Sb\Form\UserBook($_POST);
   
    // getting userbook in DB
    $userBook = \Sb\Db\Dao\UserBookDao::getInstance()->get($userBookForm->getId());

    // Getting the events related to the userbook changes
    $userEvents = \Sb\Db\Service\UserEventSvc::getInstance()->prepareUserBookEvents($userBook, $userBookForm);

    // On vérifit la correspondance du user
    $s1b->compareWithConnectedUserId($userBook->getUser()->getId());

    // updating userbook members
    $userBook->setReview($userBookForm->getReview());
    $userBook->setIsBlowOfHeart($userBookForm->getIsBlowOfHeart());
    $userBook->setIsOwned($userBookForm->getIsOwned());
    $userBook->setIsWished($userBookForm->getIsWished());
    $userBook->setRating($userBookForm->getRating());
    $userBook->setNb_of_pages($userBookForm->getNb_of_pages());
    $userBook->setNb_of_pages_read($userBookForm->getNb_of_pages_read());
    
    $readingState = \Sb\Db\Dao\ReadingStateDao::getInstance()->get($userBookForm->getReadingStateId());
    if ($userBookForm->getReadingDate())
        $userBook->setReadingDate($userBookForm->getReadingDate());
    $userBook->setReadingState($readingState);
    $userBook->setHyperlink($userBookForm->getHyperLink());

    if ($userBookForm->getTags()) {
        $tags = new \Doctrine\Common\Collections\ArrayCollection();
        foreach ($userBookForm->getTags() as $tagId) {
            $tag = \Sb\Db\Dao\TagDao::getInstance()->get($tagId);
            $tags->add($tag);
        }
        $userBook->setTags($tags);
    }

    // Mise à jour du UserBook
    if (\Sb\Db\Dao\UserBookDao::getInstance()->update($userBook)) {

        // persisting the userevent related to the userbook changes
        \Sb\Db\Service\UserEventSvc::getInstance()->persistAll($userEvents);

        \Sb\Flash\Flash::addItem(sprintf(__('Le livre "%s" a été mis à jour.', "s1b"), urldecode($userBook->getBook()->getTitle())));
        $referer = Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, "referer", null);
        if ($referer)
            HTTPHelper::redirectToUrl($referer);
        else
            HTTPHelper::redirectToLibrary();
    }
}

//////////////////////////////////////////////////////
function showBook(\Sb\Db\Model\UserBook $userBook) {

    // nous aurons besoin d'un objet Book et d'un objet UserBook pour les vues
    $book = new \Sb\Db\Model\Book();
    $book = $userBook->getBook();

    $tpl = new \Sb\Templates\Template("userBook");

    $tpl->set("action", "");

    $bookView = new \Sb\View\Book($book, false, false, false);
    $tpl->set("book", $bookView->get());

    $userBookView = new \Sb\View\UserBook($userBook, false);
    $tpl->set("bookForm", $userBookView->get());

    $buttonsBar = new \Sb\View\Components\ButtonsBar(true, __("Mettre à jour", "s1b"));
    $tpl->set("buttonsBar", $buttonsBar->get());


    $referer = HTTPHelper::getReferer();
    $tpl->setVariables(array("referer" => $referer));

    echo $tpl->output();
}