<?php
namespace Sb\Db\Mapping;

/**
 *
 * @author Didier
 */
interface Mapper {
    public static function map(\Sb\Db\Model\Model &$model, array $properties, $prefix = "");
    public static function reverseMap(\Sb\Db\Model\Model $model, array &$properties);
}

?>
