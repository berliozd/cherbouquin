<?php
/**
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
use \Sb\Helpers\HTTPHelper;
use \Sb\Entity\Urls;
?>

                    <!-- Début div footer-wrap -->
                    <div id="footer-wrap">
                        <div id="footer-top">
                                    
                            <div class="ft-item">
                                <div class="inner-padding-12">
                                    <h1 class="ft-item-title"><a class="link" href="<?php echo HTTPHelper::Link(Urls::TOPS_BOOKS);?>"><?php _e("<strong>Top</strong> des livres", "s1b");?></a></h1>
                                    <div class="ft-item-content">                                       
                                        <?php
                                        $topsBooks = \Sb\Db\Dao\BookDao::getInstance()->getListTops(5);
                                        $topsBooks = array_slice($topsBooks, 0, 5);
                                        echo "<ul>";
                                        foreach ($topsBooks as $topsBook) {
                                            echo "<li><h2><a title=\"" . $topsBook->getTitle() . " - ". $topsBook->getOrderableContributors() . "\" href=\"" . HTTPHelper::Link($topsBook->getLink()) . "\">" 
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
                                    <h1 class="ft-item-title"><a class="link" href="<?php echo HTTPHelper::Link(Urls::BLOW_OF_HEARTS_BOOKS);?>"><strong><?php _e("Coups de coeur", "s1b");?></strong></a></h1>
                                    <div class="ft-item-content">                                        
                                        <?php
                                        $bohs = \Sb\Db\Dao\BookDao::getInstance()->getListBOH(5);
                                        $bohs = array_slice($bohs, 0, 5);
                                        echo "<ul>";
                                        foreach ($bohs as $boh) {
                                            echo "<li><h2><a title=\"" . $boh->getTitle() . " - ". $boh->getOrderableContributors() . "\" href=\"" . HTTPHelper::Link($boh->getLink()) . "\">" 
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
                                    <h1 class="ft-item-title"><a class="link" href="<?php echo HTTPHelper::Link(Urls::LAST_ADDED_BOOKS);?>"><strong><?php _e("Derniers livres ajoutés", "s1b");?></strong></a></h1>
                                    <div class="ft-item-content">                                        
                                        <?php
                                        $lastlyAddedBooks = \Sb\Db\Dao\BookDao::getInstance()->getLastlyAddedBooks(5);
                                        echo "<ul>";
                                        foreach ($lastlyAddedBooks as $lastlyAddedBook) {
                                            echo "<li><h2><a title=\"" . $lastlyAddedBook->getTitle() . " - ". $lastlyAddedBook->getOrderableContributors() . "\" href=\"" . HTTPHelper::Link($lastlyAddedBook->getLink()) . "\">" 
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
                                    <h1 class="ft-item-title"><?php echo sprintf(__("<strong>%s</strong> sur les réseaux", "s1b"), \Sb\Entity\Constants::SITENAME);?></h1>
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
                                            <a class="link" href="<?php echo HTTPHelper::Link(Urls::ABOUT);?>"><?php _e("A propos", "s1b");?></a>
                                        </li>                                    
                                        <li>
                                            <a class="link" href="<?php echo HTTPHelper::Link(Urls::TEAM);?>"><?php _e("L'équipe", "s1b");?></a>
                                        </li>
                                        <li>
                                            <a class="link" href="<?php echo HTTPHelper::Link(Urls::CONTACT);?>"><?php _e("Contact", "s1b");?></a>
                                        </li>
<!--                                        <li>
                                            <a class="link" href=""><?php //_e("Presse","s1b");?></a>
                                        </li>
                                        <li>
                                            <a class="link" href=""><?php //_e("Publicité","s1b");?></a>
                                        </li>-->
                                        <li>
                                            <a class="link" target="_blank" href="<?php echo HTTPHelper::Link("CGU/CGU-26-09-2012.pdf");?>"><?php _e("Conditions générales d'utilisations", "s1b");?></a>
                                        </li>
                                        <li>
                                            <a class="link" href="<?php echo HTTPHelper::Link(Urls::HOW_TO);?>"><?php _e("Comment ça marche?", "s1b");?></a>
                                        </li>
                                        <li>
                                            <a class="link" href="<?php echo HTTPHelper::Link(Urls::HELP_US);?>"><?php _e("Nous aider", "s1b");?></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Fin div footer-wrap -->
                </div>
                <!-- Fin div content-wrap -->    
            </div>
            <!-- Fin div content -->            
        </div>
    </body>
</html>
