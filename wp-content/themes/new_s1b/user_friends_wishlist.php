<?php
require_once 'includes/init.php';
get_header();
require_once 'user_friends_wishlist_1.php';

use Sb\Helpers\HTTPHelper;
use Sb\Entity\Urls;
use Sb\Helpers\BookHelper;

/**
 * Template Name: user_friends_wishlist
 */
?>
<div id="content-wide">
    <div class="friends-list-header">
        <?php
        $friendsPageNavigation = new \Sb\View\Components\FriendsPageNavigation("wishes");
        echo $friendsPageNavigation->get();
        ?>
        <div class="fl-search">
            <div class="inner-padding">
                <div class="fls-label"><?php _e("Sélectionner un ami pour accéder à sa la liste.", "s1b"); ?></div>
                <div class="fls-form">
                    <form action="" name="friendSelectionForm" method="get">                            
                        <select class="input-item selectinput fls-select" onchange="friendSelectionForm.submit()" name="friendId">
                            <option value=""><?php _e("Aucun", "s1b"); ?></option>
                            <?php foreach ($friends as $friend) { ?>
                                <option <?php echo (($selectedFriend && ($friend->getId() == $selectedFriend->getId())) ? "selected" : ""); ?> value="<?php echo $friend->getId(); ?>"><?php echo $friend->getFriendlyName(); ?></option>
                            <?php } ?>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="content-center">
    <div class="friends-wished-books">
        <?php if ($selectedFriend) { ?>
            <?php if ($friendWishedBooks) { ?>
                <div class="fwb-title"><?php _e("Offrez un livre à vos amis", "s1b"); ?></div>
                <div class="fwb-description"><?php _e("Choisissez le livre que vous souhaitez offrir à un ami dans sa liste d'envie, puis marquez le livre afin d'éviter que d'autres personnes n'offrent le même.", "s1b"); ?></div>
                <div class="fwb-mail">
                    <div class="fwb-mail-description"><?php _e("Vous pouvez également partager cette liste ou l'envoyer par email.", "s1b");?></div>
                    <form action="<?php echo HTTPHelper::Link(Urls::USERBOOK_GIFTS_SEND_BY_EMAIL) ?>" method="post">
                        <input type="hidden" name="uid" value="<?php echo $selectedFriend->getId();?>"/>                    
                        <div class="fwb-line">
                            <div class="fwb-line-title"><?php _e("Emails (séparés par une virgule) *", "s1b"); ?></div>
                            <input type="text" class="input-item mnm-emails" name="emails" maxlength="250" value="<?php echo $_GET["emails"];?>"/>
                            <button class="button bt-blue-m float-right margin-left"><?php _e("Envoyer", "s1b"); ?></button>
                        </div>                    
                    </form>
                </div>
                <div class="horizontal-sep-1"></div>
                <div class="friend-title"><?php echo sprintf(__("Livres souhaités par %s", "s1b"), $selectedFriend->getFriendlyName()); ?></div>
                <div class="friend-books">
                    <?php
                    foreach ($friendWishedBooks as $friendUserBook) {
                        if ($friendUserBook->getIsWished()) {
                            $wishedBook = new \Sb\View\WishedUserBook($friendUserBook);
                            echo $wishedBook->get();
                            ?>
                            <div class="horizontal-sep-1"></div>
                            <?php
                        }
                    }
                    ?>
                </div>
            <?php } else { ?>
                <div class="fl-friendnobooks"><?php _e("Cet ami n'a aucun livre souhaité.", "s1b"); ?> </div>
            <?php } ?>
            <?php if ($booksHeCouldLikes && count($booksHeCouldLikes) > 0) { ?>
            <div class="pushed-books pushedBooks">
                <div class="pb-title">
                    <?php _e("Idées cadeaux... ça pourrait lui plaire","s1b"); ?>
                </div>
                <div class="pb-shelf">
                    <div class="inner-side-padding-30">
                    <?php foreach ($booksHeCouldLikes as $bookHeCouldLikes) { ?>
                        <div class="pb-bookOnShelf">
                            <a href="<?php echo HTTPHelper::Link($bookHeCouldLikes->getLink()); ?>"><?php echo BookHelper::getMediumImageTag($bookHeCouldLikes, $context->getDefaultImage());?></a>
                        </div>
                    <?php }?>
                    </div>
                </div>
            </div>
            <div class="horizontal-sep-1"></div>
            <?php } ?>
            
        <?php } elseif (!$selectedFriend) { ?>
            <div class="fwb-title"><?php _e("Merci de sélectionner un ami dans la liste.", "s1b"); ?> </div>
            <img class="fwb-autopromo-banner" src="<?php echo $context->getBaseUrl() . "/Resources/images/wishlist-autopromo-banner.png"?>" />
            <div class="fwb-subtitle"><?php _e("Offrez et faites vous offrir des cadeaux qui plaisent vraiment en utilisant la liste d'envies.", "s1b"); ?> </div>
            <div class="fwb-description"><?php _e("Accédez à la liste d'envies d'un ami en le sélectionnant dans le liste déroulante placée ci-dessus. Pour que vos amis puissent suivre vos envies marquez les livres que vous souhaitez dans votre fiche de lecture au moment de l'ajout dans votre bibliothèque.", "s1b"); ?></div>
            <div class="fwb-description"><strong><?php _e("Sous la partie commentaire, cochez la case \"vous souhaitez ce livre\"", "s1b"); ?></strong></div>
        <?php } ?>
    </div>
</div>
<div id="content-right">
    <div class="right-frame">
        <?php
        $userToolBox = new \Sb\View\Components\UserToolBox;
        echo $userToolBox->get();
        ?>
    </div>
    <div class="right-frame">
        <?php
        $ad = new \Sb\View\Components\Ad("user_friends", "2432422854");
        echo $ad->get();
        ?>
    </div>
</div>
<?php get_footer(); ?>
