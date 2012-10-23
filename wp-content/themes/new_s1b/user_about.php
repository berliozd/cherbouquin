<?php
$noAuthentification = true;
require_once 'includes/init.php';
get_header();


/**
 * Template Name: user_about
 */
?>
<div id="content-wrap">
    <div id="content-wide">
        <div class="annexe-page">
            <div class="ap-left">
                <div class="inner-padding-10">
                    <div class="ap-title"><?php _e("A propos", "s1b"); ?></div>
                    <div class="ap-description"><?php echo sprintf(__("%s vous propose de gérer votre bibliothèque, de la partager avec vos amis, de découvrir leurs derniers coups de coeur et de trouver le livre qui leur fera plaisir dans leur liste d'envies.","share1book"), \Sb\Entity\Constants::SITENAME);?></div>
                </div>        
            </div>
            <div class="ap-right">
                <div class="inner-padding-10">
                    <div class="ap-line">
                        <div class="apl-title"><?php _e("Le projet","s1b");?></div>
                        <?php
                        echo
                        __("''Trop de livres égarés sans que je ne sache qui me les a empruntés !!!''", "s1b")
                        . '<br/>' . __("''A qui est-ce qu'on l'a emprunté ce livre déjà ?''", "s1b")
                        . '<br/>' . __("''Jamais d'idées quand je cherche un bon livre !!!''", "s1b")
                        . '<br/>' . __("''Où est-ce que je me suis arrêté dans la série ???'' ", "s1b")
                        . '<br/>' . __("''Plutôt ne pas acheter que d'acheter en double !!!'' ", "s1b")
                        . '<br/>' . __("''Qu'est-ce que je pourrais offrir à mes amis qui leur ferait plaisir ???'' ", "s1b")
                        . '<br/><br/>' . __("Autant de questions, réflexions qui m'ont amenées à chercher les outils disponibles sur Internet pour répondre à mes besoins. Malheureusement malgré de nombreuses recherches je n'ai pas trouvé mon bonheur. Alors plutôt que de laisser tomber je me suis lancé dans la conception de Cher bouquin.", "s1b")
                        . '<br/><br/>' . __("Mon objectif: proposer un site convivial, simple d'utilisation et communautaire où les lecteurs peuvent gérer leur bibliothèque de livres, partager leurs lectures, échanger des bouquins, recommander ou trouver un conseil sur un livre, noter , commenter et critiquer une œuvre, trouver un cadeau pour un ami et plus encore.", "s1b")
                        . '<br/><br/>' . __("Alors bonne lecture et n'oubliez pas ...", "s1b") . '<br/>';
                        ?>  
                        <span style="font-style: italic">
                            <?php
                            echo
                            __("«On lit seul. Mais parler de ses lectures, échanger avec des amis ou des inconnus sur ses émotions littéraires est une activité aussi ancienne que la lecture elle-même. Loin de lui porter ombrage, Internet ne fait qu'enrichir la sociabilité qui se développe spontanément autour du plaisir de lire et lui ouvre de nouveaux territoires.»", "s1b")
                            ;
                            ?>
                        </span>

                        <span style="font-family: roman">
                            <?php
                            echo
                            '<br/>' . "(" . __("Jean-Marc Leveratto, Mary Leontsini", "s1b");
                            ?>
                        </span>

                        <span style="font-style: italic">
                            <?php
                            echo
                            ", " . __("Internet et la sociabilité littéraire", "s1b");
                            ?>
                        </span>

                        <span style="font-family: roman">
                            <?php
                            echo
                            ", " . __("quatrième de couverture", "s1b")
                            . ", " . __("Bibliothèque publique d'information du Centre Pompidou", "s1b")
                            . ", " . __("février 2008", "s1b")
                            . ", " . __("ISBN 9782842461119", "s1b")
                            . ")";
                            ?>
                        </span>
                    </div>
                    <div class="ap-line">
                        <div class="apl-title">
                            <?php echo __("Mentions légales", "s1b"); ?>
                        </div>
                        <div class="">
                            <?php
                            echo
                            '<b><span class="highlight">' . __("O.R.D.B", "s1b") . '</span></b>'
                            . '<br/><span class="apl-label">' . __("Forme sociale : ", "s1b") . "</span>" . __("Société par actions simplifiée", "s1b") 
                            . '<br/><span class="apl-label">' . __("Capital social : ", "s1b") . "</span>" . __("1 000 euros", "s1b")
                            . '<br/><span class="apl-label">' . __("RCS : ", "s1b") . "</span>" . __("Paris – 752 991 604", "s1b")
                            . '<br/><span class="apl-label">' . __("Siège social : ", "s1b") . "</span>" . __("29 rue de Trévise, 75009 Paris", "s1b")
                            . '<br/><span class="apl-label">' . __("Président : ", "s1b") . "</span>" . __("Olivier Rebiffé", "s1b")
                            . '<br/><span class="apl-label">' . __("Hébergeur : ", "s1b") . "</span>" . __("OVH - 140 Quai du Sartel - 59100 Roubaix - France", "s1b")
                            ?>
                        </div>
                    </div>
                </div>
            </div>        
        </div>
    </div>
    <?php get_footer(); ?>