<?php

namespace Sb\Helpers;

/**
 * @author Didier
 */
class EntityHelper {

    const DESC = "DESC";
    const ASC = "ASC";
    
    public static function compareBy($entity1, $entity2, $direction, $sortingFunction) {
        $val1 = strtoupper(call_user_func(array(&$entity1, $sortingFunction)));
        $val2 = strtoupper(call_user_func(array(&$entity2, $sortingFunction)));
        if ($val1 == $val2) {
            return 0;
        }
        if ($direction == self::ASC) {
            return ($val1 < $val2) ? -1 : 1;
        } else {
            return ($val1 > $val2) ? -1 : 1;
        }
    }

}