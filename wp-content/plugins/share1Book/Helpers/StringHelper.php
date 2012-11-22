<?php

namespace Sb\Helpers;

/**
 * @author Didier
 */
class StringHelper {

    public static function tronque($chaine, $longueur) {
        if (empty($chaine)) {
            return "";
        } elseif (strlen($chaine) < $longueur) {
            return $chaine;
        } else {
            return mb_substr ( $chaine, 0, $longueur, "utf-8") . "...";            
        }
    }
    
    public static function isValidEmail($email) {
        //$regex = "^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$";
        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
        return preg_match($regex, $email);
    }
}