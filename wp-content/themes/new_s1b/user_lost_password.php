<?php
$noAuthentification = true;
require_once 'includes/init.php';
get_header();

/*
 * Template Name: user_lost_password
 */

function gen_reg_key() {
    $key = "";   /* on initialise la variable $key à "vide" */
    $max_length_reg_key = 8;   /* on définit la taille de la chaine (8 caractères ca suffit ) */

    /* on définit le type de caractères ascii de la chaine */
    $chaine = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l",
        "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x",
        "y", "z", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0");

    $count = count($chaine) - 1;
    srand((double) microtime() * 1000000);  /* on initialise la fonction rand pour le tirage aléatoire */
    for ($i = 0; $i < $max_length_reg_key; $i++)
        $key .= $chaine[rand(0, $count)]; /* on tire aléatoirement les $max_length_reg_key carac de la chaine */
    return($key);  /* on renvois la clé générée */
    /* Fin de le génération de clé */
}
if ($_POST) {
    $email = htmlspecialchars($_POST['lostpassword-email']);
    if ($email) {

        $user = \Sb\Db\Dao\UserDao::getInstance()->getByEmail($email);
        if ($user) {
            $new_pass = gen_reg_key();
            $new_pass_md5 = sha1($new_pass);
            $user->setPassword($new_pass_md5);
            // update password in db
            \Sb\Db\Dao\UserDao::getInstance()->update($user);
            // send email with new password
            $body = \Sb\Helpers\MailHelper::newPasswordBody($new_pass);
            \Sb\Mail\Service\MailSvcImpl::getInstance()->send($user->getEmail(), __("Votre nouveau mot de passe", "s1b") . " " . \Sb\Entity\Constants::SITENAME, $body);
            \Sb\Flash\Flash::addItem(__("Votre mot de passe a été mis à jour et un email vous a été envoyé.","s1b"));   
        } else {
            \Sb\Flash\Flash::addItem(__("Nous n'avons pas trouvé de compte correspondant à l'email saisi.","s1b"));   
        }
    } else {
        \Sb\Flash\Flash::addItem(__("Vous devez renseigner un email.","s1b"));
    }
}
?>
<?php showFlashes(); ?>
<div id="content-wrap">
    <div id="content-wide">
        <div class="inner-padding-10">
            <div class="lost-password">
                <form method="post" action="">
                    <div class="lp-title">               
                        <?php _e("Mot de passe oublié?", "s1b");?>
                    </div>
                    <div class="lp-subtitle">                
                        <?php _e("Indiquez l’e-mail que vous avez utilisé pour vous inscrire et nous vous enverrons un nouveau mot de passe à cette adresse","s1b")?>
                    </div>
                    <div class="lp-line">
                        <div class="lp-label"><?php _e("Entrez votre email","s1b"); ?></div>
                        <div class="lp-field"><input name="lostpassword-email" class="textinput input-item" type ="text"/></div>
                    </div>                
                    <div class="buttons-bar">
                        <div class="inner-padding">
                            <button class="float-right button bt-black-m margin-right margin-left"><?php _e("envoyer", "s1b"); ?></button>
                            <?php if (!$_POST) {?>
                            <a class="button bt-blue-xs float-right" href="javascript:history.back()" class="link"><?php _e("Annuler", "s1b") ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php get_footer(); ?>