<?php
require_once 'includes/init.php';
get_header();
require_once 'user_message_answer_1.php';

/**
 * Template Name: user_message_answer
 */
?>
<div id="content-center">
    <div class="mailbox-message">
        <form action="" method="post" >            
            <div class="mm-header">
                <div class="inner-padding">
                    <div class="mmh-from">
                        <span class="mmh-label"><?php _e("De", "s1b"); ?></span>
                        <?php echo $message->getRecipient()->getUserName(); ?>                                
                    </div>
                    <div class="mmh-date">
                        <span class="mmh-label"><?php _e("A", "s1b"); ?></span>
                        <?php echo $message->getSender()->getUserName(); ?>
                        <input type="hidden" name="Addressee" readonly="readonly" value="<?php echo $message->getSender()->getUserName(); ?>"/>
                    </div>
                    <div class="mmh-subject">
                        <span class="mmh-label"><?php _e("Sujet", "s1b"); ?></span>
                        <?php echo 'RE: ' . $message->getTitle(); ?>
                        <input type="hidden" name="Title" readonly="readonly" value="<?php echo 'RE: ' . $message->getTitle(); ?>"/>
                    </div>
                </div>
            </div>                     
            <div class="mm-body">
                <div class="mmb-title"><?php _e("Message","s1b");?></div>
                <textarea class="input-item mm-inputbody" name="Message" ><?php if (isset($_POST['Message'])) echo $_POST['Message']; ?></textarea>
            </div>                
            <div class="buttons-bar">
                <div class="inner-padding">
                    <button class="button bt-black-m float-right margin-left"><?php _e("Envoyer","s1b");?></button>
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