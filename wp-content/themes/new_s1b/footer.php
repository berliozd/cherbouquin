<?php
/**
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
global $context;
?>
                    <div id="footer-wrap">
                        <div id="footer-top">
                                    
                            <div class="ft-item">
                                <div class="inner-padding-12">
                                    <h1 class="ft-item-title"><?php _e("<strong>Top</strong> des livres","s1b");?></h1>
                                    <div class="ft-item-content">
                                        <?php
                                        $topsBooks = \Sb\Db\Dao\BookDao::getInstance()->getListTops();
                                        $topsBooks = array_slice($topsBooks, 0, 5);
                                        echo "<ul>";
                                        foreach ($topsBooks as $topsBook) {
                                            echo "<li><h2><a title=\"" . $topsBook->getTitle() . " - ". $topsBook->getOrderableContributors() . "\" href=\"" . \Sb\Helpers\HTTPHelper::Link($topsBook->getLink()) . "\">" 
                                                    . \Sb\Helpers\StringHelper::tronque($topsBook->getTitle(), 40) 
                                                    . "</a></h2></li>";
                                        }
                                        echo "</ul>";
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="ft-item">
                                <div class="inner-padding-12">
                                    <h1 class="ft-item-title"><?php _e("<strong>Coups de coeur</strong>","s1b");?></h1>
                                    <div class="ft-item-content">
                                        <?php
                                        $bohs = \Sb\Db\Dao\BookDao::getInstance()->getListBOH();
                                        $bohs = array_slice($bohs, 0, 5);
                                        echo "<ul>";
                                        foreach ($bohs as $boh) {
                                            echo "<li><h2><a title=\"" . $boh->getTitle() . " - ". $boh->getOrderableContributors() . "\" href=\"" . \Sb\Helpers\HTTPHelper::Link($boh->getLink()) . "\">" 
                                                    . \Sb\Helpers\StringHelper::tronque($boh->getTitle(), 40) 
                                                    . "</a></h2></li>";
                                        }
                                        echo "</ul>";
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="ft-item">
                                <div class="inner-padding-12">
                                    <h1 class="ft-item-title"><?php _e("<strong>Derniers livres ajoutés</strong>","s1b");?></h1>
                                    <div class="ft-item-content">
                                         <?php
                                        $lastlyAddedBooks = \Sb\Db\Dao\BookDao::getInstance()->getLastlyAddedBooks();
                                        echo "<ul>";
                                        foreach ($lastlyAddedBooks as $lastlyAddedBook) {
                                            echo "<li><h2><a title=\"" . $lastlyAddedBook->getTitle() . " - ". $lastlyAddedBook->getOrderableContributors() . "\" href=\"" . \Sb\Helpers\HTTPHelper::Link($lastlyAddedBook->getLink()) . "\">" 
                                                    . \Sb\Helpers\StringHelper::tronque($lastlyAddedBook->getTitle(), 40) 
                                                    . "</a></h2></li>";
                                        }
                                        echo "</ul>";
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="ft-item last">
                                <div class="inner-padding-12">
                                    <h1 class="ft-item-title"><?php echo sprintf(__("<strong>%s</strong> sur les réseaux","s1b"), \Sb\Entity\Constants::SITENAME);?></h1>
                                    <div class="ft-item-content"><a target="_blank" href="http://www.facebook.com/CherBouquin" class="ft-facebook"></a></div>
                                    <div class="ft-item-content"><a target="_blank" href="https://twitter.com/#!/cherbouquin" class="ft-twitter"></a></div>
                                    <div class="ft-item-content"><a target="_blank" href="http://pinterest.com/cherbouquin" class="ft-pinterest"></a></div>
                                </div>
                            </div>
                        </div>
                        <div id="footer-bottom">
                            <div class="inner-padding">
                                <div class="fb-left float-left">&copy; 2012 <?php echo \Sb\Entity\Constants::SITENAME;?></div>
                                <div class="fb-right float-right">
                                    <ul>
                                        <li>
                                            <a class="link" href="<?php echo \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::ABOUT);?>"><?php _e("A propos","s1b");?></a>
                                        </li>                                    
                                        <li>
                                            <a class="link" href="<?php echo \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::TEAM);?>"><?php _e("L'équipe","s1b");?></a>
                                        </li>
                                        <li>
                                            <a class="link" href="<?php echo \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::CONTACT);?>"><?php _e("Contact","s1b");?></a>
                                        </li>
<!--                                        <li>
                                            <a class="link" href=""><?php //_e("Presse","s1b");?></a>
                                        </li>
                                        <li>
                                            <a class="link" href=""><?php //_e("Publicité","s1b");?></a>
                                        </li>-->
                                        <li>
                                            <a class="link" target="_blank" href="<?php echo \Sb\Helpers\HTTPHelper::Link("CGU/CGU-26-09-2012.pdf");?>"><?php _e("Conditions générales d'utilisations","s1b");?></a>
                                        </li>
                                        <li>
                                            <a class="link" href="<?php echo \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::HOW_TO);?>"><?php _e("Comment ça marche?","s1b");?></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
