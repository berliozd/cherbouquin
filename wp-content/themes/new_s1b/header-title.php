<title>
    <?php
    $defaultTitle = "Cherbouquin - gérez et partagez votre bibliothèque avec vos amis, offrez leurs le bon livre et découvrez les coups de coeur de la communauté de lecteurs";
    $bookId = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, "bid", null);
    $book = null;
    if ($bookId)
        $book = \Sb\Db\Dao\BookDao::getInstance()->get($bookId);

    if ($book)
        echo $book->getTitle() . " - " . $book->getOrderableContributors();
    else echo $defaultTitle;
    ?>
</title>