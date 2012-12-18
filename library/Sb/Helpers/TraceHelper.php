<?php

namespace Sb\Helpers;

/**
 * @author Didier
 */
class TraceHelper {

    public static function sqlInsertTrace($tablename, $params) {
        $keys = array_keys($params);
        $cols = implode(",", $keys);
        $values = array_values($params);
        $vals = implode("','", $values);
        $sql = "INSERT INTO $tablename ($cols) VALUES ('$vals')";
        return $sql;
    }

    public static function sqlUpdateTrace($tablename, $params, $keyName, $keyVal) {
        $keys = array_keys($params);
        $cols = implode(",", $keys);
        $values = array_values($params);
        $vals = implode("','", $values);
        $sql = "UPDATE $tablename set ($cols) VALUES ('$vals') WHERE $keyName = $keyVal";
        return $sql;
    }

}

?>
