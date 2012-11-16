<?php
require_once 'includes/init.php';
get_header();
require_once 'user_recommandation_facebook_1.php';
/**
 * Template Name: user_recommandation_facebook
 */
?>
<div id="content-center">
    <div class="recommand">
        <?php if ($bookNotOwned) { ?>
        <div class="recommand-error">
            <?php echo __("Vous ne possédez pas ce livre", "s1b"); ?>
            <br/>
            <a href="<?php echo $bookLink;?>"><?php echo sprintf(__("Ajouter %s à votre bibliothèque", $book->getTitle()));?></a>
        </div>
        <?php } else { ?>
        <form action="" method="post" >
            <input type="hidden" name="go" value="go" />
            <div class="mailbox-new-message">
                <div class="mnm-title"><?php _e("Partager ce livre sur facebook","s1b");?></div>
                <?php 
                $bookView = new \Sb\View\PushedBook($book, false);
                echo $bookView->get();
                ?>
                <div class="mnm-line">
                    <div class="mnm-line-title"><?php _e("Message *", "s1b"); ?></div>
                    <textarea  class="input-item mnm-body" name="post_message" ></textarea>
                </div>
                <div class="buttons-bar">
                    <div class="inner-padding">   
                        <?php _e("Champs obligatoires *","");?>
                        <button class="button bt-blue-m float-right margin-left"><?php _e("Envoyer","s1b");?></button>
                        <a class="button bt-black-xs float-right" href="javascript:history.back()" class="link"><?php _e("Annuler", "s1b") ?></a>
                    </div>
                </div>
            </div>                        
        </form>
    <?php } ?>
    </div>
</div>
<div id="content-right">
    <?php
    $userToolBox = new \Sb\View\Components\UserToolBox;
    echo $userToolBox->get();
    ?>
</div>
<?php get_footer(); ?>