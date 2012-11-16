<?php
require_once 'includes/init.php';
get_header();

/**
 * Template Name: user_message_read
 */
$messageId = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, 'mid', null);
if ($messageId) {
    $message = \Sb\Db\Dao\MessageDao::getInstance()->get($messageId);
    if ($message->getRecipient()->getId() != $context->getConnectedUser()->getId()) {
        $redirect = true;
        \Sb\Flash\Flash::addItem(__("Le message que vous tentez de lire ne vous est pas destiné.", "s1b"));
    } else {
        $message->setIs_read(true);
        \Sb\Db\Dao\MessageDao::getInstance()->update($message);
    }
} else {
    $redirect = true;
    \Sb\Flash\Flash::addItem(__("Le message que vous tentez de lire n'existe pas.", "s1b"));
}

if ($redirect)
    \Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::USER_MAILBOX);
?>

<div id="content-center">
    <div class="mailbox-message">
        <div class="mm-header">
            <div class="inner-padding">
                <div class="mmh-from">
                    <span class="mmh-label"><?php _e("De", "s1b"); ?></span>
                    <?php echo $message->getSender()->getUserName(); ?>
                </div>
                <div class="mmh-date">              
                    <span class="mmh-label"><?php _e("Date : ","s1b");?></span>
                    <?php echo $message->getDate()->format('d/m/Y'); ?>
                </div>
                <div class="mmh-subject">
                    <span class="mmh-label"><?php _e("Sujet : ","s1b");?></span>
                    <?php echo $message->getTitle(); ?>
                </div>
            </div>
        </div>
        <div class="mm-body">
            <div class="mmb-title"><?php _e("Message","s1b");?></div>
            <?php echo $message->getMessage(); ?>
        </div>
        <div class="buttons-bar">
            <div class="inner-padding">
                <a class="button float-right bt-delete" href="<?php echo \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_MAILBOX_DELETE_MESSAGE,array("mid" => $message->getId())); ?>"></a>
                <a class="button float-right margin-right bt-blue-m" href="<?php echo \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_MAILBOX_REPLY_MESSAGE, array("mid" => $message->getId())); ?>"><?php _e("répondre", "s1b"); ?></a>                
            </div>
        </div>
    </div>
</div>
<div id="content-right">
    <?php
    $userToolBox = new \Sb\View\Components\UserToolBox;
    echo $userToolBox->get();
    ?>
</div>
<?php get_footer(); ?>