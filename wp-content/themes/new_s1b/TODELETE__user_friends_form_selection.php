<?php
require_once 'includes/init.php';
get_header();
require_once 'user_friends_form_selection_1.php';

use Sb\View\Components\FriendsWidget;
use Sb\View\Components\Ad;
use Sb\Helpers\StringHelper;
use Sb\Helpers\UserHelper;

/**
 * Template Name: user_friends_form_selection
 */
?>
<script language="JavaScript">
    function cocherOuDecocherTout(cochePrincipale) {
        var coches = document.getElementsByTagName('input');
        for(var i = 0 ; i < coches.length ; i++) {
            var c = coches[i];
            if(c.type.toUpperCase() == 'CHECKBOX' & c != cochePrincipale) {
                c.checked = cochePrincipale.checked;
            }
        }
        return true;
    }
</script>
<div id="content-center">
    <div class="friend-selection">
        <div class="fs-title"><?php _e("Choisir un destinataire","");?></div>            
        <form class="friends_list_selection_form" action="<?php echo $_SERVER["HTTP_REFERER"];?>" method="post">
            <input type="hidden" name="selection" value="selection"/>
            <div class="fs-list">
                <?php foreach ($friends as $friend) { ?>
                    <div class="friend-item">
                        <div class="inner-padding">
                            <img class="image-frame image-thumb-square-medium" src="<?php echo $friend->getGravatar();?>" />
                            <div class="fs-friendname">
                                <input type="checkbox" name="Friends[]" value="<?php echo $friend->getId(); ?>">
                                <?php echo StringHelper::tronque(UserHelper::getFullName($friend), 25); ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>                    
            </div>
            <div class="horizontal-sep-1"></div>
            <div class="fs-all">
                <input name="tout" type="checkbox" onclick="return cocherOuDecocherTout(this);"/>
                <span><?php _e("sélectionner tous vos contacts", "s1b"); ?></span>
            </div>
            <div class="buttons-bar">
                <div class="inner-padding">   
                    <button class="button bt-blue-l float-right margin-left"><?php _e("Sélectionner","s1b");?></button>
                    <a class="button bt-black-xs float-right" href="javascript:history.back()" class="link"><?php _e("Annuler", "s1b") ?></a>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="content-right">
    <div class="right-frame">
        <?php
        $friendWidget = new FriendsWidget;
        echo $friendWidget->get();
        ?>
    </div>
    <div class="right-frame">
    <?php
        $ad = new Ad("user_friends", "2432422854");
        echo $ad->get();
    ?>
    </div>
</div>
<?php get_footer(); ?>