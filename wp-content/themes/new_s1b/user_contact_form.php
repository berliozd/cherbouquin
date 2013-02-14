<?php
$noAuthentification = true;
require_once 'includes/init.php';
get_header();
/*
  Template Name: Contact Form
 */
?>

<?php

function validateContactForm() {
    $name = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, "contactName", null);
    $firstName = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, "contactFirstName", null);
    $email = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, "email", null);
    $message = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, "comments", null);
    
    $ok = true;
    
    if (!$name) {
        \Sb\Flash\Flash::addItem(__("Indiquez votre nom", "s1b"));            
        $ok = false;
    }    
    if (!$firstName) {
        \Sb\Flash\Flash::addItem(__("Indiquez votre prénom", "s1b"));            
        $ok = false;
    }
    if (!$email) {
        \Sb\Flash\Flash::addItem(__("Indiquez une adresse mail valide", "s1b"));
        $ok = false;
    } elseif (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", $email)) {
        \Sb\Flash\Flash::addItem(__("Indiquez une adresse mail valide", "s1b"));
        $ok = false;
    }
    if (!$message) {
        \Sb\Flash\Flash::addItem(__("Le message est vide.", "s1b"));
        $ok = false;
    }
    return $ok;
}

if ($_POST) {

    $emailSent = false;

    if (validateContactForm()) {

        $name = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, "contactName", null);
        $firstName = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, "contactFirstName", null);
        $email = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, "email", null);
        $message = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, "comments", null);
        $sendCopy = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, "sendCopy", false);           

        $subject = sprintf(__("Formulaire de contact : %s %s", "s1b"), $name, $firstName);
        $body = sprintf(__("Nom : %s <br/>Prénom: %s <br/>Email : %s <br/>Message: %s <br/>", "s1b"), $name, $firstName, $email, $message);
        
        $mailSvc = \Sb\Service\MailSvc::getNewInstance(null, \Sb\Entity\Constants::CONTACT_EMAIL);        
        $mailSvc->send(\Sb\Entity\Constants::CONTACT_EMAIL . ", berliozd@gmail.com, rebiffe_olivier@yahoo.fr", $subject, $body);

        if ($sendCopy) {
            $subject = __("Formulaire de contact", "s1b");
            $copyMessage = sprintf(__("Merci d'avoir contacté %s.", "s1b"), \Sb\Entity\Constants::SITENAME)
                    . "<br/>" . __("Nous nous efforçons de vous répondre au plus vite.", "s1b")
                    . "<br/>" . sprintf(__("L'équipe %s", "s1b"), \Sb\Entity\Constants::SITENAME)
                    . "<br/><br/>" . $body;
            $mailSvc->send($email, $subject, $copyMessage);
        }

        $emailSent = true;
    } else {
        \Sb\Flash\Flash::addItem(__("Le message n'a pas pu être envoyé.", "s1b"));
    }
    
    if ($emailSent) {
        \Sb\Flash\Flash::addItem(__("Merci.","s1b"));
        \Sb\Flash\Flash::addItem(__("Votre e-mail a été envoyé. Vous recevrez une réponse au plus vite.","s1b"));
        \Sb\Flash\Flash::addItem(sprintf(__("L'equipe %s","s1b"), \Sb\Entity\Constants::SITENAME));
    }
    
}
?>

<div id="content-wide">
    <div class="annexe-page">
        <div class="ap-left">
            <div class="inner-padding-10">
                <div class="ap-title"><?php _e("Contact","s1b");?></div>
                <div class="ap-description"><?php _e("Remplissez le formulaire et nous vous répondrons dans les meilleurs délais","s1b");?></div>
            </div>        
        </div>
        <div class="ap-right">
            <div class="inner-padding-10">
                <form action="" method="post">
                    <div class="ap-line">
                        <div class="ap-label"><?php echo __("Nom *", "s1b"); ?></div>
                        <input type="text" class="textinput input-item" name="contactName" id="contactName" value="<?php echo $_POST['contactName']; ?>" class="requiredField" />
                    </div>
                    <div class="ap-line">
                        <div class="ap-label"><?php echo __("Prénom *", "s1b"); ?></div>
                        <input type="text" class="textinput input-item" name="contactFirstName" id="contactFirstName" value="<?php echo $_POST['contactFirstName']; ?>" class="requiredField" />
                    </div>
                    <div class="ap-line">
                        <div class="ap-label"><?php echo __("Email *", "s1b"); ?></div>
                        <input type="text" class="textinput input-item" name="email" id="email" value="<?php echo $_POST['email']; ?>" class="requiredField" />
                    </div>
                    <div class="ap-line">
                        <div class="ap-label"><?php echo __("Message *", "s1b"); ?></div>
                        <textarea class="input-item" name="comments" id="commentsText"><?php echo $_POST['comments']; ?></textarea>                    
                    </div>
                    <div class="buttons-bar">
                        <div class="inner-padding">
                            <?php _e("*champs obligatoires", "s1b"); ?>                                
                            <input type="checkbox" name="sendCopy" id="sendCopy" value="true"<?php if (isset($_POST['sendCopy']) && $_POST['sendCopy'] == true) echo ' checked="checked"'; ?> />
                            <?php echo __("Recevoir une copie du message", "s1b"); ?>
                            <button class="float-right button bt-black-m margin-right margin-left"><?php _e("Valider","s1b");?></button>
                            <?php if (!$_POST) {?>
                            <a class="button bt-blue-xs float-right" href="javascript:history.back()" class="link"><?php _e("Annuler", "s1b") ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>        
    </div>
</div>
<?php get_footer(); ?>