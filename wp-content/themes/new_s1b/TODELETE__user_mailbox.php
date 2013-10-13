<?php
require_once 'includes/init.php';
get_header();
require_once 'user_mailbox_1.php';

use Sb\View\Components\MailboxWidget;
use Sb\View\Components\Ad;
use Sb\Helpers\UserHelper;

/**
 * Template Name: user_mailbox
 */
/* * **********************************************************************************
 * Functions
 * *********************************************************************************** */
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
    <div class="mailbox">        
        <form method="post" action="">
            <div class="navigation mb-hat">                
                <div class="inner-padding">
                    <?php if (count($messages) != 0) {?>
                    <div class="nav-links"><?php echo $navigation;;?></div>                    
                    <div class="nav-resume"><?php echo sprintf(__("Message(s) %s à %s sur %s","s1b"), $firstItemIdx, $lastItemIdx, $nbItemsTot) ;?></div>
                    <?php }?>
                </div>
            </div>
            <table border="0" cellpadding="0" cellspacing="0">
                <tr class="mb-header">
                    <th class="mb-col-check">
                        <div class="inner-padding">
                            <input name="tout" type="checkbox" onclick="return cocherOuDecocherTout(this);"/></th>
                        </div>
                    <th class="mb-col-avatar"></th>
                    <th class="mb-col-msg">
                        <div class="inner-padding"><?php _e("Messages", "s1b"); ?></div>
                    </th>
                    <th class="mb-col-date <?php echo $dateCSSClass;?>">
                        <div class="inner-padding">
                            <?php $sortVal = (\Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, "sortby", "DESC") == "DESC" ? "ASC"  : "DESC");?>
                            <a href="?sortby=<?php echo $sortVal;?>"><?php _e("Date", "s1b"); ?></a>
                        </div>
                    </th>
                    <th class="mb-col-state">
                        <div class="inner-padding">
                            <?php _e("Etat", "s1b"); ?>
                        </div>
                    </th>
                    <th class="mb-col-read"></th>
                    <th class="mb-col-delete"></th>
                </tr>
            <?php
            if (count($messages) != 0) {
            $i = 0;
            foreach ($messages as $message) {
                $i += 1;
                $senderProfileLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_PROFILE, array("uid" => $message->getSender()->getId()));
                $readMessageLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_MAILBOX_READ_MESSAGE, array("mid" => $message->getId()));
                $deleteMessageLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_MAILBOX_DELETE_MESSAGE, array("mid" => $message->getId()));
                ?>
                <?php if ($i != 1) {?>
                <tr>
                    <td colspan="6">
                        <div class="horizontal-sep-1"></div>
                    </td>
                </tr>
                <?php } else {?>
                <tr>
                    <td colspan="6">
                        <div class="margin-top-l"></div>
                    </td>
                </tr>
                <?php }?>
                <tr>
                    <td class="mb-col-check">
                        <div class="inner-padding">
                            <input type="checkbox" name="delete[]" value="<?php echo $message->getId(); ?>" />
                        </div>
                    </td>                    
                    <td class="mb-col-avatar">
                        <div class="inner-padding">
                            <?php echo '<a href=' . $senderProfileLink . '><img class="image-frame image-thumb-square-small" src="' . $message->getSender()->getGravatar() . '" /></a>';?>
                        </div>
                    </td>
                    <td class="mb-col-msg border-right">
                        <div class="inner-padding">
                            <?php echo sprintf(__("<strong>De</strong> <span class=\"highlight\">%s</span>","s1b"), UserHelper::getFullName($message->getSender()));?>
                            <br/>
                            <a href="<?php echo $readMessageLink?>"><?php echo \Sb\Helpers\StringHelper::tronque($message->getTitle(), 75);?></a>
                        </div>
                    </td>
                    <td class="mb-col-date border-right">
                        <div class="inner-padding">
                            <?php echo $message->getDate()->format('d/m/Y'); ?>
                        </div>
                    </td>
                    <td class="mb-col-state border-right">
                        <div class="inner-padding">
                            <?php ($message->getIs_read()? _e("Lu","s1b") : _e("Non lu","s1b")); ?>
                        </div>
                    </td>
                    <td class="mb-col-read">
                        <div class="inner-padding">
                            <a class="link" href="<?php echo $readMessageLink?>"><?php _e("Lire", "s1b");?></a>
                        </div>
                    </td>
                    <td class="mb-col-delete">
                        <div class="inner-padding">
                            <a class="bt-delete float-right" href="<?php echo $deleteMessageLink; ?>" onClick="return(confirm('<?php _e("confirmez-vous la suppression?", "s1b"); ?>'));"></a>
                        </div>
                    </td>
                </tr>
            <?php }
            } else {?>
                <tr>
                    <td colspan="6">
                        <div class=""><?php _e("Pas de messages","s1b");?></div>
                    </td>
                </tr>                    
            <?php }?>
                <tr>
                    <td colspan="6">
                        <div class="margin-top-l"></div>
                    </td>
                </tr>
            </table>
            <div class="navigation mb-foot">                
                <div class="inner-padding">
                    <div class="mb-deletebutton">
                        <button class="button bt-black-l"><?php _e("Supprimer les messages", "s1b"); ?></button>
                    </div>
                    <div class="nav-links"><?php echo $navigation;?></div>
                </div>
            </div>            
        </form>

    </div>
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


