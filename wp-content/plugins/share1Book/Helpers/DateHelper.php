<?php

namespace Sb\Helpers;

/**
 * @author Didier
 */
class DateHelper {

    /**
     * Returns a date that can be passed to mmysql
     * @param DateTime $date
     * @return string formatted for db 'Y-m-d H:i:s'
     */
    public static function getDateForDB($date) {
//        var_dump("getDateForDB");
//        var_dump($date);
        return $date->format('Y-m-d H:i:s');
        if (!($date instanceof \DateTime)) {
            throw new \Exception('Date passed is null.');
        } else {
            return $date->format('Y-m-d H:i:s');
        }
    }

    /**
     *
     * @param string $input 'Y-m-d H:i:s'
     * @return datetime
     */
    public static function createDateTime($input) {
//        var_dump($input);
        $arr = (explode(" ", $input));

        $strDate = "";
        $strTime = "";

        if (is_array($arr) && (count($arr) > 1)) {
            $strDate = $arr[0];
            $strTime = $arr[1];
        }

        $arrDate = explode("-", $strDate);
        if (!$arrDate)
            return;
        if (count($arrDate) < 3)
            return;
        $arrTime = explode(":", $strTime);
        if (!$arrTime)
            return;
        if (count($arrTime) < 3)
            return;

        $dt = new \DateTime;
        $dt->setTime($arrTime[0], $arrTime[1], $arrTime[2]);
        $dt->setDate($arrDate[0], $arrDate[1], $arrDate[2]);

        return $dt;
    }

    /**
     *
     * @param string $input 'Y-m-d'
     * @return datetime
     */
    public static function createDate($input) {

        $arrDate = explode("-", $input);
        if (!$arrDate)
            return;
        if (count($arrDate) < 3)
            return;

        $dt = new \DateTime;
        $dt->setDate($arrDate[0], $arrDate[1], $arrDate[2]);
        $dt->setTime(0, 0, 0);
        return $dt;
    }

     /**
     *
     * @param string $input 'd/m/Y'
     * @return datetime
     */
    public static function createDateBis($input) {

        $arrDate = explode("/", $input);
        if (!$arrDate)
            return;
        if (count($arrDate) < 3)
            return;

        $dt = new \DateTime;
        $dt->setDate($arrDate[2], $arrDate[1], $arrDate[0]);

        return $dt;
    }


}

?>
