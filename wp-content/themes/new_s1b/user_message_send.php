<?php
require_once 'includes/init.php';

require_once 'user_message_send_1.php';

get_header();

use Sb\View\Components\MailboxWidget;
use Sb\View\Components\Ad;

/**
 * Template Name: user_message_send
 */
?>
<div id="content-center">
    <form action="" method="post" >
        <input type="hidden" name="go" value="go"/>
        <div class="mailbox-new-message">
            <div class="mnm-title"><?php _e("Ecrire un message","s1b");?></div>
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
                <div class="mnm-line-title"><?php _e("Sujet *", "s1b"); ?></div>
                <input type="text" class="input-item mnm-subject" name="Title" value="<?php if (isset($_POST['Title'])) echo $_POST['Title']; ?>"/>
            </div>
            <div class="mnm-line">
                <div class="mnm-line-title"><?php _e("Message *", "s1b"); ?></div>
                <textarea  class="input-item mnm-body mnm-body" name="Message" ><?php if (isset($_POST['Message'])) echo $_POST['Message']; ?></textarea>
            </div>
            <div class="buttons-bar">
                <div class="inner-padding">   
                    <?php _e("Champs obligatoires *","");?>
                    <button class="button bt-blue-l float-right margin-left"><?php _e("Envoyer","s1b");?></button>
                    <a class="button bt-black-xs float-right" href="javascript:history.back()" class="link"><?php _e("Annuler", "s1b") ?></a>
                </div>
            </div>
        </div>            
    </form>
</div>
<div id="content-right">    
    <div class="right-frame">
        <?php
        $mailboxWidget = new MailboxWidget();
        echo $mailboxWidget->get();
        ?>
    </div>
    <div class="right-frame">
        <?php
        $ad = new Ad("","");
        echo $ad->get();
        ?>
    </div>
</div>
<?php get_footer(); ?>