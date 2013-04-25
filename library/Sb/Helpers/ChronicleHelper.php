<?php
namespace Sb\Helpers;

use Sb\Entity\ChronicleType;
/** 
 * @author Didier
 * 
 */
class ChronicleHelper {

	/**
	 * Get a chronicle type label
	 * @param int $typeId a chronicle type id
	 * @return string return a label for the type id received
	 */
    public static function getTypeLabel($typeId) {
        $typeLabel = "";
        switch ($typeId) {
        case ChronicleType::BOOK_CHRONICLE:
            $typeLabel = __("Chronique d'un livre", "s1b");
            break;
        case ChronicleType::DISCOVERY:
            $typeLabel = __("Découverte", "s1b");
            break;
        case ChronicleType::FREE:
            $typeLabel = __("Libre", "s1b");
            break;
        case ChronicleType::GAME:
            $typeLabel = __("Jeux", "s1b");
            break;
        case ChronicleType::JUST_FOR_FUN:
            $typeLabel = __("Juste pour le fun", "s1b");
            break;
        case ChronicleType::NEWS:
            $typeLabel = __("Actualités", "s1b");
            break;
        case ChronicleType::NEWSLETTER:
            $typeLabel = __("Newsletter", "s1b");
            break;
        case ChronicleType::TOPS:
            $typeLabel = __("Les tops", "s1b");
            break;
        case ChronicleType::WEDNESDAY_COMIC:
            $typeLabel = __("BD du mercredi", "s1b");
            break;
        default:
            $typeLabel = __("Autres", "s1b");
            break;
        }

        return $typeLabel;
    }
}
