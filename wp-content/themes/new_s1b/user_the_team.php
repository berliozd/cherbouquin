<?php
$noAuthentification = true;
require_once 'includes/init.php';
get_header();

$book1 = \Sb\Db\Dao\BookDao::getInstance()->get(225);
//$book1 = new \Sb\Db\Model\Book;
$book2 = \Sb\Db\Dao\BookDao::getInstance()->get(223);
//$book2 = new \Sb\Db\Model\Book;
/**
 * Template Name: user_the_team
 */
?>
<div id="content-wrap">
    <div id="content-wide">
        <div class="annexe-page">
            <div class="ap-left">
                <div class="inner-padding-10">
                    <div class="ap-title"><?php _e("L'équipe", "s1b"); ?></div>
                </div>        
            </div>
            <div class="ap-right">
                <div class="inner-padding-10">
                    <div class="ap-line team-member">
                        <div class="tm-image">
                            <img class="image-frame" src="<?php echo $context->getBaseUrl() . "Resources/images/team/olivier_100x100px.png"; ?>" />
                        </div>
                        <div class="tm-resume">
                            <div class="tm-name">Olivier Rebiffé</div>
                            <div class="tm-function">Président - Fondateur</div>
                            <div class="tm-librarylink"><a href="" class="link"><?php _e("Voir sa bibliothèque", "s1b");?></a></div>
                        </div>
                        <div class="tm-description">
                            <?php
                            echo
                            __("En pleine activité de lecture... <br/>Après quelques années passées au sein de grands groupes mais aussi de structures en devenir, j'ai décidé de sauter le pas et de porter mon propre projet.", "s1b")
                            . '<br/>' . __("Pourquoi un site Internet autour de la lecture me direz-vous ? Et bien outre le fait que j'ai passé mes 7 dernières années au sein de ''pure player'' Internet, je suis grand fan de romans policiers et j'ai également un pêché mignon pour la bande dessinée.", "s1b");
                            ?>
                        </div>                        
                    </div>
                    <div class="ap-line team-member">
                        <div class="tm-image">
                            <img class="image-frame" src="<?php echo $context->getBaseUrl() . "Resources/images/team/didier_100x100px.png"; ?>" width="100" height="100" />
                        </div>
                        <div class="tm-resume">
                            <div class="tm-name">Didier Berlioz</div>
                            <div class="tm-function">Fondateur</div>
                            <div class="tm-librarylink"><a href="" class="link"><?php _e("Voir sa bibliothèque", "s1b");?></a></div>                            
                            <?php //echo \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::FRIEND_LIBRARY, array("fid" => 14));?>
                        </div>
                        <div class="tm-description">
                           <!--Je travaille depuis plus de 10 ans dans le secteur des nouvelles technologies et plus précisément dans le développement de solution internet. Passionné par mon travail, je mets aujourd'hui à profit mes compétences et mon expérience dans ce projet auquel je crois. A l'ère des réseaux sociaux, l'ambition de ce dernier est de donner une dimension nouvelle au monde littéraire et permettre aux lecteurs de partager et d'échanger.-->
                           Papa de 2 enfants, je lis et relis <a href="<?php echo \Sb\Helpers\HTTPHelper::Link($book1->getLink());?>" class="link">Clara la grenouille à grande bouche</a> très souvent au point que ma fille la connaisse par coeur. Développeur de ce site, il m'arrive aussi de feuilleter <a href="<?php echo \Sb\Helpers\HTTPHelper::Link($book2->getLink());?>" class="link">La création de sites web expliquée à ma Grand-Mère</a>.
                           Mes lectures sont en fait très variées.
                           <br/>Sinon, je travaille depuis plus de 10 ans dans le secteur des nouvelles technologies. Passionné par mon travail, je mets aujourd'hui à profit mes compétences et mon expérience dans ce projet auquel je crois.
                        </div>                        
                    </div>
                </div>
            </div>        
        </div>
    </div>
    <?php get_footer(); ?>
