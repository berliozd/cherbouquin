<?php
$noAuthentification = true;
require_once 'includes/init.php';
get_header();
require_once 'user_login_1.php';
\Sb\Trace\Trace::addItem("test");

/**
 * Template Name: user_login
 */
?>

<div id="content-top">
    <script type="text/javascript" src="<?php echo $context->getBaseUrl() . "Resources/js/simple.carousel.js"; ?>"></script>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            // example 2
            $("ul.carousel-items").simplecarousel({
                width:980,
                height:340,
                auto: 8000,
                fade: 200,
                pagination: true
            });
        });
    </script>
    <div class="carousel-wrap">
        <ul class="carousel-items">
            <li>
                <div class="accroche accroche-1">
                    <div class="ci-content">
                        <div class="ci-text"><span class="ci-maintext">Trouvez&nbsp;</span></br>le livre qui fera plaisir à votre ami dans sa liste d'envies</div>
                        <div class="ci-button"><a class="bt-carroussel button" href="<?php echo \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::SUBSCRIBE); ?>">S'inscrire</a></div>
                    </div>
                </div>
            </li>
            <li>
                <div class="accroche accroche-2">
                    <div class="ci-content">
                        <div class="ci-text"><span class="ci-maintext">Découvrez&nbsp;</span></br>Les derniers coups de coeur de vos amis</div>
                        <div class="ci-button"><a class="bt-carroussel button" href="<?php echo \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::SUBSCRIBE); ?>">S'inscrire</a></div>
                    </div>
                </div>

            </li>
            <li>
                <div class="accroche accroche-3">
                    <div class="ci-content">
                        <div class="ci-text"><span class="ci-maintext">Gérez&nbsp;</span></br>votre bibliothèque avec la fonction de prêt et emprunt et conservez une trace de vos livres préférés</div>
                        <div class="ci-button"><a class="bt-carroussel button" href="<?php echo \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::SUBSCRIBE); ?>">S'inscrire</a></div>
                    </div>
                </div>

            </li>
            <!--                <li>
                                <div class="accroche accroche-4">
                                    <div class="ci-content">
                                     <div class="ci-text"><span class="ci-maintext">Jeu</span></br>Recevez un livre d'un de nos éditeurs partenaires, écrivez sa critique et attribuez lui une note.</br><strong>A partir du 18/09/2012.</strong></div>
                                    <div class="ci-button"><a class="bt-carroussel button" href="<?php //echo \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::FRIEND_PROFILE);  ?>">Jouer</a></div>
                                </div>
                                </div>
                            </li>-->
        </ul>
    </div>
</div>
<div id="content-center">
    <div class="pushed-books pushedBooks">
        <h1 class="pb-title">
            <?php _e("<span class=\"pb-highlight\">Coups de coeur</span> des lecteurs", "s1b"); ?>
        </h1>
        <div class="pb-content">
            <?php
            $bohBooks = \Sb\Db\Dao\BookDao::getInstance()->getListBOH();
            if (count($bohBooks) == 0) {
                $noBohBooks = new \Sb\View\Components\NoBooksWidget(__("Aucun livre n'a encore été noté par les membres", "s1b"));
                echo $noBohBooks->get();
            } else {
                $bohBooksView = new \Sb\View\PushedBooks($bohBooks, 3, true);
                echo $bohBooksView->get();
            }
            ?>
        </div>
    </div>
</div>
<div id="content-right">
    <div class="right-frame">
        <?php
        $facebookFrame = new \Sb\View\Components\FacebookFrame();
        echo $facebookFrame->get();
        ?>
    </div>
    <div class="right-frame">
        <!--<a href=""><img src="<?php //echo $context->getBaseUrl() . "Resources/images/ads/ad-one.jpg"; ?>" border="0"/></a>-->
        <?php
        $ad = new \Sb\View\Components\Ad("user_login", "0457389056");
        echo $ad->get();
        ?>
    </div>
</div>
<?php get_footer(); ?>