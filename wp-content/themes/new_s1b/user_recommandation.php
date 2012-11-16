<?php
require_once 'includes/init.php';
get_header();
require_once 'user_recommandation_1.php';
/**
 * Template Name: user_message_recommandation
 */
?>
<div id="content-center">           
    <div class="recommand">
        <?php if (!$userBook) {?>
        <div class="recommand-error">
            <?php
            echo __("Vous ne possédez pas ce livre", "s1b") . '<br/><a href=' . $bookLink  . '>' . __("Ajouter", "s1b") . " " . $book->getTitle() . " " . __("à votre bibliothèque", "s1b") . '</a>'; ?>
        </div>
        <?php } else { ?>
        <form action="" method="post" >
            <input type="hidden" name="go" value="go" />
            <input type="hidden" name="Title" readonly="readonly" value="<?php echo $user->getFirstName() . " " . __("vous recommande: ", "s1b") . $book->getTitle() . " " . __("de", "s1b") . " " . $book->getOrderableContributors(); ?>">
            <div class="mailbox-new-message">
                <div class="mnm-title"><?php _e("Partager ce livre","s1b");?></div>
                <?php 
                $bookView = new \Sb\View\PushedBook($book, false);
                echo $bookView->get();
                ?>
                <div class="mnm-line">
                    <span class="mnm-label"><?php _e("A", "s1b"); ?></span>
                    <?php
                    if (count($friendList) == 0) { ?>
                        <a class="link" href="<?php echo \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_FRIENDS_SELECTION); ?>"><?php _e("sélectionner le(s) destinataires(s)", "s1b"); ?></a>
                    <?php } else { ?>
                        <a class="link" href="<?php echo \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_FRIENDS_SELECTION); ?>"><?php _e("modifier le(s) destinataires(s)", "s1b"); ?></a>
                        <br/>
                        <input type="hidden" name="IdAddressee" value="<?php echo $friendIdList; ?>">
                        <?php
                            foreach ($friendList as $friend) {
                                echo $friend->getUserName() . ";";
                            }
                    } ?>                    
                </div>                                
                <div class="mnm-line">
                    <div class="mnm-line-title"><?php _e("Message *", "s1b"); ?></div>
                    <textarea  class="input-item mnm-body" name="Message" ><?php if (isset($_POST['Message'])) echo $_POST['Message']; ?></textarea>
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