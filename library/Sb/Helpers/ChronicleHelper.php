<?php

namespace Sb\Helpers;

use Sb\Entity\ChronicleType;
use Sb\Entity\ChronicleLinkType;

/**
 *
 * @author Didier
 *
 */
class ChronicleHelper {

    /**
     * Get a chronicle type label
     *
     * @param int $typeId
     * a chronicle type id
     * @return string return a label for the type id received
     */
    public static function getTypeLabel($typeId) {

        $typeLabel = "";
        switch ($typeId) {
            case ChronicleType::BOOK_CHRONICLE :
                $typeLabel = __("Chronique d'un livre", "s1b");
                break;
            case ChronicleType::DISCOVERY :
                $typeLabel = __("Découverte", "s1b");
                break;
            case ChronicleType::FREE :
                $typeLabel = __("Libre", "s1b");
                break;
            case ChronicleType::GAME :
                $typeLabel = __("Jeux", "s1b");
                break;
            case ChronicleType::JUST_FOR_FUN :
                $typeLabel = __("Juste pour le fun", "s1b");
                break;
            case ChronicleType::NEWS :
                $typeLabel = __("Actualités", "s1b");
                break;
            case ChronicleType::NEWSLETTER :
                $typeLabel = __("Newsletter", "s1b");
                break;
            case ChronicleType::TOPS :
                $typeLabel = __("Les tops", "s1b");
                break;
            case ChronicleType::WEDNESDAY_COMIC :
                $typeLabel = __("BD du mercredi", "s1b");
                break;
            default :
                $typeLabel = __("Autres", "s1b");
                break;
        }
        
        return $typeLabel;
    
    }

    /**
     * Get e chronicle link type label
     * @param int $linkTypeId a chronicle link type id
     * @return string a label for the specified chronicle link type
     */
    public static function getLinkTypeLabel($linkTypeId) {

        $linkTypeLabel = "";
        switch ($linkTypeId) {
            case ChronicleLinkType::COMIC_BOARD :
                $linkTypeLabel = __("Planche de BD", "s1b");
                break;
            case ChronicleLinkType::IMAGE :
                $linkTypeLabel = __("Photo", "s1b");
                break;
            case ChronicleLinkType::OTHER :
                $linkTypeLabel = __("Autres", "s1b");
                break;
            case ChronicleLinkType::PODCAST :
                $linkTypeLabel = __("Podcast", "s1b");
                break;
            case ChronicleLinkType::PRESS :
                $linkTypeLabel = __("Article de presse", "s1b");
                break;
            case ChronicleLinkType::URL :
                $linkTypeLabel = __("Lien", "s1b");
                break;
            case ChronicleLinkType::VIDEO :
                $linkTypeLabel = __("Vidéos", "s1b");
                break;
            default :
                ;
                break;
        }
        
        return $linkTypeLabel;
    
    }
    
    
    /**
     * Get only different chronicles than the one passed as first parameter
     * @param Chronicle $chronicle chronicle to remove from collection
     * @param Array $chronicles the collection of current chronicle to parse
     * @param int $maxNumber number of chronicles to return
     * @return a Collection of Chronicle that doesn't contain the one received as first parameter
     */
    public static function getDifferentChronicles($chronicle, $chronicles, $maxNumber) {
    
        $result = array();
    
        foreach ($chronicles as $chronicleToParse) {
            /* @$chronicle Chronicle */
            if ($chronicleToParse->getId() != $chronicle->getId()) {
                $result[] = $chronicleToParse;
                if (count($result) >= $maxNumber) {
                    return $result;
                }
            }
        }
    
        return $result;
    }

}
