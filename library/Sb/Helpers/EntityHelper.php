<?php

namespace Sb\Helpers;

/**
 *
 * @author Didier
 */
class EntityHelper {

    const DESC = "DESC";

    const ASC = "ASC";

    public static function compareBy($entity1, $entity2, $direction, $sortingFunction) {

        $val1 = strtoupper(call_user_func(array(
                &$entity1,
                $sortingFunction
        )));
        $val2 = strtoupper(call_user_func(array(
                &$entity2,
                $sortingFunction
        )));
        if ($val1 == $val2) {
            return 0;
        }
        if ($direction == self::ASC) {
            return ($val1 < $val2) ? -1 : 1;
        } else {
            return ($val1 > $val2) ? -1 : 1;
        }
    }

    /**
     * Merge array of second entities in first array only if not already existing
     * @param array of Model $firstEntities
     * @param array of Model $secondEntities
     * @return array of Model
     */
    public static function mergeEntities($firstEntities, $secondEntities) {

        foreach ($secondEntities as $secondEntitie) {
            
            $add = true;
            foreach ($firstEntities as $firstEntitie) {
                if ($firstEntitie->getId() == $secondEntitie->getId()) {
                    $add = false;
                    break;
                }
            }
            if ($add)
                $firstEntities[] = $secondEntitie;
        }
        return $firstEntities;
    }

}