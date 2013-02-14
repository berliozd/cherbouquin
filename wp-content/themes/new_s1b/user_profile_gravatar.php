<?php
require_once 'includes/init.php';
get_header();
require_once 'user_profile_gravatar_1.php';

/**
 * Template Name: user_profile_gravatar
 */
?>
<div class="user-profile-bkg">
    <div id="content-center">
        <?php
        $profileView =new \Sb\View\UserProfile($user, $userSettings, false, false, false);
        echo $profileView->get();
        ?>
        <div class="horizontal-sep-1"></div>
        <div class="profile-edit-form">
            <div class="pef-line">
                <?php
                echo
                __("Votre photo provient par défaut du site gravatar.com ou de votre profil Facebook.
                    Si vous n'êtes pas inscrit via Facebook, Gravatar vous propose d'associer une adresse email
                    à un ou plusieurs gravatars.", "s1b") . "<br/>" .
                __("Créer votre gravatar est très simple et rapide, pour cela rendez-vous sur", "s1b")
                . " " . '<a href="http://gravatar.com">www.gravatar.com</a>' . "<br/>" .
                __("1/ créez votre gravatar", "s1b") . "<br/>" .
                __("2/ et voilà c'est terminé", "s1b");
                ?>
            </div>
            <div class="pef-line">
                <?php _e("Ou choisissez l'un des gravatars suivants : ", "s1b"); ?>
            </div>
            <div class="pef-gravatar-choice">                    
            <form action="" method="post">
                <div class="pef-gravatar">
                    <input type="radio" name="gravatar" value="http://www.gravatar.com/avatar/<?php echo md5(strtolower(trim($user->getEmail()))); ?>" />
                    <label><img class="image-frame" src="http://www.gravatar.com/avatar/<?php echo md5(strtolower(trim($user->getEmail()))); ?>" /></label>
                </div>
                
                <?php if ($user->getPictureBig() != "") {?>
                <div class="pef-gravatar">
                    <input type="radio" name="gravatar" value="<?php echo $user->getPictureBig(); ?>" />
                    <label><img class="image-frame" src="<?php echo $user->getPictureBig(); ?>" /></label>
                </div>
                <?php } ?>

                <div class="pef-gravatar">
                    <input type="radio" name="gravatar" value="<?php echo $context->getBaseUrl(); ?>/Resources/images/avatars/avatar01.jpg" />
                    <label><img class="image-frame" src="<?php echo $context->getBaseUrl(); ?>/Resources/images/avatars/avatar01.jpg" /></label>
                </div>                
                
                <div class="pef-gravatar">
                    <input type="radio" name="gravatar" value="<?php echo $context->getBaseUrl(); ?>/Resources/images/avatars/avatar02.jpg" />
                    <label><img class="image-frame" src="<?php echo $context->getBaseUrl(); ?>/Resources/images/avatars/avatar02.jpg" /></label>
                </div>

                
                <div class="buttons-bar">                   
                    <div class="inner-padding">
                        <button class="float-right button bt-blue-m margin-right margin-left"><?php _e("Valider","s1b");?></button>
                        <?php if (!$_POST) {?>
                        <a class="button bt-black-xs float-right" href="javascript:history.back()" class="link"><?php _e("Annuler", "s1b") ?></a>
                        <?php } ?>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
    <div id="content-right">
        <div class="right-frame">
            <?php
            $ad = new \Sb\View\Components\Ad("","");
            echo $ad->get();
            ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>