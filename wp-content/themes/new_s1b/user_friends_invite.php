<?php
require_once 'includes/init.php';
get_header();
require_once 'user_friends_invite_1.php';
/**
 * Template Name: user_friends_invite
 */
?>
<div id="content-center">
    <form action="" method="post" >
        <div class="mailbox-new-message">
            <div class="mnm-title"><?php _e("Envoyer une invitation", "s1b"); ?></div>
            <div class="mnm-line">
                <div class="mnm-line-title"><?php _e("Emails (séparés par une virgule) *", "s1b"); ?></div>
                <input type="text" class="input-item mnm-emails" name="Emails" maxlength="250" value="<?php echo $_POST["Emails"];?>"/>
            </div>
            <div class="mnm-line">
                <div class="mnm-line-title"><?php _e("Message *", "s1b"); ?></div>                    
                <textarea class="input-item mnm-body mnm-body" name="Message" maxlength="500"><?php echo sprintf(__("%s %s vous invite à rejoindre <a href=\"%s\">%s</a>, réseau communautaire autour du livre et de la lecture.\n\nVenez échanger sur vos lectures et coups de coeur, chercher l'inspiration grâce aux recommandations, gérer et partager votre bibliothèque avec vos amis et trouver dans leurs listes d’envies des idées de cadeaux.\n\nL’inscription est gratuite ! Rejoignez-nous..."), $user->getFirstName(), $user->getLastName(), $_SERVER["SERVER_NAME"], $_SERVER["SERVER_NAME"]); ?>
                </textarea>
            </div>
            <div class="buttons-bar">
                <div class="inner-padding">   
                    <?php _e("Champs obligatoires *", ""); ?>
                    <button class="button bt-blue-m float-right margin-left"><?php _e("Envoyer", "s1b"); ?></button>
                    <a class="button bt-black-xs float-right" href="javascript:history.back()" class="link"><?php _e("Annuler", "s1b") ?></a>
                </div>
            </div>
        </div>            
    </form>
</div>
<div id="content-right">
    <?php
    $userToolBox = new \Sb\View\Components\UserToolBox;
    echo $userToolBox->get();
    ?>
</div>
<?php get_footer(); ?>