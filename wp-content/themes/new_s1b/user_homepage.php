<?php
require_once 'includes/init.php';
get_header();

/**
 * Template Name: user_homepage
 */
?>
<?php
$userNavigation = new \Sb\View\Components\UserNavigation;
echo $userNavigation->get();
?>
<?php showFlashes(); ?>
<div id="content-wrap">
    <div id="content-center">
        
        <?php 
        // Temp check  : to remove when new "a la page" is live
        if ($config->getIsProduction()) {?>
        
        <div class="pushed-books pushedBooks">
            <div class="pb-title">
                <?php _e("Vous lisez actuellement", "s1b"); ?>
            </div>
            <?php
            $currentlyReadingBook = \Sb\Db\Dao\UserBookDao::getInstance()->getReadingNow($context->getConnectedUser()->getId());
            if ($currentlyReadingBook) {
                $currentlyReadingBookView = new \Sb\View\CurrentlyReadingBook($config->getUserLibraryPageName(), true, $currentlyReadingBook);
                echo $currentlyReadingBookView->get();
            } else {
                $readingWhatform = new \Sb\View\Components\ReadingWhatForm();
                echo $readingWhatform->get();
            }
            ?>
        </div>
        <div class="horizontal-sep-1"></div>
        <div class="pushed-books pushedBooks">
            <div class="pb-title">
                <?php _e("Vos livres souhaités", "s1b"); ?>
            </div>
            <?php
            $allWishedBooks = \Sb\Db\Dao\UserBookDao::getInstance()->getListWishedBooks($context->getConnectedUser()->getId(), -1, true);
            $wishedBooks = array_slice($allWishedBooks, 0, 10);
            if (count($wishedBooks) == 0) {
                $noWishdBooksWidget = new \Sb\View\Components\NoBooksWidget(__("Vous ne souhaitez aucun livre.", "s1b"));
                echo $noWishdBooksWidget->get();
            } else {
                $wishedBooksView = new \Sb\View\PushedUserBooks($wishedBooks, $config->getUserLibraryPageName(), 3, false, false);
                echo $wishedBooksView->get();
            }
            ?>
        </div>
        <div class="horizontal-sep-1"></div>
        <?php } ?>        
        
        <div class="pushed-books pushedBooks">
            <div class="pb-title">
                <?php _e("Coups de coeur de vos amis", "s1b"); ?>
            </div>
            <?php
            $blowOfHeartFriendsBooks = \Sb\Db\Dao\BookDao::getInstance()->getListBOHFriends($context->getConnectedUser()->getId());
            if (count($blowOfHeartFriendsBooks) == 0) {
                $noBohForFriends = new \Sb\View\Components\NoBooksWidget(__("Vos amis n'ont pas encore ajouté de coups de coeur", "s1b"));
                echo $noBohForFriends->get();
            } else {
                $view = new \Sb\View\PushedBooks($blowOfHeartFriendsBooks, 3, true);
                echo $view->get();
            }
            ?>
        </div>
        <div class="horizontal-sep-1"></div>
        <div class="pushed-books pushedBooks">
            <div class="pb-title">
                <?php _e("Top 10 des membres", "s1b"); ?>
            </div>
            <?php
            $topsBooks = \Sb\Db\Dao\BookDao::getInstance()->getListTops();
            if (count($topsBooks) == 0) {
                $noTopBooks = new \Sb\View\Components\NoBooksWidget(__("Aucun livre n'a encore été noté par les membres", "s1b"));
                echo $noTopBooks->get();
            } else {
                $view = new \Sb\View\PushedBooks($topsBooks, 3, false);
                echo $view->get();
            }
            ?>
        </div>
    </div>
    <div id="content-right">
        <div class="right-frame">
            <?php
            $userToolBox = new \Sb\View\Components\UserToolBox(true, true);
            echo $userToolBox->get();
            ?>
        </div>
        <div class="right-frame">
            <?php
            $facebookFrame = new \Sb\View\Components\FacebookFrame();
            echo $facebookFrame->get();
            ?>
        </div>
        <div class="right-frame">
            <?php
            $ad = new \Sb\View\Components\Ad("user_homepage", "6697829998");
            echo $ad->get();
            ?>
        </div>  
    </div>
    <?php get_footer(); ?>