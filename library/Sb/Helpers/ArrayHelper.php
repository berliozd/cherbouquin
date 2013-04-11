<?php

namespace Sb\Helpers;

/**
 * @author Didier
 */
class ArrayHelper {

    public static function getSafeFromArray($array, $key, $def) {
        if ($array && (array_key_exists($key, $array))) {
            $tmp = $array[$key];
            if (is_string($tmp))
                $tmp = trim($tmp);
            if ($tmp != "")
                return $tmp;
        }
        return $def;
    }

}