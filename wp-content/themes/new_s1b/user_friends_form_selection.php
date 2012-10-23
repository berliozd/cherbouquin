<?php
require_once 'includes/init.php';
get_header();
require_once 'user_friends_form_selection_1.php';
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
<?php
$userNavigation = new \Sb\View\Components\UserNavigation;
echo $userNavigation->get();
?>
<?php showFlashes(); ?>
<div id="content-wrap">
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
                                    <?php echo \Sb\Helpers\StringHelper::tronque($friend->getUserName(), 25); ?>
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
                        <button class="button bt-black-m float-right margin-left"><?php _e("Sélectionner","s1b");?></button>
                        <a class="button bt-blue-xs float-right" href="javascript:history.back()" class="link"><?php _e("Annuler", "s1b") ?></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div id="content-right">
        <?php
        $userToolBox = new \Sb\View\Components\UserToolBox;
        echo $userToolBox->get();
        ?>
    </div>
<?php get_footer(); ?>