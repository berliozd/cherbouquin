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
            return mb_substr($chaine, 0, $longueur, "utf-8") . "...";
        }
    }

    public static function isValidEmail($email) {
        //$regex = "^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$";
        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
        return preg_match($regex, $email);
    }

    public static function parseTweet($text) {
        $text = preg_replace('#http://[a-z0-9._/-]+#i', '<a  target="_blank" href="$0">$0</a>', $text); //Link
        $text = preg_replace('#@([a-z0-9_]+)#i', '@<a  target="_blank" href="http://twitter.com/$1">$1</a>', $text); //usernames
        $text = preg_replace('# \#([a-z0-9_-]+)#i', ' #<a target="_blank" href="http://search.twitter.com/search?q=%23$1">$1</a>', $text); //Hashtags
        $text = preg_replace('#https://[a-z0-9._/-]+#i', '<a  target="_blank" href="$0">$0</a>', $text); //Links
        return $text;
    }

    /** 
     * Return a string cleaned from HTML and double quote
     * @param String $string the string to clean from HTML
     * @param Boolean $removeDoubleQuote tell if double quote must removed
     * @return String the string cleaned from HTML
     */
    public static function cleanHTML($string, $removeDoubleQuote = true) {
        $res = strip_tags($string);
        if ($removeDoubleQuote)
            $res = str_replace("\"", "", $res);
        return $res;
    }
    
    public static function sanitize($text) {
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace("/[áàâãªä]/u", "a", $text);
        $text = preg_replace("/[íìîï]/u", "i", $text);
        $text = preg_replace("/[éèêë]/u", "e", $text);
        $text = preg_replace("/[óòôõºö]/u", "o", $text);
        $text = preg_replace("/[úùûü]/u", "u", $text);
        $text = preg_replace("/[ñ]/u", "n", $text);
        $text = str_replace("ç", "c", $text);
        $text = preg_replace("/[':;.()]/u", "-", $text);
        $text = preg_replace("/[\\W]/u", "-", $text);
        $text = preg_replace("/[-]$/u", "", $text);
        $text = preg_replace("/[-]+/u", "-", $text);
        return $text;
    }
}
